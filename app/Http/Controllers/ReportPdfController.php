<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Services\WordExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportPdfController extends Controller
{
    /**
     * Generate bulk Word report for selected activities.
     * Expects a POST request with activity_ids array.
     */
    public function bulkGenerate(Request $request, WordExportService $wordExportService)
    {
        $request->validate([
            'activity_ids' => 'required|array',
            'activity_ids.*' => 'exists:activities,id'
        ]);

        $activityIds = $request->input('activity_ids');

        // Fetch activities with their submissions and respondents
        $activities = Activity::with(['submissions.respondents'])->whereIn('id', $activityIds)->get();

        if ($activities->isEmpty()) {
            return back()->with('error', 'No activities found.');
        }

        // Flatten all submissions from all activities
        $allSubmissions = new Collection();
        foreach ($activities as $activity) {
            foreach ($activity->submissions as $submission) {
                // Ensure submission has activity relation loaded
                $submission->setRelation('activity', $activity);
                $allSubmissions->push($submission);
            }
        }

        if ($allSubmissions->isEmpty()) {
            return back()->with('error', 'No submissions found for selected activities.');
        }

        // Generate the Word document using the new service
        $filePath = $wordExportService->generateBulk($allSubmissions);

        // Download the file
        $fileName = 'laporan-bulk-' . now()->format('Y-m-d-H-i-s') . '.docx';

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Generate Word report for a single activity.
     * This is a convenience wrapper around bulkGenerate for one activity.
     */
    public function generate(Activity $activity, WordExportService $wordExportService)
    {
        // Load submissions with respondents
        $activity->load(['submissions.respondents']);

        if ($activity->submissions->isEmpty()) {
            return back()->with('error', 'No submissions found for this activity.');
        }

        // Use generateBulk with a single activity's submissions
        $filePath = $wordExportService->generateBulk($activity->submissions);

        $fileName = 'laporan-' . $activity->uuid . '.docx';

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
