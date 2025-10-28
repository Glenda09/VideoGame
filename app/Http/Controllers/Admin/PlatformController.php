<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function index(): View
    {
        $platforms = Platform::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.platforms.index', [
            'platforms' => $platforms,
        ]);
    }

    public function create(): View
    {
        return view('admin.platforms.create', [
            'platform' => new Platform(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePlatform($request);

        Platform::query()->create($data);

        return redirect()
            ->route('admin.platforms.index')
            ->with('status', 'Plataforma creada correctamente.');
    }

    public function edit(Platform $platform): View
    {
        return view('admin.platforms.edit', [
            'platform' => $platform,
        ]);
    }

    public function update(Request $request, Platform $platform): RedirectResponse
    {
        $data = $this->validatePlatform($request, $platform);

        $platform->update($data);

        return redirect()
            ->route('admin.platforms.index')
            ->with('status', 'Plataforma actualizada correctamente.');
    }

    public function destroy(Platform $platform): RedirectResponse
    {
        if ($platform->products()->exists()) {
            return back()
                ->withErrors('No se puede eliminar una plataforma asociada a productos.')
                ->withInput();
        }

        $platform->delete();

        return redirect()
            ->route('admin.platforms.index')
            ->with('status', 'Plataforma eliminada.');
    }

    private function validatePlatform(Request $request, ?Platform $platform = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ];

        $validated = $request->validate($rules);

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? null,
        ];
    }
}
