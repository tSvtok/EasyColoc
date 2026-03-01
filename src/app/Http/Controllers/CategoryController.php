<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request, Colocation $colocation): RedirectResponse
    {
        if ((int) $colocation->owner_id !== (int) $request->user()->id) {
            abort(403);
        }

        $colocation->categories()->create([
            'name' => $request->validated('name'),
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Catégorie ajoutée.');
    }

    public function destroy(Request $request, Colocation $colocation, Category $category): RedirectResponse
    {
        if ((int) $colocation->owner_id !== (int) $request->user()->id) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Catégorie supprimée.');
    }
}
