<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Submission;
use App\Models\Respondent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicSubmissionController extends Controller
{
    /**
     * Show the public submission form for a specific activity.
     */
    public function showForm($uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();
        return view('public.submit', compact('activity'));
    }

    /**
     * Store a new submission (partner data with multiple respondents grouped by SLS).
     */
    public function store(Request $request, $uuid)
    {
        $activity = Activity::where('uuid', $uuid)->firstOrFail();

        // Build validation rules
        $rules = [
            'nama_mitra' => ['required', 'string'],
            'groups' => 'required|array',
            'groups.*.nks_resp' => 'nullable|string|max:50',
            'groups.*.kec_sls' => 'required|string|max:255',
            'groups.*.desa_sls' => 'required|string|max:255',
            'groups.*.nama_sls' => 'required|string|max:255',
            'groups.*.respondents' => 'required|array',
            'groups.*.respondents.*.nama_resp' => 'required|string|max:255',
            // Allow up to 5MB per image after compression (frontend sends compressed ~500KB, but safety limit)
            'groups.*.respondents.*.photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];

        // If activity has partners list, enforce selection from that list
        if (!empty($activity->partners)) {
            $rules['nama_mitra'][] = Rule::in($activity->partners);
        }

        // Validate partner name and the groups array
        $validated = $request->validate($rules);

        // Create the submission record
        $submission = Submission::create([
            'activity_id' => $activity->id,
            'nama_mitra' => $validated['nama_mitra'],
        ]);

        $groups = $request->input('groups', []);

        // Process each SLS group
        foreach ($groups as $groupData) {
            $groupRespondents = $groupData['respondents'] ?? [];

            foreach ($groupRespondents as $respIdx => $respData) {
                $namaResp = $respData['nama_resp'] ?? '';

                // Handle file upload for this respondent
                $photoPath = null;
                $photoFile = $request->file("groups.{$respIdx}.respondents.{$respIdx}.photo") ?? null;
                // Actually need to get file from the nested structure properly
                // We'll iterate differently: use $files array passed separately
            }
        }

        // Better approach: loop with both input and files
        $groupsInput = $request->input('groups', []);
        $groupKeys = array_keys($groupsInput);

        foreach ($groupKeys as $groupIdx) {
            $groupData = $groupsInput[$groupIdx];
            $respondentsInput = $groupData['respondents'] ?? [];
            $respondentKeys = array_keys($respondentsInput);

            foreach ($respondentKeys as $respIdx) {
                $respData = $respondentsInput[$respIdx];
                $namaResp = $respData['nama_resp'] ?? '';

                // Get the uploaded file for this nested position
                $photoFile = $request->file("groups.{$groupIdx}.respondents.{$respIdx}.photo");

                $photoPath = null;
                if ($photoFile) {
                    $photoPath = $photoFile->store('reports', 'public');
                }

                // Create the respondent record
                $submission->respondents()->create([
                    'nama_resp' => $namaResp,
                    'nks_resp' => $groupData['nks_resp'] ?? null,
                    'kec_sls' => $groupData['kec_sls'] ?? '',
                    'desa_sls' => $groupData['desa_sls'] ?? '',
                    'nama_sls' => $groupData['nama_sls'] ?? '',
                    'photo_path' => $photoPath,
                ]);
            }
        }

        return view('public.success', compact('activity'));
    }
}
