<?php

    namespace App\Http\Controllers\Api\EcomBackend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\MediaPlayerBackend\GetDetectionResponseRequest;
    use App\Http\Requests\MediaPlayerBackend\ImageAnalyzerStoreRequest;
    use App\Repositories\EcomBackend\ImageAnalyzer\ImageAnalyzerRepositoryInterface;
    use Illuminate\Http\JsonResponse;

    class ImageAnalyzerController extends Controller
    {
        /**
         * @var ImageAnalyzerRepositoryInterface
         */
        protected ImageAnalyzerRepositoryInterface $imageAnalyzer;

        public function __construct(ImageAnalyzerRepositoryInterface $ImageAnalyzer)
        {
            $this->imageAnalyzer = $ImageAnalyzer;
            parent::__construct();
        }

        /**
         * @param \App\Http\Requests\MediaPlayerBackend\ImageAnalyzerStoreRequest $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function storeImageAnalyzer(ImageAnalyzerStoreRequest $request): JsonResponse
        {
            $data = $this->imageAnalyzer->storeAnalyzeRequest($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }

        /**
         *
         * @param \App\Http\Requests\MediaPlayerBackend\GetDetectionResponseRequest $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function analyzedResponse(GetDetectionResponseRequest $request): JsonResponse
        {
            $data = $this->imageAnalyzer->analyzedResponse($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }
    }
