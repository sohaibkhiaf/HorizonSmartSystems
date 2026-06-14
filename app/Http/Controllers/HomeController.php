<?php

namespace App\Http\Controllers;

use App\Models\PublicAchievement;
use App\Models\PublicService;
use App\Models\PublicSoftware;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home () {
        // fetch
        $achievements = PublicAchievement::where('is_published', true)
            ->latest()
            ->limit(10)
            ->get();

        // fetch
        $services = PublicService::all();
        $software = PublicSoftware::all();

        // view
        return view('/home', [
            'achievements' => $achievements, 
            'services' => $services, 
            'software' => $software, 
        ]);
    }

    public function show(PublicService $service)
    {
        abort_if(! $service->is_published, 404);

        $otherServices = PublicService::where('is_published', true)
            ->where('id', '!=', $service->id)
            ->latest()
            ->take(4)
            ->get();

        $services = PublicService::all();

        return view('service', compact('service', 'services' , 'otherServices'));
    }
}
