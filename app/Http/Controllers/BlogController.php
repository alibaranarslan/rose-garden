<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\StorefrontImage;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'))->with([
            'metaTitle' => __('Rose Garden Blog'),
            'metaDescription' => __('Çiçek bakımı ve hediye önerileri.'),
        ]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['category', 'author', 'products.images'])
            ->firstOrFail();

        $post->increment('view_count');

        $relatedProducts = $post->products()
            ->storefrontReady()
            ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
            ->take(4)
            ->get();

        return view('blog.show', compact('post', 'relatedProducts'))->with([
            'metaTitle' => $post->meta_title ?: $post->title,
            'metaDescription' => $post->meta_description ?: $post->excerpt,
            'ogImage' => StorefrontImage::resolveBlog(
                $post->featured_image,
                $post->slug,
                $post->title,
                $post->category?->name,
            ),
            'ogType' => 'article',
        ]);
    }
}
