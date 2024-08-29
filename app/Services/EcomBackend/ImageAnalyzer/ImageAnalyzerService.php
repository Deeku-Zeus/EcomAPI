<?php

    namespace App\Services\EcomBackend\ImageAnalyzer;

    use App\Dao\EcomBackend\AnalyzeRequestDao;
    use App\Dao\EcomBackend\AnalyzeResponseDao;
    use App\Dao\EcomBackend\UserProfileDao;
    use App\Services\CommonCrypt;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Log;
    use Throwable;

    class ImageAnalyzerService
    {
        /**
         * @var \App\Dao\EcomBackend\UserProfileDao
         */
        protected UserProfileDao $userProfileDao;
        /**
         * @var \App\Dao\EcomBackend\AnalyzeRequestDao
         */
        protected AnalyzeRequestDao  $analyzerRequestDao;
        protected AnalyzeResponseDao $analyzedResponseDao;

        /**
         * ImageAnalyzerService constructor.E
         *
         * @param \App\Dao\EcomBackend\UserProfileDao     $UserProfileDao
         * @param \App\Dao\EcomBackend\AnalyzeRequestDao  $AnalyzeRequestDao
         * @param \App\Dao\EcomBackend\AnalyzeResponseDao $AnalyzeResponseDao
         */
        public function __construct(
            UserProfileDao $UserProfileDao,
            AnalyzeRequestDao $AnalyzeRequestDao,
            AnalyzeResponseDao $AnalyzeResponseDao
        )
        {
            $this->userProfileDao = $UserProfileDao;
            $this->analyzerRequestDao = $AnalyzeRequestDao;
            $this->analyzedResponseDao = $AnalyzeResponseDao;
        }

        /**
         * Store Analyzer Request
         *
         * @param $request
         *
         * @return array
         */
        public function storeAnalyzeRequest($request): array
        {
            $result = true;
            $response = [
                "result"  => $result,
                "status"  => "success",
                "message" => "Request saved successfully"
            ];
            $request = collect($request);
            try {
                $profileid = $this->userProfileDao->fetchUserProfileId(collect($request));
                if (!$profileid) {
                    $result = false;
                    $response['result'] = $result;
                    $response['status'] = "failed";
                    $response['message'] = "User Profile data not found";
                }
                if ($result) {
                    $upsertData = [
                        "user_profile_id" => $profileid,
                        "image"           => $request->get('image', null),
                        "videoName"       => $request->get('videoName', null),
                        "timestamp"       => $request->get('timestamp', null),
                        "request_token"   => $request->get('request_token', null),
                        "is_analyzed"     => $request->get('is_analyzed', false)
                    ];
                    $this->analyzerRequestDao->storeAnalyzeRequest($upsertData);
                }
            } catch (Throwable $th) {
                Log::error($th);
                $result = false;
                $response['result'] = $result;
                $response['status'] = "failed";
                $response['message'] = "Some error has occurred during saving the data";
            }
            return $response;
        }

        /**
         * @param $request
         *
         * @return array
         */
        public function analyzedResponse($request): array
        {
            try {
                $request = collect($request);
                $requestToken = $request->get('request_token');
                $uid = $request->get('uid', []);

                // Check if the request token is provided
                if (!$requestToken) {
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "Request token is not provided",
                        "data"    => []
                    ];
                }

                $analyzeRequest = $this->analyzerRequestDao->getRequestIds($requestToken);

                if (!$analyzeRequest) {
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "Analysis request not found",
                        "data"    => []
                    ];
                }

                $image = pathinfo($analyzeRequest->image, PATHINFO_FILENAME);
                $analyzedData = $analyzeRequest->analyzeResponse()
                    ->when(!empty($uid), function ($query) use ($uid) {
                        return $query->whereIn('uid', $uid);
                    })
                    ->get();

                if ($analyzedData->isEmpty()) {
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "Analysis response not found",
                        "data"    => []
                    ];
                }

                $common = new CommonCrypt(env('COMMON_CRYP_KEY'));
                $data = $analyzedData->map(function ($analyzeResponse) use ($common, $image) {
                    return [
                        'coordinates' => json_decode(base64_decode($common->decrypt($analyzeResponse->coordinates))),
                        'confidence'  => $analyzeResponse->confidence,
                        'tags'        => json_decode(base64_decode($common->decrypt($analyzeResponse->tags))),
                        'uid'         => $analyzeResponse->uid,
                        'color'       => $analyzeResponse->color,
                        'image'       => $image
                    ];
                });

                return [
                    "result"  => true,
                    "status"  => "success",
                    "message" => "Analyze response fetched successfully",
                    "data"    => ['analyzed_response' => $data->toArray()]
                ];
            } catch (Throwable $th) {
                Log::error($th);
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "Some error has occurred during fetching the data",
                    "data"    => []
                ];
            }
        }

        /**
         * @param $request
         *
         * @return array
         *
         * @throws \Exception
         */
        public function StoreAnalyzedResponse($request): array
        {
            if (empty($request)) {
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "Request is empty",
                ];
            }
            foreach ($request as $item) {
                $item = collect($item);
                $requestToken = $item->get('request_token');

                // Validate request token
                if (!$requestToken) {
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "Request token is not provided",
                    ];
                }

                $analyzeRequestId = $this->analyzerRequestDao->getAnalyzeRequestId($requestToken);

                // Check if analyze request ID is valid
                if ($analyzeRequestId) {
                    $common = new CommonCrypt(env('COMMON_CRYP_KEY'));

                    // Prepare data for upsert
                    $upsertData = [
                        'coordinates'        => $common->encrypt($item->get('coordinates')),
                        'confidence'         => $item->get('confidence',""),
                        'uid'                => $item->get('uid'),
                        'color'              => $item->get('color',""),
                        'tags'               => $common->encrypt($item->get('tags')),
                        'analyze_request_id' => $analyzeRequestId
                    ];

                    // Perform the upsert operation
                    $this->analyzerRequestDao->upsertAnalyzedResponse($upsertData);
                }
            }

            return [
                "result"  => true,
                "status"  => "success",
                "message" => "Request saved successfully",
            ];

        }

        /**
         * @param $request
         *
         * @return array
         *
         * @throws \Exception
         */
        public function UpdateAnalyzeData($request): array
        {
            try {
                $request = collect($request);
                $uid = $request->get('uid');
                if (!$uid) {
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "UID is not provided",
                    ];
                }
                $responseId = $this->analyzedResponseDao->getResponseIdByUid($uid);
                if (!$responseId){
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "No response found for the given UID",
                    ];
                }
                $tags = $request->get('tags');
                $color = $request->get('color');
                if (empty($tags) && empty($color)) {
                    return [
                        "result"  => false,
                        "status"  => "failed",
                        "message" => "Nothing to update",
                    ];
                }
                $updateData = collect();
                $common = new CommonCrypt(env('COMMON_CRYP_KEY'));
                if (!empty($tags)) {
                    $updateData->put('tags', $common->encrypt($tags));
                }
                if (!empty($color)) {
                    $updateData->put('color', $color);
                }
                $this->analyzedResponseDao->updateAnalyzedResponse($responseId,$updateData->toArray());
                return [
                    "result"  => true,
                    "status"  => "success",
                    "message" => "Data updated successfully",
                ];
            }
            catch (Throwable $th){
                Log::error($th);
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "Some error has occurred during updating the data",
                ];
            }
        }

        /**
         * @param $request
         *
         * @return array
         * @throws \Exception
         */
        public function getResponseHistory($request):array
        {
            $request = collect($request);
            $videoName = $request->get('videoName');
            if (!$videoName){
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "Video Name is not provided",
                ];
            }
            $page = $request->get('page',1);
            $analyzeRequest = $this->analyzerRequestDao->getRequestIdByVideoName($videoName);
            if (!$analyzeRequest) {
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "Analysis request not found",
                    "data"    => []
                ];
            }
            $imgArray = [];
            foreach ($analyzeRequest as $item){
                $imgArray[$item->id] = pathinfo($item->image, PATHINFO_FILENAME);
            }
            $analyzeRequestId = $analyzeRequest->pluck('id');
            if (!$analyzeRequestId){
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "No histories found for the video name",
                ];
            }
            $analyzedData = $this->analyzedResponseDao->getResponseHistories($analyzeRequestId,$page);
            if ($analyzedData->isEmpty()) {
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "Analysis response not found",
                    "data"    => []
                ];
            }

            $common = new CommonCrypt(env('COMMON_CRYP_KEY'));
            $data = $analyzedData->map(function ($analyzeResponse) use ($common, $imgArray) {
                $reqId = $analyzeResponse->analyze_request_id;
                return [
                    'coordinates' => json_decode(base64_decode($common->decrypt($analyzeResponse->coordinates))),
                    'confidence'  => $analyzeResponse->confidence,
                    'tags'        => json_decode(base64_decode($common->decrypt($analyzeResponse->tags))),
                    'uid'         => $analyzeResponse->uid,
                    'color'       => $analyzeResponse->color,
                    'image'       => $imgArray[$reqId] ?? ""
                ];
            });

            return [
                "result"  => true,
                "status"  => "success",
                "message" => "Analyze response fetched successfully",
                "data"    => ['analyzed_response' => $data->toArray()]
            ];
        }
    }
