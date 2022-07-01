<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycLevel extends Model
{
    use HasFactory;

    protected  $fillable = [
        'level','trx_cap'
    ];

    public function kycRequirements()
    {
        return $this->belongsToMany(KycLevelRequirement::class);
    }
}
