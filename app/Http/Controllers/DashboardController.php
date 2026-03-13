<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $stats = [
            'total_activities' => Activity::count(),
            'total_submissions' => \App\Models\Submission::count(),
            'activities_this_month' => Activity::whereMonth('created_at', now()->month)->count(),
        ];

        $recent_activities = Activity::withCount('submissions')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_activities'));
    }
}
