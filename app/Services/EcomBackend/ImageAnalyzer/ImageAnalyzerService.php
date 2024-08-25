<?php

    namespace App\Services\EcomBackend\ImageAnalyzer;

    use App\Dao\EcomBackend\AnalyzeRequestDao;
    use App\Dao\EcomBackend\UserProfileDao;
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
        protected AnalyzeRequestDao $analyzerRequestDao;

        /**
         * ImageAnalyzerService constructor.E
         *
         * @param \App\Dao\EcomBackend\UserProfileDao    $UserProfileDao
         * @param \App\Dao\EcomBackend\AnalyzeRequestDao $AnalyzeRequestDao
         */
        public function __construct(
            UserProfileDao $UserProfileDao,
            AnalyzeRequestDao $AnalyzeRequestDao
        )
        {
            $this->userProfileDao = $UserProfileDao;
            $this->analyzerRequestDao = $AnalyzeRequestDao;
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
                        "user_profile_id"     => $profileid,
                        "image"         => $request->get('image', null),
                        "videoName"     => $request->get('videoName', null),
                        "timestamp"     => $request->get('timestamp', null),
                        "request_token" => $request->get('request_token', null),
                        "is_analyzed"   => $request->get('is_analyzed', false)
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
            $result = true;
            $response = [
                "result"  => $result,
                "status"  => "success",
                "message" => "Request saved successfully",
                "data"    => []
            ];
            $request = collect($request);
            $requestToken = $request->get('request_token');
            if (!$requestToken) {
                $result = false;
                $response['result'] = $result;
                $response['status'] = "failed";
                $response['message'] = "Request token is not provided";
            }
            try {
                $analyzedData = $this->analyzerRequestDao->analyzedResponse($requestToken);
                $responseData = collect();
                $data = [];
                foreach ($analyzedData->analyzeResponse as $analyzeResponse){
                    $analyzedObj = collect();
                    $analyzedObj->put('is_classified',$analyzeResponse->is_classified);
                    $analyzedObj->put('coordinates',$analyzeResponse->coordinates);
                    $analyzedObj->put('confidence',$analyzeResponse->confidence);
                    $analyzedObj->put('object',$analyzeResponse->object);
                    $analyzedObj->put('uid',$analyzeResponse->uid);
                    $data[] = $analyzedObj;
                }
                $responseData->put('is_analyzed',$analyzedData->is_analyzed);
                $responseData->put('timestamp',$analyzedData->timestamp);
                $responseData->put('videoName',$analyzedData->videoName);
                $responseData->put('request_token',$analyzedData->request_token);
                $responseData->put('timestamp',$analyzedData->timestamp);
                $responseData->put('analyzed_response',$data);
                $response['data'] = $responseData->toArray();
            } catch (Throwable $th) {
                Log::error($th);
                $result = false;
                $response['result'] = $result;
                $response['status'] = "failed";
                $response['message'] = "Some error has occurred during fetching the data";
            }
            return $response;
        }
    }
