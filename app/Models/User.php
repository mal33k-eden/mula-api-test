<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'handle',
        'email',
        'mobile',
        'country',
        'country_code',
        'can_receive_newsletter',
        'referred_code',
        'referral_code',
        'city',
        'address',
        'stream_token',
        'kyc_level_id',
        'connected_country_id',
        'btc_wallet',
        'mula_wallet',
        'password',
        'profile_photo',
        'two_factor_code',
        'two_factor_expires_at',
        'invite_points',
        'dial_code',
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
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
        'mobile_verified_at',
        'two_factor_expires_at',
    ];

     protected function setFirstNameAttribute($value){
         $this->attributes['first_name']= ucfirst($value);
     }
    protected function setLastNameAttribute($value){
        $this->attributes['last_name']= ucfirst($value);
    }
    protected function setHandleAttribute($value){
        $this->attributes['handle']= strtolower($value);
    }
    protected function setEmailAttribute($value){
        $this->attributes['email']= strtolower($value);
    }
    protected function setPasswordAttribute($value){
        $this->attributes['password']= bcrypt($value);
    }
    public  function setConnectedCountryId($value){
         $country = ConnectedCountry::where('name', $value)->first();
         if ($country != null){
             $this->connected_country_id = $country->id;
             $this->save();
         }
    }

    protected function getProfilePhotoAttribute($value){
        $base = env('APP_LINK').Storage::url('profile_photos/');
        if ($value) {
            return $base.$value;
        } else {
            return $base.$value;
        }
    }
    public function generateTwoFactorCode($type)
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        if ($type == 'email'){
            $this->email_verified_at = null;
        }else{
            $this->mobile_verified_at = null;
        }
        $this->save();
    }


    public function resetTwoFactorCode($type)
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        if ($type == 'email'){
            $this->email_verified_at= now();
        }else{
            $this->mobile_verified_at= now();
        }
        $this->save();
    }

    public static function generateInviteCode($handle){
        return $handle.rand(100, 999);
    }

    public static function addReferralPoints($referred_code){
        if ($referred_code != ''){
            $user = User::where('referral_code', $referred_code)->first();
            $old_points = $user->invite_points;
            $user->invite_points = $old_points + 1;
            $user->save();
        }

    }
    //BEGINNING OF RELATIONSHIPS
    public function kycLevel()
    {
        return $this->belongsTo(KycLevel::class);
    }
    public function connectedCountry()
    {
        return $this->belongsTo(ConnectedCountry::class);
    }



}
