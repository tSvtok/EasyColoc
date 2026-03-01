<?php

namespace App\Services;

use App\Models\Colocation;

class BalanceService
{
    //Calculate the balance for each active member.
    //Balance = total paid by member - fair share of all expenses.
    //Positive = others owe them. Negative = they owe others.
    
    public function calculateBalances(Colocation $colocation): array
    {
        $members = $colocation->activeMembers()->get();
        $memberCount = $members->count();

        if ($memberCount === 0) {
            return [];
        }

        $expenses = $colocation->expenses;
        $totalExpenses = $expenses->sum('amount');
        $fairShare = $totalExpenses / $memberCount;

        // Calculate paid amounts and subtract payments already made
        $paidAmounts = [];
        foreach ($members as $member) {
            $paidAmounts[$member->id] = 0;
        }

        foreach ($expenses as $expense) {
            if (isset($paidAmounts[$expense->paid_by])) {
                $paidAmounts[$expense->paid_by] += (float) $expense->amount;
            }
        }

        // Factor in settled payments
        $paidSettlements = $colocation->settlements()->where('is_paid', true)->get();
        foreach ($paidSettlements as $settlement) {
            if (isset($paidAmounts[$settlement->from_user_id])) {
                $paidAmounts[$settlement->from_user_id] += (float) $settlement->amount;
            }
            if (isset($paidAmounts[$settlement->to_user_id])) {
                $paidAmounts[$settlement->to_user_id] -= (float) $settlement->amount;
            }
        }

        $balances = [];
        foreach ($members as $member) {
            $balances[$member->id] = round(($paidAmounts[$member->id] ?? 0) - $fairShare, 2);
        }

        return $balances;
    }

    public function calculateSettlements(Colocation $colocation): array
    {
        $balances = $this->calculateBalances($colocation);
        $members = $colocation->activeMembers()->get()->keyBy('id');

        $debtors = [];  // negative balance = they owe
        $creditors = []; // positive balance = they are owed

        foreach ($balances as $userId => $balance) {
            if ($balance < -0.01) {
                $debtors[] = ['user_id' => $userId, 'amount' => abs($balance)];
            } elseif ($balance > 0.01) {
                $creditors[] = ['user_id' => $userId, 'amount' => $balance];
            }
        }

        // Sort to optimize settlement count
        usort($debtors, fn ($a, $b) => $b['amount'] <=> $a['amount']);
        usort($creditors, fn ($a, $b) => $b['amount'] <=> $a['amount']);

        $settlements = [];
        $i = 0;
        $j = 0;

        while ($i < count($debtors) && $j < count($creditors)) {
            $amount = min($debtors[$i]['amount'], $creditors[$j]['amount']);

            if ($amount > 0.01) {
                $settlements[] = [
                    'from' => $members[$debtors[$i]['user_id']] ?? null,
                    'to' => $members[$creditors[$j]['user_id']] ?? null,
                    'amount' => round($amount, 2),
                ];
            }

            $debtors[$i]['amount'] -= $amount;
            $creditors[$j]['amount'] -= $amount;

            if ($debtors[$i]['amount'] < 0.01) {
                $i++;
            }
            if ($creditors[$j]['amount'] < 0.01) {
                $j++;
            }
        }

        return $settlements;
    }
}
