<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransparencyPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransparencyPostController extends Controller
{
    public function index(): View
    {
        $posts = TransparencyPost::latest('published_at')->latest()->paginate(15);
        $categories = TransparencyPost::CATEGORIES;

        return view('admin.transparency.index', compact('posts', 'categories'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.transparency.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image_path'] = $request->file('image')?->store('transparency', 'public');
        $data['file_path'] = $request->file('document')?->store('transparency', 'public');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['published_at'] = now();

        TransparencyPost::create($data);

        return redirect()->route('admin.transparency.index')
            ->with('success', 'Transparency post published successfully.');
    }

    public function edit(string $transparency): RedirectResponse
    {
        return redirect()->route('admin.transparency.index');
    }

    public function update(Request $request, string $transparency): RedirectResponse
    {
        $transparency = TransparencyPost::findOrFail($transparency);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            if ($transparency->image_path) {
                Storage::disk('public')->delete($transparency->image_path);
            }
            $data['image_path'] = $request->file('image')->store('transparency', 'public');
        }

        if ($request->hasFile('document')) {
            if ($transparency->file_path) {
                Storage::disk('public')->delete($transparency->file_path);
            }
            $data['file_path'] = $request->file('document')->store('transparency', 'public');
        }

        $transparency->update($data);

        return redirect()->route('admin.transparency.index')
            ->with('success', 'Transparency post updated successfully.');
    }

    public function destroy(string $transparency): RedirectResponse
    {
        $transparency = TransparencyPost::findOrFail($transparency);
        if ($transparency->image_path) {
            Storage::disk('public')->delete($transparency->image_path);
        }
        if ($transparency->file_path) {
            Storage::disk('public')->delete($transparency->file_path);
        }
        $transparency->delete();

        return redirect()->route('admin.transparency.index')
            ->with('success', 'Transparency post deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(array_keys(TransparencyPost::CATEGORIES))],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:8192'],
        ]);
    }
}
