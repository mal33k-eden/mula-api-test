<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycLevelRequirement extends Model
{
    use HasFactory;

    protected  $fillable = [
        'kyc_level_id','connected_country_id','document'
    ];


    public function connectedCountry()
    {
        return $this->belongsTo(ConnectedCountry::class);
    }
    public function kycLevel()
    {
        return $this->belongsTo(KycLevel::class);
    }
}
