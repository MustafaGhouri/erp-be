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
        'password',
        'status',
        'role_id',
        'is_online',
        'verification_code',
        'image', 
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
    public function country()
    {
        return $this->belongsTo(Countries::class, "country_id");
    }
    public function state()
    {
        return $this->belongsTo(States::class, "state_id");
    }
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class, "jurisdiction_id");
    }
    public function lawyer_ratings()
    {
        return $this->hasMany(LawyerRating::class, "lawyer_id");
    }
    public function lawyer_experties()
    {
        return $this->hasMany(AreaExpertiseOfUsers::class, 'user_id', 'id');
    }
    public function lawyer_appointments()
    {
        return $this->hasMany(Appointment::class, 'lawyer_id', 'id');
    }
    public function blogLikes()
    {
        return $this->hasMany(BlogUserLike::class, 'user_id', 'id');
    }
    public function withdraw_requests()
    {
        return $this->hasMany(WithdrawRequest::class, 'user_id', 'id');
    }
    public function cases()
    {
        return $this->hasMany(Cases::class, 'lawyer_id', 'id');
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'lawyer_id', 'id');
    }
    public function transactions_history()
    {
        return $this->hasMany(TransactionsHistory::class, 'user_id', 'id');
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