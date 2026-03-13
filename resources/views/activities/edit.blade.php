<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        Edit Activity
                    </h2>

                    <form action="{{ route('activities.update', $activity) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Activity Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $activity->title) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Activity Date -->
                        <div class="mb-4">
                            <label for="activity_date" class="block text-sm font-medium text-gray-700">Activity Date</label>
                            <input type="date" name="activity_date" id="activity_date" value="{{ old('activity_date', $activity->activity_date->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                            @error('activity_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Provinsi & Kabupaten -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                                <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi', $activity->provinsi) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                                @error('provinsi')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="kabupaten" class="block text-sm font-medium text-gray-700">Kabupaten</label>
                                <input type="text" name="kabupaten" id="kabupaten" value="{{ old('kabupaten', $activity->kabupaten) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                                @error('kabupaten')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Nama Pemeriksa -->
                        <div class="mb-4">
                            <label for="nama_pemeriksa" class="block text-sm font-medium text-gray-700">Nama Pemeriksa</label>
                            <input type="text" name="nama_pemeriksa" id="nama_pemeriksa" value="{{ old('nama_pemeriksa', $activity->nama_pemeriksa) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                            @error('nama_pemeriksa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Partners List -->
                        <div class="mb-6">
                            <label for="partners" class="block text-sm font-medium text-gray-700">
                                Partner/Organization List <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="partners"
                                id="partners"
                                rows="6"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2 font-mono text-sm"
                                placeholder="Enter one partner name per line. You can copy-paste from Excel or Notepad."
                            >{{ old('partners', implode("\n", $activity->partners ?? [])) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                Required. Add at least one partner. Each partner will appear in the public submission dropdown.
                            </p>
                            @error('partners')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Public URL Info -->
                        <div class="mb-6 p-4 bg-gray-50 rounded">
                            <p class="text-sm text-gray-600">
                                <strong>Public Submission URL:</strong><br>
                                <span class="font-mono text-blue-600">{{ url('submit/' . $activity->uuid) }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Share this link with partners for them to submit their data.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('activities.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Activity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>