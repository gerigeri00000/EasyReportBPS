<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Submit Report - {{ $activity->title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Client-side image compression library -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.1/dist/browser-image-compression.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-100" x-data="submissionForm()">
        <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-4xl">
                <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                    Submit Your Data
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Activity: <strong>{{ $activity->title }}</strong><br>
                    {{ $activity->location }} • {{ $activity->activity_date->format('d M Y') }}
                </p>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="space-y-6" action="{{ route('public.submit', $activity->uuid) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Partner Selection -->
                        <div>
                            <label for="nama_mitra" class="block text-sm font-medium text-gray-700">
                                Partner/Organization Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select
                                    name="nama_mitra"
                                    id="nama_mitra"
                                    required
                                    class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                                >
                                    <option value="" disabled {{ old('nama_mitra') ? '' : 'selected' }}>
                                        -- Select your organization --
                                    </option>
                                    @foreach($activity->partners ?? [] as $partner)
                                        <option value="{{ $partner }}" {{ old('nama_mitra') == $partner ? 'selected' : '' }}>
                                            {{ $partner }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('nama_mitra')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-6 border-gray-300">

                        <p class="text-sm text-gray-600 mb-4">
                            Add SLS groups. For each SLS, enter the shared information and add respondents within that group.
                        </p>

                        <!-- SLS Groups Container -->
                        <div id="groups-container" class="space-y-6">
                            <template x-for="(group, groupIndex) in groups" :key="groupIndex">
                                <div class="border rounded-lg p-4 bg-gray-50 relative">
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="font-semibold text-gray-800">SLS Group <span x-text="groupIndex + 1"></span></h4>
                                        <button type="button" @click="removeGroup(groupIndex)" x-show="groups.length > 1"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium whitespace-nowrap">
                                            Remove SLS Group
                                        </button>
                                    </div>

                                    <!-- SLS Shared Fields -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs text-gray-700">NKS Responden</label>
                                            <input type="text"
                                                :name="`groups[${groupIndex}][nks_resp]`"
                                                x-model="group.nks_resp"
                                                class="block w-full rounded-md border-gray-300 border px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-700">Kecamatan SLS</label>
                                            <input type="text"
                                                :name="`groups[${groupIndex}][kec_sls]`"
                                                x-model="group.kec_sls"
                                                required
                                                class="block w-full rounded-md border-gray-300 border px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-700">Desa SLS</label>
                                            <input type="text"
                                                :name="`groups[${groupIndex}][desa_sls]`"
                                                x-model="group.desa_sls"
                                                required
                                                class="block w-full rounded-md border-gray-300 border px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-700">Nama SLS</label>
                                            <input type="text"
                                                :name="`groups[${groupIndex}][nama_sls]`"
                                                x-model="group.nama_sls"
                                                required
                                                class="block w-full rounded-md border-gray-300 border px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>

                                    <!-- Respondents within this SLS Group -->
                                    <div class="mt-4 border-t border-gray-200 pt-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="font-medium text-gray-700">Respondents in this SLS</h5>
                                            <button type="button" @click="addRespondent(groupIndex)"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium py-1 px-3 rounded">
                                                + Add Respondent
                                            </button>
                                        </div>

                                        <div class="space-y-3">
                                            <template x-for="(respondent, respIndex) in group.respondents" :key="respIndex">
                                                <div class="border rounded p-3 bg-white flex justify-between items-start gap-3">
                                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs text-gray-700">Nama Responden</label>
                                                            <input type="text"
                                                                :name="`groups[${groupIndex}][respondents][${respIndex}][nama_resp]`"
                                                                x-model="respondent.nama_resp"
                                                                required
                                                                class="block w-full rounded-md border-gray-300 border px-2 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-700">Photo (auto-compressed)</label>
                                                            <div class="relative">
                                                                <input type="file"
                                                                    :name="`groups[${groupIndex}][respondents][${respIndex}][photo]`"
                                                                    accept=".jpg,.jpeg,.png"
                                                                    @change="compressImage($event, groupIndex, respIndex)"
                                                                    class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                                <span x-show="respondent.compressStatus === 'compressing'" class="absolute right-2 top-1.5 text-xs text-orange-600 font-medium">Compressing...</span>
                                                                <span x-show="respondent.compressStatus === 'done'" class="absolute right-2 top-1.5 text-xs text-green-600 font-medium">✓</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" @click="removeRespondent(groupIndex, respIndex)" x-show="group.respondents.length > 1"
                                                        class="text-red-500 hover:text-red-700 text-sm self-start mt-5">
                                                        ✕
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <p x-show="group.respondents.length === 0" class="text-sm text-gray-500 italic">
                                            No respondents added yet. Click "+ Add Respondent" to add.
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Add SLS Group Button -->
                        <div class="mt-4">
                            <button type="button" @click="addGroup"
                                class="flex w-full justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                + Add Another SLS Group
                            </button>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Submit Report
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 text-center text-xs text-gray-500">
                        This form is provided by BPS Office Report System.
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('submissionForm', function() {
                    return {
                        groups: @json(old('groups', [])),

                        init() {
                            if (this.groups.length === 0) {
                                this.addGroup();
                            }
                        },

                        addGroup() {
                            this.groups.push({
                                nks_resp: '',
                                kec_sls: '',
                                desa_sls: '',
                                nama_sls: '',
                                respondents: [this.emptyRespondent()]
                            });
                        },

                        removeGroup(index) {
                            if (this.groups.length > 1) {
                                this.groups.splice(index, 1);
                            }
                        },

                        addRespondent(groupIndex) {
                            this.groups[groupIndex].respondents.push(this.emptyRespondent());
                        },

                        removeRespondent(groupIndex, respIndex) {
                            if (this.groups[groupIndex].respondents.length > 1) {
                                this.groups[groupIndex].respondents.splice(respIndex, 1);
                            }
                        },

                        emptyRespondent() {
                            return {
                                nama_resp: '',
                                photo: null,
                                compressStatus: 'idle' // idle, compressing, done, error
                            };
                        },

                        async compressImage(event, groupIndex, respIndex) {
                            const input = event.target;
                            const file = input.files[0];
                            if (!file) return;

                            // Check if imageCompression is available
                            if (typeof imageCompression === 'undefined') {
                                console.warn('imageCompression library not loaded, skipping compression');
                                return;
                            }

                            const respondent = this.groups[groupIndex].respondents[respIndex];
                            respondent.compressStatus = 'compressing';

                            try {
                                const compressedFile = await imageCompression(file, {
                                    maxSizeMB: 0.5,
                                    maxWidthOrHeight: 1200,
                                    useWebWorker: true,
                                    fileType: 'image/jpeg', // Convert to JPEG for better compression
                                    initialQuality: 0.8
                                });

                                // Replace the file in the input's FileList
                                const dt = new DataTransfer();
                                dt.items.add(compressedFile);
                                input.files = dt.files;

                                respondent.compressStatus = 'done';
                            } catch (error) {
                                console.error('Image compression failed:', error);
                                respondent.compressStatus = 'error';
                                // Keep original file if compression fails
                            }
                        }
                    };
                });
            });
        </script>
    </body>
</html>
