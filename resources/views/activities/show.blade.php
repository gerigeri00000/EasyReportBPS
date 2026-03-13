<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        {{ $activity->title }}
                    </h2>
                    <p class="text-gray-600 mt-1">
                        {{ $activity->location }} • {{ $activity->activity_date->format('d F Y') }}
                    </p>
                    @if($activity->description)
                        <p class="text-gray-700 mt-2">{{ $activity->description }}</p>
                    @endif

                    <!-- Additional Activity Details -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Nama Pemeriksa:</strong> {{ $activity->nama_pemeriksa }}</p>
                        </div>
                        <div>
                            <p><strong>Provinsi:</strong> {{ $activity->provinsi }}</p>
                            <p><strong>Kabupaten:</strong> {{ $activity->kabupaten }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 ml-4 mt-1">
                    <a href="{{ route('activities.word.generate', $activity) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Download Word Report
                    </a>
                    <a href="{{ route('activities.edit', $activity) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit Activity
                    </a>
                </div>
            </div>

            <!-- Public URL -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>Public Submission URL:</strong><br>
                    <span class="font-mono text-blue-600 break-all">{{ url('submit/' . $activity->uuid) }}</span>
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    Partners can use this link to submit their data without logging in.
                </p>
            </div>

            <!-- Partners List -->
            @if(!empty($activity->partners))
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Registered Partners:</h4>
                    <ul class="list-disc list-inside text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-1">
                        @foreach($activity->partners as $partner)
                            <li>{{ $partner }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Submissions Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="font-semibold text-xl text-gray-800 mb-4">
                        Partner Submissions ({{ $submissions->count() }})
                    </h3>

                    @if($submissions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Respondents</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($submissions as $index => $submission)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $submission->nama_mitra }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->respondents_count }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->created_at->format('d M Y, H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($submission->respondents->count() > 0)
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($submission->respondents->take(4) as $respondent)
                                                            @if($respondent->photo_path)
                                                                <a href="{{ asset('storage/' . $respondent->photo_path) }}" target="_blank">
                                                                    <img src="{{ asset('storage/' . $respondent->photo_path) }}" alt="Respondent" class="h-10 w-10 object-cover rounded border">
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                        @if($submission->respondents->count() > 4)
                                                            <span class="text-xs text-gray-500 self-center">+{{ $submission->respondents->count() - 4 }} more</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">No respondents</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <form action="{{ route('activities.submissions.destroy', [$activity, $submission]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this submission and all its photos?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No submissions yet. Share the public URL to collect responses.</p>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('activities.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Activities
                </a>
            </div>
        </div>
    </div>
</x-app-layout>