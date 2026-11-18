<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move the profile fields off the users table into a dedicated profiles table.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('location', 100)->nullable();
            $table->string('profile_pic')->nullable()->default('https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=200&q=80');
            $table->string('header_pic')->nullable()->default('https://images.unsplash.com/photo-1505839673365-e3971f8d9184?auto=format&fit=crop&w=1400&q=80');
            $table->timestamps();
        });

        // Preserve existing profile data before the user columns are removed.
        DB::table('users')
            ->select('id', 'bio', 'location', 'profile_pic', 'header_pic')
            ->orderBy('id')
            ->each(function ($user) {
                DB::table('profiles')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'bio' => $user->bio,
                        'location' => $user->location,
                        'profile_pic' => $user->profile_pic,
                        'header_pic' => $user->header_pic,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'location', 'profile_pic', 'header_pic']);
        });
    }

    /**
     * Move the profile fields back onto the users table.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->string('location', 100)->nullable();
            $table->string('profile_pic')->nullable()->default('https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=200&q=80');
            $table->string('header_pic')->nullable()->default('https://images.unsplash.com/photo-1505839673365-e3971f8d9184?auto=format&fit=crop&w=1400&q=80');
        });

        DB::table('profiles')
            ->orderBy('id')
            ->each(function ($profile) {
                DB::table('users')->where('id', $profile->user_id)->update([
                    'bio' => $profile->bio,
                    'location' => $profile->location,
                    'profile_pic' => $profile->profile_pic,
                    'header_pic' => $profile->header_pic,
                ]);
            });

        Schema::dropIfExists('profiles');
    }
};
