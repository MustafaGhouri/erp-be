<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\EncryptionHelpers;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'status',
        'role_id',
        'is_online',
        'verification_code',
        'image',
        'region',
        'customer',
        'location',
        'department',
        "balance",
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function region_detail()
    {
        return $this->belongsTo(Region::class, 'region', 'id');
    }

    public function customer_detail()
    {
        return $this->belongsTo(Customer::class, 'customer', 'id');
    }

    public function location_detail()
    {
        return $this->belongsTo(Location::class, 'location', 'id');
    }

    public function department_detail()
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    // Define attributes that need encryption and decryption
    protected $encryptedAttributes = [
        'balance',
        'iban',
    ];

    // Mutators for encrypting and decrypting attributes
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptedAttributes)) {
            return EncryptionHelpers::decrypt($value);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            $value = EncryptionHelpers::encrypt($value);
        }

        parent::setAttribute($key, $value);
    }
}
