<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('order-tracking.index')->with([
            'metaTitle' => 'Sipariş Takip',
            'metaDescription' => 'Rose Garden sipariş takibi.',
        ]);
    }

    public function track(Request $request)
    {
        $validated = $request->validate(
            [
                'order_number' => ['required', 'string', 'max:50'],
            ],
            [],
            [
                'order_number' => __('sipariş numarası'),
            ]
        );

        $order = Order::where('order_number', $validated['order_number'])
            ->with('statusHistory')
            ->first();

        return view('order-tracking.index', compact('order'))->with([
            'metaTitle' => 'Sipariş Takip',
            'metaDescription' => 'Rose Garden sipariş durum sorgulama.',
        ]);
    }
}
