<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('id'); //
            $table->text('bio')->nullable()->after('email'); //
            $table->string('location', 100)->nullable()->after('bio'); //[cite: 11]
            $table->string('profile_pic')->default('https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=200&q=80')->after('location'); //[cite: 11]
            $table->string('header_pic')->default('https://images.unsplash.com/photo-1505839673365-e3971f8d9184?auto=format&fit=crop&w=1400&q=80')->after('profile_pic'); //[cite: 11]
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'bio', 'location', 'profile_pic', 'header_pic']); //[cite: 11]
        });
    }
};