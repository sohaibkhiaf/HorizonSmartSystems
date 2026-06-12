<?php

namespace App\Http\Controllers;

use App\Models\PublicAchievement;
use App\Models\PublicMessage;
use Illuminate\Http\Request;

// x

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'achievementsCount' => PublicAchievement::count(),
            'publishedAchievementsCount' => PublicAchievement::where('is_published', true)->count(),
            'waitingMessagesCount' => PublicMessage::where('is_seen', false)->count(),
            'seenMessagesCount' => PublicMessage::where('is_seen', true)->count(),
        ]);
    }
}
