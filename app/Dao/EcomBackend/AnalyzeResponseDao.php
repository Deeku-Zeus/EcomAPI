<?php

    namespace App\Dao\EcomBackend;

    use App\Models\EcomBackend\AnalyzeRequest;
    use App\Models\EcomBackend\AnalyzeResponse;

    class AnalyzeResponseDao
    {
        /**
         * @var \App\Models\EcomBackend\AnalyzeRequest
         */
        protected AnalyzeRequest $analyzeRequest;
        protected AnalyzeResponse $analyzedResponse;

        /**
         * AnalyzeRequestDao constructor.
         *
         * @param \App\Models\EcomBackend\AnalyzeRequest  $analyzeRequestObj
         * @param \App\Models\EcomBackend\AnalyzeResponse $analyzedResponseObj
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
         * Get analyzed responses
         *
         * @param string $requestId
         * @param array  $uid
         *
         * @return mixed
         */
        public function analyzedResponse(string $requestId,array $uid): mixed
        {
            $query = $this->analyzedResponse->where('analyze_request_id',$requestId);
            if (!empty($uid)){
                $query->whereIn('uid',$uid);
            }
            return $query->get();
        }
        /**
         * Get response id
         *
         * @param array  $uid
         *
         * @return mixed
         */
        public function getResponseIdByUid(string $uid): mixed
        {
            return $this->analyzedResponse->where('uid',$uid)->first()->id ?? null;
        }

        /**
         * Get response id
         *
         * @param string $id
         * @param array  $updateData
         *
         * @return mixed
         */
        public function updateAnalyzedResponse(string $id,array $updateData): mixed
        {
            return $this->analyzedResponse->whereId($id)->update($updateData);
        }
    }
