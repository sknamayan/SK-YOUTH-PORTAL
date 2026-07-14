<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Partner;
use App\Models\CarouselSlide;
use App\Models\NewsArticle;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the public landing page.
     */
     public function index(Request $request)
     {
         $categories = [
             'education' => ['label' => 'EDUCATION'],
             'health' => ['label' => 'HEALTH'],
             'governance' => ['label' => 'GOVERNANCE'],
             'active-citizenship' => ['label' => 'ACTIVE CITIZENSHIP'],
             'social-inclusion' => ['label' => 'SOCIAL INCLUSION'],
             'peace-building' => ['label' => 'PEACE BUILDING'],
             'environment' => ['label' => 'ENVIRONMENT'],
             'youth-employment' => ['label' => 'YOUTH EMPLOYMENT & EMPOWERMENT'],
             'agriculture' => ['label' => 'AGRICULTURE'],
             'global-mobility' => ['label' => 'GLOBAL MOBILITY'],
         ];
 
         $announcements = Announcement::active()
             ->latest()
             ->limit(3)
             ->get();
 
         $partners = Partner::where('is_active', true)->latest()->get();
         $slides = CarouselSlide::orderBy('sort_order', 'asc')->get();
 
         if ($slides->isEmpty()) {
             $formattedSlides = [
                 [
                     'title' => 'Empowering Namayan Youth Leaders',
                     'desc' => 'Access local government programs, health consultations, library slots, and tournament registrations easily.',
                     'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Book a Consultation',
                     'url1' => route('forms.health.create')
                 ],
                 [
                     'title' => 'Silid Karunungan Studying Spaces',
                     'desc' => 'Reserve a study space at our local modern library facilities with free high-speed internet and research tools.',
                     'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Book Library Slot',
                     'url1' => route('forms.silid.create')
                 ],
                 [
                     'title' => 'Medicine Delivery Support Services',
                     'desc' => 'Apply digitally for the SK Pabili Medicine program to receive essential healthcare assistance directly to your home.',
                     'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Apply for Medicine',
                     'url1' => route('forms.medicine.create')
                 ],
                 [
                     'title' => 'SK Sports Leagues & Tournaments',
                     'desc' => 'Register teams or sign up individually for community basketball, volleyball, badminton, and esports leagues.',
                     'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1200&q=80',
                     'cta1' => 'Register for Sports',
                     'url1' => route('forms.sports.create')
                 ]
             ];
         } else {
             $formattedSlides = $slides->map(function($slide) {
                 return [
                     'title' => $slide->title,
                     'desc' => $slide->description,
                     'image' => asset('storage/' . $slide->image_path),
                     'cta1' => $slide->cta_text ?? 'Apply Now',
                     'url1' => $slide->cta_url ?? '#'
                 ];
             })->all();
         }

        // Load news articles for landing page (Featured & Recent only)
        $featuredArticle = NewsArticle::where('is_featured', true)->latest()->first();
        if (!$featuredArticle) {
            $featuredArticle = NewsArticle::latest()->first();
        }

        $featuredId = optional($featuredArticle)->id;

        $recentArticles = NewsArticle::when($featuredId, function($q) use ($featuredId) {
                return $q->where('id', '!=', $featuredId);
            })
            ->latest()
            ->limit(3)
            ->get();

        $initiatives = \App\Models\Initiative::whereNotNull('form_route')->get()->keyBy('form_route');
        $highlightedInitiatives = \App\Models\Initiative::where('is_highlighted', true)->get();

        return view('landing.index', compact(
            'categories', 
            'announcements', 
            'partners', 
            'formattedSlides',
            'featuredArticle',
            'recentArticles',
            'initiatives',
            'highlightedInitiatives'
        ));
    }

    /**
     * Display the public news listing page.
     */
    public function newsIndex(Request $request)
    {
        $trendingArticles = NewsArticle::where('is_trending', true)
            ->latest()
            ->limit(3)
            ->get();
 
        return view('news.index', compact('trendingArticles'));
    }

    /**
     * Display the detailed news article view.
     */
    public function showNews(Request $request, $slug)
    {
        $article = NewsArticle::where('slug', $slug)->firstOrFail();
 
        return view('news.show', compact('article'));
    }
}
