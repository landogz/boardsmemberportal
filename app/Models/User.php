<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\MediaLibrary;
use App\Models\GovernmentAgency;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'pre_nominal_title',
        'post_nominal_title',
        'email',
        'username',
        'username_edited',
        'password_hash',
        'privilege',
        'representative_type',
        'revoked_permissions',
        'government_agency_id',
        'is_active',
        'is_online',
        'mobile',
        'landline',
        'representative_name',
        'company',
        'office_building_no',
        'office_house_no',
        'office_street_name',
        'office_purok',
        'office_sitio',
        'office_region',
        'office_province',
        'office_city_municipality',
        'office_barangay',
        'position',
        'designation',
        'sex',
        'gender',
        'birth_date',
        'status',
        'profile_picture',
        'banner_image',
        'email_verified_at',
        'current_session_id',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
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
            'is_active' => 'boolean',
            'username_edited' => 'boolean',
            'birth_date' => 'date',
            'last_activity' => 'datetime',
            'revoked_permissions' => 'array',
        ];
    }

    /**
     * Get the password attribute (map password_hash to password)
     */
    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }

    /**
     * Set the password attribute (map password to password_hash)
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Get the profile picture media
     */
    public function profilePictureMedia()
    {
        return $this->belongsTo(MediaLibrary::class, 'profile_picture');
    }

    /**
     * Get the banner image media
     */
    public function bannerImageMedia()
    {
        return $this->belongsTo(MediaLibrary::class, 'banner_image');
    }

    /**
     * Get the government agency for this user.
     */
    public function governmentAgency()
    {
        return $this->belongsTo(GovernmentAgency::class, 'government_agency_id');
    }

    /**
     * Check if user has admin or consec privilege
     */
    public function isAdminOrConsec(): bool
    {
        return $this->privilege === 'admin' || $this->privilege === 'consec';
    }

    /**
     * Override Spatie's can() method to ensure admin privilege has full access
     */
    public function can($permission, $guardName = null): bool
    {
        // Admin privilege always has full access to everything
        if ($this->privilege === 'admin') {
            return true;
        }
        
        // Call parent can() method from Spatie
        return parent::can($permission, $guardName);
    }

    /**
     * Check if user has permission (admin privilege and admin role have all permissions automatically)
     * But respect direct permission revocations
     */
    public function hasPermission($permission): bool
    {
        // Admin privilege always has full access to everything (cannot be revoked)
        if ($this->privilege === 'admin') {
            return true;
        }
        
        // Admin role always has all permissions (cannot be revoked)
        if ($this->hasRole('admin')) {
            return true;
        }
        
        // Check if permission is explicitly revoked
        $revokedPermissions = $this->revoked_permissions ?? [];
        if (in_array($permission, $revokedPermissions)) {
            return false;
        }
        
        // Check actual permissions from roles and direct assignments in the database
        // This respects what's configured in Role & Permission Manager
        return $this->can($permission);
    }
}
