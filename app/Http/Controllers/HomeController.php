<?php

namespace App\Http\Controllers;

use App\Models\PublicAchievement;
use App\Models\PublicHero;
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
        $software = PublicSoftware::with([
            'demos' => function ($query) {
                $query->where('is_active', true)->latest();
            }
        ])
        ->where('is_published', true)
        ->get();
        $hero = PublicHero::findOrFail(1);

        // view
        return view('/home', [
            'achievements' => $achievements, 
            'services' => $services, 
            'software' => $software, 
            'hero' => $hero,
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
        $hero = PublicHero::findOrFail(1);

        return view('service', compact('service', 'services' , 'otherServices', 'hero'));
    }

    public function software(PublicSoftware $software)
    {
        abort_if(! $software->is_published, 404);

        $software->load([
            'demos' => function ($query) {
                $query->where('is_active', true)->latest();
            }
        ]);

        $otherSoftware = PublicSoftware::where('is_published', true)
            ->where('id', '!=', $software->id)
            ->latest()
            ->take(4)
            ->get();

        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        return view('software', compact(
            'software',
            'services',
            'otherSoftware',
            'hero'
        ));
    }
    
}
