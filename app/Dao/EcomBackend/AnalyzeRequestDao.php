<?php

    namespace App\Dao\EcomBackend;

    use App\Models\EcomBackend\AnalyzeRequest;

    class AnalyzeRequestDao
    {
        /**
         * @var \App\Models\EcomBackend\AnalyzeRequest
         */
        protected AnalyzeRequest $analyzeRequest;

        /**
         * AnalyzeRequestDao constructor.
         *
         * @param \App\Models\EcomBackend\AnalyzeRequest $analyzeRequestObj
         */
        public function __construct(AnalyzeRequest $analyzeRequestObj)
        {
            $this->analyzeRequest = $analyzeRequestObj;
        }

        /**
         * Upsert the request data to database
         *
         * @param $upsertData
         *
         * @return mixed
         */
        public function storeAnalyzeRequest($upsertData): mixed
        {
            return $this->analyzeRequest->updateOrCreate(
                [
                    'request_token' => $upsertData['request_token']
                ],
                $upsertData
            );
        }

        /**
         * Get analyzed responses
         *
         * @param $requestToken
         *
         * @return mixed
         */
        public function analyzedResponse($requestToken): mixed
        {
            return $this->analyzeRequest->where('request_token',$requestToken)->first();
        }
    }
