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
            'analyze_request_id',
            'coordinates',
            'confidence',
            'tags',
            'uid',
            'color'
        ];

        /**
         * Get the user that owns the requests.
         */
        public function analyzeRequest()
        {
            return $this->belongsTo(AnalyzeRequest::class);
        }
    }
