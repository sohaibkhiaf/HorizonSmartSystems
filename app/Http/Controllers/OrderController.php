<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->get();
        return view('client.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        return view('client.orders.show', compact('order'));
    }
}
