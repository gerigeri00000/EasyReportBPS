<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Activities</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_activities'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Submissions</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['total_submissions'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Activities This Month</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['activities_this_month'] }}</div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="font-semibold text-lg text-gray-800">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('activities.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            + Create Activity
                        </a>
                        <a href="{{ route('activities.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Manage Activities
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-lg text-gray-800">Recent Activities</h3>
                    <a href="{{ route('activities.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                <div class="p-6">
                    @forelse ($recent_activities as $activity)
                        <div class="mb-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0 last:mb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $activity->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $activity->location }} • {{ $activity->activity_date->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Submissions: {{ $activity->submissions_count }}
                                    </p>
                                </div>
                                <a href="{{ route('activities.show', $activity) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No activities created yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
