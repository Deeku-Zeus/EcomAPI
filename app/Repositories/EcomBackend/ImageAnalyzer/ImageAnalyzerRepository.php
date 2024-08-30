<?php

    namespace App\Repositories\EcomBackend\ImageAnalyzer;

    use App\Services\EcomBackend\ImageAnalyzer\{ImageAnalyzerService,ImageResponseService};

    class ImageAnalyzerRepository implements ImageAnalyzerRepositoryInterface
    {
        /**
         * @var ImageAnalyzerService
         */
        protected ImageAnalyzerService $imageAnalyzerService;
        protected ImageResponseService $imageResponseService;

        /**
         * ImageAnalyzerRequestExecRepository constructor.
         *
         * @param ImageAnalyzerService $analyzerService
         */
        public function __construct(
            ImageAnalyzerService $analyzerService,
            ImageResponseService $responseService
        )
        {
            $this->imageAnalyzerService = $analyzerService;
            $this->imageResponseService = $responseService;
        }

        /**
         * @param array $request
         *
         * @return array
         */
        public function storeAnalyzeRequest(array $request): array
        {
            return $this->imageAnalyzerService->storeAnalyzeRequest($request);
        }

        /**
         * @param array $request
         *
         * @return array
         */
        public function analyzedResponse(array $request): array
        {
            return $this->imageAnalyzerService->analyzedResponse($request);
        }

        /**
         * @param array $request
         *
         * @return array
         *
         * @throws \Exception
         */
        public function StoreAnalyzedResponse(array $request): array
        {
            return $this->imageAnalyzerService->StoreAnalyzedResponse($request);
        }

        /**
         * @param array $request
         *
         * @return array
         */
        public function UpdateAnalyzeData(array $request): array
        {
            return $this->imageAnalyzerService->UpdateAnalyzeData($request);
        }

        /**
         * @param array $request
         *
         * @return array
         */
        public function getResponseHistory(array $request): array
        {
            return $this->imageAnalyzerService->getResponseHistory($request);
        }

        public function getEcomProducts(array $request): array
        {
            return $this->imageAnalyzerService->getEcomProducts($request);
        }
        public function getUserRequests(array $request): array
        {
            return $this->imageResponseService->getUserRequests($request);
        }
    }
