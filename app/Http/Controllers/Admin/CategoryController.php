<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(),
            'parents' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCategory($request);

        Category::query()->create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => Category::query()
                ->whereKeyNot($category->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateCategory($request, $category);

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()
                ->withErrors('No se puede eliminar una categoría que tiene subcategorías activas.')
                ->withInput();
        }

        if ($category->products()->exists()) {
            return back()
                ->withErrors('No se puede eliminar una categoría asociada a productos.')
                ->withInput();
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categoría eliminada.');
    }

    private function validateCategory(Request $request, ?Category $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        if ($category && isset($validated['parent_id']) && (int) $validated['parent_id'] === $category->id) {
            throw ValidationException::withMessages([
                'parent_id' => 'No puedes seleccionar la misma categoría como padre.',
            ]);
        }

        return [
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
        ];
    }
}
