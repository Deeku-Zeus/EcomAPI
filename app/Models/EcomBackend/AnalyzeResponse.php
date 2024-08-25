<?php

    namespace App\Models\EcomBackend;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class AnalyzeResponse extends Model
    {
        use HasFactory;

        protected $connection = 'ecomBackend';

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'requestId',
            'is_classified',
            'coordinates',
            'object',
            'confidence',
            'uid',
            'responseData'
        ];

        /**
         * Get the user that owns the requests.
         */
        public function analyzeRequest()
        {
            return $this->belongsTo(AnalyzeRequest::class);
        }
    }
