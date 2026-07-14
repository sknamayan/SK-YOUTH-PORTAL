<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkOfficial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OfficialController extends Controller
{
    public function index(): View
    {
        $officials = SkOfficial::ordered()->paginate(15);

        return view('admin.officials.index', compact('officials'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.officials.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['photo_path'] = $request->file('photo')?->store('officials', 'public');
        $data['is_active'] = $request->boolean('is_active', true);

        SkOfficial::create($data);

        return redirect()->route('admin.officials.index')
            ->with('success', 'Official profile published successfully.');
    }

    public function edit(string $official): RedirectResponse
    {
        return redirect()->route('admin.officials.index');
    }

    public function update(Request $request, string $official): RedirectResponse
    {
        $official = SkOfficial::findOrFail($official);
        $data = $this->validated($request, $official->id);
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('photo')) {
            if ($official->photo_path) {
                Storage::disk('public')->delete($official->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('officials', 'public');
        }

        $official->update($data);

        return redirect()->route('admin.officials.index')
            ->with('success', 'Official profile updated successfully.');
    }

    public function destroy(string $official): RedirectResponse
    {
        $official = SkOfficial::findOrFail($official);
        if ($official->photo_path) {
            Storage::disk('public')->delete($official->photo_path);
        }
        $official->delete();

        return redirect()->route('admin.officials.index')
            ->with('success', 'Official profile removed successfully.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'photo' => [$id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'term' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
    }
}
