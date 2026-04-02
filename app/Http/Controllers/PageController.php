<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.show', compact('page'))->with([
            'metaTitle' => $page->meta_title ?: $page->title,
            'metaDescription' => $page->meta_description ?: str($page->content)->stripTags()->limit(160)->toString(),
        ]);
    }

    public function contact()
    {
        return view('pages.contact')->with([
            'metaTitle' => 'İletişim',
            'metaDescription' => 'Rose Garden ile iletişime geçin.',
        ]);
    }

    public function faq()
    {
        return view('pages.faq')->with([
            'metaTitle' => 'Sıkça Sorulan Sorular',
            'metaDescription' => 'Teslimat, ödeme ve sipariş süreci hakkında SSS.',
        ]);
    }

    public function deliveryInfo()
    {
        return view('pages.delivery-info')->with([
            'metaTitle' => 'Teslimat Bilgileri',
            'metaDescription' => 'Teslimat bölgesi ve saat aralığı bilgileri.',
        ]);
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', __('Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.'));
    }
}
