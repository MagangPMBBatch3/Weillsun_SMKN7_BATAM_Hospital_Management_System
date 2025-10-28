<section>
    <header class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="bx bx-user-circle text-indigo-500 text-3xl"></i>
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="relative w-44 h-44 mx-auto">
            <img src="{{ $user->profile?->foto ? asset('storage/' . $user->profile->foto) : asset('default_pp.jpg') }}"
                id="userProfileFotoPreview" class="w-44 w h-44 rounded-full mx-auto object-cover" alt="User Photo">

            <label for="addUserProfileFoto"
                class="absolute bottom-2 right-2 w-9 h-9 flex transition text-white items-center justify-center border-4 border-white bg-blue-500 rounded-full cursor-pointer hover:bg-blue-700">
                <i class='bx bxs-edit-alt text-md'></i>
            </label>
            <input type="file" id="addUserProfileFoto" name="foto" class="hidden" accept="image/*" />
        </div>

        @push('scripts')
            <script>
                document.getElementById('addUserProfileFoto').onchange = function() {
                    const [file] = this.files;
                    if (file) {
                        document.getElementById('userProfileFotoPreview').src = URL.createObjectURL(file);
                    }
                };
            </script>
        @endpush

        <div>
            <x-input-label for="nickname" :value="__('Nickname')" />
            <x-text-input id="nickname" name="nickname" type="text" class="mt-1 block w-full" :value="old('nickname', $user->profile?->nickname ?? $user->name)"
                required autocomplete="nickname" />
            <x-input-error class="mt-2" :messages="$errors->get('nickname')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" inputmode="email"
                class="mt-1 bg-gray-100 block w-full" :value="old('email', $user->email)" readonly autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            <div class="mt-4">
                <x-input-label for="telepon" :value="__('Phone Number')" />
                <x-text-input id="telepon" name="telepon" type="text" inputmode="numeric" maxlength="13"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)" class="mt-1 block w-full"
                    :value="old('telepon', $user->profile?->telepon)" autocomplete="tel" />

                <x-input-error class="mt-2" :messages="$errors->get('telepon')" />
            </div>

            <div class="mt-4">
                <x-input-label for="alamat" :value="__('Address')" />
                <textarea id="alamat" name="alamat"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('alamat', $user->profile?->alamat) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
            </div>

            {{-- <div class="mt-4">
                <x-input-label for="tenaga_medis_id" :value="__('Tenaga Medis')" />
                <select id="tenaga_medis_id" name="tenaga_medis_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Pilih Tenaga Medis</option>
                    @foreach ($tenagaMedisList as $tenagaMedis)
                        <option value="{{ $tenagaMedis->id }}"
                            {{ old('tenaga_medis_id', $tenagaMedis->id) == optional($user->profile)->id ? 'selected' : '' }}>
                            {{ $tenagaMedis->profile?->nickname }} - {{ $tenagaMedis->spesialisasi }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('tenaga_medis_id')" />
            </div> --}}

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex justify-between gap-4">

            @if ($userTenagaMedis)
                <div class="flex items-center text-green-600 dark:text-green-400">
                    <i class='bx bx-check-circle mr-2 text-xl'></i>
                    <p class="px-2 py-1 rounded-full bg-green-100">
                        Already a <span class="font-semibold">{{ $userTenagaMedis->spesialisasi }}</span> Specialist
                    </p>
                </div>
            @else
                <x-blue-button x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'create-tenaga-medis')">
                    Add as Med Staff
                </x-blue-button>
            @endif

            <div class="flex gap-2 items-center">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                @endif
            </div>
        </div>
    </form>

    <x-modal name="create-tenaga-medis" focusable>
        <div class="p-6">
            <form onsubmit="createTenagaMedis(); event.preventDefault();">

                <x-loading></x-loading>

                <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Med Staff</h2>

                <div class="space-y-3">
                    <input type="hidden" id="create-profile-id" value="{{ $user->profile?->id ?? '' }}" />

                    <x-input-label>Specialization</x-input-label>
                    <x-text-input id="create-spesialisasi" type="text" placeholder="Enter Your Specialization..."
                        class="border p-2 w-full rounded" required />

                    <x-input-label>No STR</x-input-label>
                    <x-text-input id="create-no-str" type="number" placeholder="Enter Your STR number..."
                        class="border p-2 w-full rounded" required />

                    <div class="flex justify-end mt-4">
                        <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                        <x-primary-button class="ml-2">Save</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        const API_URL = "/graphql";

        function showLoading() {
            document.body.style.overflow = "hidden";
            const overlay = document.getElementById("loadingOverlay");
            if (overlay) overlay.classList.remove("hidden");
        }

        function hideLoading() {
            document.body.style.overflow = "";
            const overlay = document.getElementById("loadingOverlay");
            if (overlay) overlay.classList.add("hidden");
        }

        async function createTenagaMedis() {
            const profile_id = document.getElementById("create-profile-id").value.trim();
            const spesialisasi = document.getElementById("create-spesialisasi").value.trim();
            const no_str = document.getElementById("create-no-str").value.trim();

            if (!spesialisasi || !no_str)
                return alert("Please fill in all required fields!");

            showLoading();

            const mutationTenagaMedis = `
                mutation($input: CreateTenagaMedisInput!) {
                    createTenagaMedis(input: $input) {
                        id spesialisasi no_str profile_id
                    }
                }
            `;
            const variablesTenagaMedis = {
                input: {
                    profile_id,
                    spesialisasi,
                    no_str
                },
            };

            try {
                const resTenagaMedis = await fetch(API_URL, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        query: mutationTenagaMedis,
                        variables: variablesTenagaMedis,
                    }),
                });

                const resultTenagaMedis = await resTenagaMedis.json();
                const dataTenagaMedis = resultTenagaMedis?.data?.createTenagaMedis;

                if (dataTenagaMedis) {
                    window.dispatchEvent(
                        new CustomEvent("close-modal", {
                            detail: "create-tenaga-medis"
                        })
                    );
                        
                
                } else {
                    console.error("GraphQL Error:", resultTenagaMedis.errors);
                    alert("Failed to create Tenaga Medis!");
                }
            } catch (error) {
                console.error("Error:", error);
                alert("An error occurred while creating the data");
            } finally {
                hideLoading();
            }
            window.location.reload();
        }
    </script>

</section>
