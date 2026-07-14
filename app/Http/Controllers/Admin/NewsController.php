<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display a listing of the news articles.
     */
    public function index(): View
    {
        $articles = NewsArticle::latest()->paginate(10);
        return view('admin.news.index', compact('articles'));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.news.index');
    }

    /**
     * Store a newly created news article in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'read_time' => ['required', 'integer', 'min:1'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        $isFeatured = $request->boolean('is_featured');
        if ($isFeatured) {
            // Reset other featured articles
            NewsArticle::where('is_featured', true)->update(['is_featured' => false]);
        }

        NewsArticle::create([
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'read_time' => $request->input('read_time'),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'image_path' => $imagePath,
            'is_featured' => $isFeatured,
            'is_trending' => $request->boolean('is_trending'),
            'published_at' => now(), // Auto publish on creation
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article published successfully.');
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit($id): RedirectResponse
    {
        return redirect()->route('admin.news.index');
    }

    /**
     * Update the specified news article in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $news = NewsArticle::findOrFail($id);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'read_time' => ['required', 'integer', 'min:1'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
        ]);

        $isFeatured = $request->boolean('is_featured');
        if ($isFeatured) {
            // Reset other featured articles
            NewsArticle::where('id', '!=', $news->id)->where('is_featured', true)->update(['is_featured' => false]);
        }

        $data = [
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'read_time' => $request->input('read_time'),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'is_featured' => $isFeatured,
            'is_trending' => $request->boolean('is_trending'),
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $data['image_path'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $news = NewsArticle::findOrFail($id);

        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
