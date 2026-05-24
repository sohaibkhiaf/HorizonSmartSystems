<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Message;
use Illuminate\Http\Request;

// x

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'achievementsCount' => Achievement::count(),
            'publishedAchievementsCount' => Achievement::where('is_published', true)->count(),
            'waitingMessagesCount' => Message::where('is_seen', false)->count(),
            'seenMessagesCount' => Message::where('is_seen', true)->count(),
        ]);
    }
}
