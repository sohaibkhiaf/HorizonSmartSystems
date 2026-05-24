<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Message;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'achievementsCount' => Achievement::count(),
            'publishedAchievementsCount' => Achievement::where('is_published', true)->count(),
            'waitingMessagesCount' => Message::where('seen', false)->count(),
            'seenMessagesCount' => Message::where('seen', true)->count(),
        ]);
    }
}
