<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = auth()->user()->services()->latest()->get();
        return view('client.services.index', compact('services'));
    }
}
