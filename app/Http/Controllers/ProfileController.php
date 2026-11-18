<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display a user's public profile.
     */
    public function show(Request $request, User $user): View
    {
        $tabs = ['posts', 'replies', 'media', 'likes'];
        $tab = in_array($request->query('tab'), $tabs, true) ? $request->query('tab') : 'posts';

        $user->loadCount(['posts', 'followers', 'following']);

        $posts = collect();
        $replies = collect();

        if ($tab === 'posts') {
            $posts = $user->posts()
                ->with('user')
                ->withCount('comments')
                ->latest()
                ->get();
        } elseif ($tab === 'replies') {
            $replies = $user->comments()
                ->with(['post.user'])
                ->latest()
                ->get();
        }

        return view('profile.show', [
            'user' => $user,
            'tab' => $tab,
            'posts' => $posts,
            'replies' => $replies,
            'isOwnProfile' => $request->user()->is($user),
            'isFollowing' => $user->isFollowedBy($request->user()),
        ]);
    }

    /**
     * Follow a user.
     */
    public function follow(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 403, 'You cannot follow yourself.');

        $request->user()->following()->syncWithoutDetaching([$user->id]);

        return Redirect::back()->with('status', 'followed');
    }

    /**
     * Unfollow a user.
     */
    public function unfollow(Request $request, User $user): RedirectResponse
    {
        $request->user()->following()->detach($user->id);

        return Redirect::back()->with('status', 'unfollowed');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->fill(Arr::only($validated, ['name', 'username', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $user->profile()->updateOrCreate(
            [],
            Arr::only($validated, ['bio', 'location', 'profile_pic', 'header_pic'])
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
