<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <!-- Profile Avatar -->
    <div class="mt-6 flex items-center gap-6">
        <div class="shrink-0">
            @if ($user->profile)
                <div class="flex -space-x-4 rtl:space-x-reverse">
                    <img src="{{ asset('storage/' . $user->profile) }}" alt="Profile Image"
                        class="w-20 h-20 border-2 border-buffer rounded-full">
                </div>
            @else
                <div
                    class="w-20 h-20 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Upload Profile Image -->
        <div>
            <x-input-label for="profile" :value="__('Profile Image')" />
            <input id="profile" name="profile" type="file" accept="image/*"
                class="mt-1 block w-full text-sm text-gray-600">
            <x-input-error class="mt-2" :messages="$errors->get('profile')" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>
            @endif
        </div>

        <!-- Save -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
