<section>
    <h2 class="card-title">{{ __('Update Password') }}</h2>
    <p class="card-subtitle">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
            @if ($errors->updatePassword->has('current_password'))
                <p class="field-error">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password">
            @if ($errors->updatePassword->has('password'))
                <p class="field-error">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="field-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="form-actions">
            @if (session('status') === 'password-updated')
                <p class="form-status">{{ __('Saved.') }}</p>
            @endif

            <button type="submit" class="btn submit">{{ __('Save') }}</button>
        </div>
    </form>
</section>
