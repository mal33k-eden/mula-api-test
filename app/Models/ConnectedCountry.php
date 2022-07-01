<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectedCountry extends Model
{
    use HasFactory;

    protected  $fillable = [
      'name','iso_code','dial_code', 'currency'
    ];

    public function kycLevelRequirement()
    {
        return $this->hasMany(KYCLevelRequirement::class);
    }
}
