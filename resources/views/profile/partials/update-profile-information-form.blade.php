@php
    $profile = $user->profile;
@endphp

<section>
    <h2 class="card-title">{{ __('Profile Information') }}</h2>
    <p class="card-subtitle">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="Your display name" required autofocus autocomplete="name">
            @error('name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="username">{{ __('Username') }}</label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" placeholder="your_username" required autocomplete="username">
            @error('username')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="bio">{{ __('Bio') }}</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Tell us about yourself">{{ old('bio', $profile?->bio) }}</textarea>
            @error('bio')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="location">{{ __('Location') }}</label>
            <input id="location" name="location" type="text" value="{{ old('location', $profile?->location) }}" placeholder="e.g. Jakarta, Indonesia" autocomplete="address-level2">
            @error('location')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="profile_pic">{{ __('Profile Picture URL') }}</label>
            <input id="profile_pic" name="profile_pic" type="url" value="{{ old('profile_pic', $profile?->profile_pic) }}" placeholder="https://example.com/image.jpg">
            @error('profile_pic')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="header_pic">{{ __('Header / Cover URL') }}</label>
            <input id="header_pic" name="header_pic" type="url" value="{{ old('header_pic', $profile?->header_pic) }}" placeholder="https://example.com/cover.jpg">
            @error('header_pic')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="you@example.com" required autocomplete="username">
            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="form-hint">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="link-button">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="form-hint" style="color: var(--accent-green);">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <div class="form-actions">
            @if (session('status') === 'profile-updated')
                <p class="form-status">{{ __('Saved.') }}</p>
            @endif

            <a href="{{ route('profile.show', $user->username) }}" class="btn">{{ __('Cancel') }}</a>
            <button type="submit" class="btn submit">{{ __('Save Profile') }}</button>
        </div>
    </form>
</section>
