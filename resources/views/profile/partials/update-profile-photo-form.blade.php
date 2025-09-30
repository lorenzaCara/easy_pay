<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile photo.") }}
        </p>
    </header>

    <form method='POST' action='{{ route('profile.update-photo') }}' enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <img src="{{ auth()->user()->profile_photo_url }}" alt="Foto profilo" class="h-20 w-20 rounded-full object-cover">
        </div>

        <div>
            <input type="file" name="profile_photo" accept="image/*" class="block w-full text-sm text-gray-700">
            @error('profile_photo')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-primary-button>{{ __('Aggiorna foto') }}</x-primary-button>
        </div>
    </form>
</section>