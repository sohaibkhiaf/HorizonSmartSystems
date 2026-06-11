<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = auth()->user()->subscriptions()->orderBy('renewal_date')->get();
        return view('client.subscriptions.index', compact('subscriptions'));
    }
}
