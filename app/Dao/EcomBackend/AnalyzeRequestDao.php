<?php

    namespace App\Dao\EcomBackend;

    use App\Models\EcomBackend\AnalyzeRequest;
    use App\Models\EcomBackend\AnalyzeResponse;

    class AnalyzeRequestDao
    {
        /**
         * @var \App\Models\EcomBackend\AnalyzeRequest
         */
        protected AnalyzeRequest $analyzeRequest;
        protected AnalyzeResponse $analyzedResponse;

        /**
         * AnalyzeRequestDao constructor.
         *
         * @param \App\Models\EcomBackend\AnalyzeRequest $analyzeRequestObj
         */
        public function __construct(
            AnalyzeRequest $analyzeRequestObj,
            AnalyzeResponse $analyzedResponseObj
        )
        {
            $this->analyzeRequest = $analyzeRequestObj;
            $this->analyzedResponse = $analyzedResponseObj;
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
         * @param string $requestToken
         *
         * @param array  $uid
         *
         * @return mixed
         */
        public function getRequestIds(string $requestToken): mixed
        {
            return $this->analyzeRequest->select(['id','image'])->where('request_token',$requestToken)->first();
        }

        /**
         * Get analyzed responses
         *
         * @param $requestToken
         *
         * @return mixed
         */
        public function getAnalyzeRequestId($requestToken): mixed
        {
            return $this->analyzeRequest->where('request_token',$requestToken)->get()->pluck('id')->first();
        }

        /**
         * @param $upsertData
         *
         * @return mixed
         */
        public function upsertAnalyzedResponse($upsertData){
            return $this->analyzedResponse->UpdateOrCreate(
                [
                    'analyze_request_id' => $upsertData['analyze_request_id'],
                    'uid'=> $upsertData['uid']
                ],
                $upsertData
            );
        }

        /**
         * Get response id
         *
         * @param string $videoName
         *
         * @return mixed
         */
        public function getRequestIdByVideoName(string $videoName): mixed
        {
            return $this->analyzeRequest->where('videoName',$videoName)->first();
        }
    }
