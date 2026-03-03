<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->with('category')
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'))->with([
            'metaTitle' => 'Rose Garden Blog',
            'metaDescription' => 'Cicek bakimi ve hediye onerileri.',
        ]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['category', 'products.images'])
            ->firstOrFail();

        $post->increment('view_count');

        $relatedProducts = $post->products()
            ->active()
            ->with('images')
            ->take(4)
            ->get();

        return view('blog.show', compact('post', 'relatedProducts'))->with([
            'metaTitle' => $post->meta_title ?: $post->title,
            'metaDescription' => $post->meta_description ?: $post->excerpt,
            'ogImage' => $post->featured_image,
            'ogType' => 'article',
        ]);
    }
}
