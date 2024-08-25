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
        'user_profile_id',
        'image',
        'videoName',
        'timestamp',
        'request_token',
        'is_analyzed',
        'responseTime'
    ];
    /**
     * Get the responses.
     */
    public function analyzeResponse(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnalyzeResponse::class);
    }
    /**
     * Get the user that owns the profile.
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UserProfile::class,'requestId');
    }
}
