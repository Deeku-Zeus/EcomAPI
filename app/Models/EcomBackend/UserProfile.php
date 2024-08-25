<?php
namespace App\Models\EcomBackend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * Database connection
     *
     * @var string
     */
    protected $connection = 'ecomBackend';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'profile_name',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the analyze requests associated with the user.
     */
    public function analyzeRequest(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnalyzeRequest::class,'requestId');
    }
}
