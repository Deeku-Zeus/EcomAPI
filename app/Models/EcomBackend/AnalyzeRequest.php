<?php

namespace App\Models\EcomBackend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyzeRequest extends Model
{
    use HasFactory;
    protected $connection = 'ecomBackend';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profileId',
        'image',
        'videoName',
        'timestamp',
        'request_token',
        'is_analyzed',
        'responseTime'
    ];
    /**
     * Get the user that owns the profile.
     */
    public function analyzeResoinse()
    {
        return $this->hasMany(AnalyzeResponse::class);
    }
}
