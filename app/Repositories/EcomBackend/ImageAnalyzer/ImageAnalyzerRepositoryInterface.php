<?php

    namespace App\Repositories\EcomBackend\ImageAnalyzer;
    /*
     * ImageAnalyzerRepositoryInterface
     */

    interface ImageAnalyzerRepositoryInterface
    {
        /**
         * @param array $request
         *
         * @return array
         */
        public function storeAnalyzeRequest(array $request): array;

        /**
         * @param array $request
         *
         * @return array
         */
        public function analyzedResponse(array $request): array;

        /**
         * @param array $request
         *
         * @return array
         */
        public function StoreAnalyzedResponse(array $request): array;

        /**
         * @param array $request
         *
         * @return array
         */
        public function UpdateAnalyzeData(array $request): array;

        /**
         * @param array $request
         *
         * @return array
         */
        public function getResponseHistory(array $request): array;
    }
