<?php

    namespace App\Http\Controllers\Api\EcomBackend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\MediaPlayerBackend\GetAnalyzedDataHistoryRequest;
    use App\Http\Requests\MediaPlayerBackend\GetDetectionResponseRequest;
    use App\Http\Requests\MediaPlayerBackend\GetEcomProductsRequest;
    use App\Http\Requests\MediaPlayerBackend\ImageAnalyzerStoreRequest;
    use App\Http\Requests\MediaPlayerBackend\StoreDetectionResponseRequest;
    use App\Http\Requests\MediaPlayerBackend\UpdateAnalyzeDataRequest;
    use App\Repositories\EcomBackend\ImageAnalyzer\ImageAnalyzerRepositoryInterface;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;

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

        /**
         *
         * @param \App\Http\Requests\MediaPlayerBackend\StoreDetectionResponseRequest $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function StoreAnalyzedResponse(StoreDetectionResponseRequest $request): JsonResponse
        {
            $data = $this->imageAnalyzer->StoreAnalyzedResponse($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }

        /**
         * @param \App\Http\Requests\MediaPlayerBackend\UpdateAnalyzeDataRequest $request
         *
         * @return mixed
         */
        public function UpdateAnalyzeData(UpdateAnalyzeDataRequest $request){
            $data = $this->imageAnalyzer->UpdateAnalyzeData($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }

        /**
         * @param \App\Http\Requests\MediaPlayerBackend\GetAnalyzedDataHistoryRequest $request
         *
         * @return mixed
         */
        public function getResponseHistory(GetAnalyzedDataHistoryRequest $request){
            $data = $this->imageAnalyzer->getResponseHistory($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }
        public function getEcomProducts(GetEcomProductsRequest $request){
            $data = $this->imageAnalyzer->getEcomProducts($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }
        public function getUserRequests(Request $request){
            $data = $this->imageAnalyzer->getUserRequests($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }
    }
