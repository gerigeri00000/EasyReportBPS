<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{-- {{ __('Activities Management') }} --}}
                </h2>
                <a href="{{ route('activities.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Create New Activity
                </a>
            </div>

            <!-- Bulk Export Notice -->
            <div class="mb-4 text-sm text-gray-600">
                Select activities and click "Download Bulk Word" to generate a single Word document containing all partner reports from selected activities.
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Activities Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Bulk Export Form (only contains checkbox column and button) -->
                <form id="bulk-export-form" action="{{ route('activities.bulk.word') }}" method="POST">
                    @csrf
                    <div class="mb-4 flex items-center px-6 py-3 bg-gray-50">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Download bulk Word for selected activities?')">
                            Download Bulk Word
                        </button>
                        <span class="ml-2 text-sm text-gray-600">Selected: <span id="selected-count">0</span></span>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="form-checkbox h-4 w-4 text-blue-600">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Public URL
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Submissions
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($activities as $activity)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="activity_ids[]" value="{{ $activity->id }}" class="activity-checkbox form-checkbox h-4 w-4 text-blue-600" onchange="updateSelectedCount()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $activity->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $activity->location }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $activity->activity_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('public.form', $activity->uuid) }}" target="_blank" class="text-blue-600 hover:text-blue-900 underline">
                                            {{ Str::limit($activity->uuid, 8) }}...
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $activity->submissions_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('activities.show', $activity) }}" class="text-gray-600 hover:text-gray-900">View</a>

                                            <a href="{{ route('activities.edit', $activity) }}" class="text-blue-600 hover:text-blue-900">Edit</a>

                                            <a href="{{ route('activities.word.generate', $activity) }}" class="text-green-600 hover:text-green-900" onclick="return confirm('Download Word file?')">Word</a>

                                            <button type="button"
                                                onclick="deleteActivity({{ $activity->id }}, '{{ $activity->title }}')"
                                                class="text-red-600 hover:text-red-900 focus:outline-none">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No activities created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $activities->links() }}
            </div>
        </div>
    </div>

    <!-- Hidden form for delete action -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function deleteActivity(id, title) {
            if (confirm('Delete activity "' + title + '" and all its associated data? This cannot be undone.')) {
                const form = document.getElementById('delete-form');
                form.action = '/activities/' + id;
                form.submit();
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.activity-checkbox');

            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateSelectedCount();
            });

            window.updateSelectedCount = function() {
                const checked = document.querySelectorAll('.activity-checkbox:checked').length;
                document.getElementById('selected-count').textContent = checked;
            };
            // Initial count
            updateSelectedCount();
        });
    </script>
</x-app-layout>
