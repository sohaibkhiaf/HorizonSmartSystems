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
}
