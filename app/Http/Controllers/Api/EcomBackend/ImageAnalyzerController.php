<?php

    namespace App\Http\Controllers\Api\EcomBackend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\MediaPlayerBackend\ImageAnalyzerStoreRequest;
    use App\Repositories\EcomBackend\ImageAnalyzer\ImageAnalyzerRepositoryInterface;

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
        public function storeImageAnalyzer(ImageAnalyzerStoreRequest $request)
        {
            $data = $this->imageAnalyzer->storeAnalyzeRequest($request->toArray());
            return response()->json(
                $data,
                empty($data) ? 204 : 200
            );
        }
    }
