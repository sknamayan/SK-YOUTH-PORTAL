<?php

namespace App\Http\Controllers;

use App\Models\SkOfficial;
use App\Models\TransparencyPost;
use Illuminate\View\View;

class GovernanceController extends Controller
{
    public function officialsIndex(): View
    {
        $officials = SkOfficial::active()->ordered()->get();

        return view('officials.index', compact('officials'));
    }

    public function officialShow(string $slug): View
    {
        $official = SkOfficial::active()->where('slug', $slug)->firstOrFail();
        $otherOfficials = SkOfficial::active()
            ->where('id', '!=', $official->id)
            ->ordered()
            ->limit(4)
            ->get();

        return view('officials.show', compact('official', 'otherOfficials'));
    }

    public function transparencyIndex(): View
    {
        $category = request('category');
        $query = TransparencyPost::active()->published()->latest('published_at');

        if ($category && array_key_exists($category, TransparencyPost::CATEGORIES)) {
            $query->where('category', $category);
        }

        $posts = $query->paginate(12)->withQueryString();
        $categories = TransparencyPost::CATEGORIES;

        return view('transparency.index', compact('posts', 'categories', 'category'));
    }

    public function transparencyShow(string $slug): View
    {
        $post = TransparencyPost::active()->published()->where('slug', $slug)->firstOrFail();
        $related = TransparencyPost::active()
            ->published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('transparency.show', compact('post', 'related'));
    }
}
