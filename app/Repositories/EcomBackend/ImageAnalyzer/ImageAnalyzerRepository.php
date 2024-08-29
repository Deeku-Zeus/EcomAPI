<?php

    namespace App\Repositories\EcomBackend\ImageAnalyzer;

    use App\Services\EcomBackend\ImageAnalyzer\ImageAnalyzerService;

    class ImageAnalyzerRepository implements ImageAnalyzerRepositoryInterface
    {
        /**
         * @var ImageAnalyzerService
         */
        protected ImageAnalyzerService $imageAnalyzerService;

        /**
         * ImageAnalyzerRequestExecRepository constructor.
         *
         * @param ImageAnalyzerService $analyzerService
         */
        public function __construct(ImageAnalyzerService $analyzerService)
        {
            $this->imageAnalyzerService = $analyzerService;
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
    }
