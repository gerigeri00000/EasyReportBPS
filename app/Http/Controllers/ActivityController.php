<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::withCount('submissions')->latest()->paginate(10);
        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Transform partners textarea into array
        $partnersString = $request->input('partners', '');
        $partnersArray = $this->normalizePartners($partnersString);
        $request->merge(['partners' => $partnersArray]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'activity_date' => 'required|date',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'nama_pemeriksa' => 'required|string|max:255',
            'partners' => 'required|array|min:1',
            'partners.*' => 'string|max:255',
        ]);

        Activity::create($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $submissions = $activity->submissions()->with(['respondents'])->withCount('respondents')->latest()->get();
        return view('activities.show', compact('activity', 'submissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        return view('activities.edit', compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        // Transform partners textarea into array
        $partnersString = $request->input('partners', '');
        $partnersArray = $this->normalizePartners($partnersString);
        $request->merge(['partners' => $partnersArray]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'activity_date' => 'required|date',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'nama_pemeriksa' => 'required|string|max:255',
            'partners' => 'required|array|min:1',
            'partners.*' => 'string|max:255',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Normalize partners input string into an array.
     *
     * @param  string  $partnersString
     * @return array
     */
    private function normalizePartners($partnersString)
    {
        $partners = explode("\n", $partnersString);
        $partners = array_map('trim', $partners);
        $partners = array_filter($partners, function ($value) {
            return $value !== '' && $value !== null;
        });
        $partners = array_unique($partners);
        return array_values($partners);
    }

    /**
     * Remove the specified resource from storage.
     * Also deletes all associated submission photos from storage.
     */
    public function destroy(Activity $activity)
    {
        // Load all submissions with respondents to delete photos
        $activity->load(['submissions.respondents']);

        // Delete all respondents' photos from storage
        foreach ($activity->submissions as $submission) {
            foreach ($submission->respondents as $respondent) {
                if ($respondent->photo_path) {
                    Storage::disk('public')->delete($respondent->photo_path);
                }
            }
        }

        // Delete the activity (cascade will delete submissions and respondents)
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity and all associated data deleted successfully.');
    }

    /**
     * Delete a specific submission from an activity.
     * Also deletes associated respondent photos.
     */
    public function destroySubmission(Activity $activity, Submission $submission)
    {
        // Ensure the submission belongs to this activity
        if ($submission->activity_id !== $activity->id) {
            abort(404, 'Submission not found in this activity');
        }

        // Load respondents with photos
        $submission->load('respondents');

        // Delete all respondents' photos
        foreach ($submission->respondents as $respondent) {
            if ($respondent->photo_path) {
                Storage::disk('public')->delete($respondent->photo_path);
            }
        }

        // Delete the submission (cascade will delete respondents)
        $submission->delete();

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Submission and its photos deleted successfully.');
    }
}

