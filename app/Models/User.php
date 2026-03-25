<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


use Spatie\Permission\Traits\HasRoles; // <--- បន្ទាត់ទី ១ (Import)
use Spatie\Activitylog\Traits\LogsActivity; // <--- ថែមនេះសម្រាប់ Log
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * កំណត់ការកត់ត្រា Log (សម្រាប់ Activity Log)
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}
