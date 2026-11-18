<section>
    <h2 class="card-title">{{ __('Delete Account') }}</h2>
    <p class="card-subtitle">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <form method="post" action="{{ route('profile.destroy') }}" class="profile-form"
          onsubmit="return confirm('{{ __('Are you sure you want to delete your account? This cannot be undone.') }}');">
        @csrf
        @method('delete')

        <div class="form-group">
            <label for="delete_password">{{ __('Password') }}</label>
            <input id="delete_password" name="password" type="password" placeholder="{{ __('Enter your password to confirm') }}" autocomplete="current-password">
            @if ($errors->userDeletion->has('password'))
                <p class="field-error">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn danger">{{ __('Delete Account') }}</button>
        </div>
    </form>
</section>
