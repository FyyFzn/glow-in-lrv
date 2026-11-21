<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'username', //[cite: 11]
    'name', //[cite: 11]
    'email',
    'password', //[cite: 11]
])]
#[Hidden(['password', 'remember_token'])] //[cite: 11]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Keep the profile available wherever a user is loaded.
     *
     * @var list<string>
     */
    protected $with = ['profile'];

    /**
     * Profile fields are stored on the related Profile model but are still
     * exposed on the user payload for existing consumers.
     *
     * @var list<string>
     */
    protected $appends = ['bio', 'location', 'profile_pic', 'header_pic'];

    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->profile()->create([]);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Profile fields, read through the related Profile model.
     */
    protected function bio(): Attribute
    {
        return Attribute::make(get: fn () => $this->profile?->bio);
    }

    protected function location(): Attribute
    {
        return Attribute::make(get: fn () => $this->profile?->location);
    }

    protected function profilePic(): Attribute
    {
        return Attribute::make(get: fn () => $this->profile?->profile_pic);
    }

    protected function headerPic(): Attribute
    {
        return Attribute::make(get: fn () => $this->profile?->header_pic);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Users that follow this user.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    /**
     * Users this user follows.
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isFollowedBy(?User $user): bool
    {
        return $user !== null && $this->followers()->whereKey($user->id)->exists();
    }
}