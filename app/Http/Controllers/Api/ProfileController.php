<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()
            ->loadCount(['posts', 'followers', 'following'])
            ->load('posts');

        return response()->json($user);
    }

    /**
     * Display another user's public profile.
     */
    public function showUser(User $user): JsonResponse
    {
        $user->loadCount(['posts', 'followers', 'following'])->load('posts');

        return response()->json($user);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'bio' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'location' => ['sometimes', 'nullable', 'string', 'max:100'],
            'profile_pic' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'header_pic' => ['sometimes', 'nullable', 'string', 'max:2048'],
        ]);

        $user->fill(Arr::only($validated, ['name', 'username', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $user->profile()->updateOrCreate(
            [],
            Arr::only($validated, ['bio', 'location', 'profile_pic', 'header_pic'])
        );

        return response()->json(
            $user->fresh()->loadCount(['posts', 'followers', 'following'])
        );
    }
}
