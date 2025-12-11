<?php

namespace App\Http\Controllers;

use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisement;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //
    public function index()
    {
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(3)
            ->get();

        $featured_articles = ArticleNews::with(['category'])
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->take(3)
            ->get();

        $authors = Author::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            // ->take(1)
            ->first();

        // Mengambil artikel dari kategori "Entertainment" yang bukan featured
        $entertainment_articles = ArticleNews::whereHas('category', function ($query) {
                $query->where('name', 'Entertainment');
            })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        //Mengambil artikel dari kategori "Entertainment" yang featured
        $entertainment_featured_articles = ArticleNews::whereHas('category', function ($query) {
                $query->where('name', 'Entertainment');
            })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        //Mengaambil artikel dari kategori "Health" yang bukan featured
        $health_articles = ArticleNews::whereHas('category', function ($query) {
                $query->where('name', 'Health');
            })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        //Mengambil artikel dari kategori "Health" yang featured
        $health_featured_articles = ArticleNews::whereHas('category', function ($query) {
                $query->where('name', 'Health');
            })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        //Mengambil artikel dari kategori "Automotive" yang bukan featured
        $automotive_articles = ArticleNews::whereHas('category', function ($query) {
                $query->where('name', 'Automotive');
            })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        //Mengambil artikel dari kategori "Entertainment" yang featured
        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
                $query->where('name', 'Automotive');
            })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        return view('front.index', compact('categories', 'articles', 'authors', 'featured_articles', 'bannerads', 'entertainment_articles', 'entertainment_featured_articles', 'health_articles', 'health_featured_articles', 'automotive_articles', 'automotive_featured_articles'));
    }

    public function category(Category $category)
    {
        $categories = Category::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        $articles = ArticleNews::with(['category'])
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(6);

        return view('front.category', compact('categories', 'category', 'articles', 'bannerads'));
    }

    public function author(Author $author)
    {
        $categories = Category::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        $articles = ArticleNews::with(['category'])
            ->where('author_id', $author->id)
            ->latest()
            ->paginate(6);

        return view('front.author', compact('categories', 'author', 'articles', 'bannerads'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => ['required' , 'string' , 'max:255'],
        ]);

        $categories = Category::all();

        $keyword = $request->keyword;

        $articles = ArticleNews::with(['category', 'author'])
            ->where('name', 'like', '%' . $keyword . '%')
            ->paginate(6);

        return view('front.search', compact('categories', 'articles', 'keyword'));
    }

    public function details(ArticleNews $articleNews)
    {
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
            ->where('is_featured', 'not_featured')
            ->where('id', '!=', $articleNews->id)
            ->latest()
            ->take(3)
            ->get();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        $square_ads = BannerAdvertisement::where('type', 'square')
            ->where('is_active', 'active')
            ->inRandomOrder()
            ->take(2)
            ->get();

        if($square_ads->count() < 2){
            $square_ads_1 = $square_ads->first();
            $square_ads_2 = $square_ads->first();
        } else {
            $square_ads_1 = $square_ads->get(0);
            $square_ads_2 = $square_ads->get(1);
        }

        $author_news = ArticleNews::where('author_id', $articleNews->author_id)
            ->where('id', '!=', $articleNews->id)
            ->inRandomOrder()
            ->get();

        return view('front.details', compact('author_news','articleNews', 'categories', 'articles', 'bannerads', 'square_ads_1', 'square_ads_2'));
    }

}
