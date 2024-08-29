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
        protected AnalyzeRequestDao $analyzerRequestDao;
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
                "message" => "No response found",
                "data"    => []
            ];
            $request = collect($request);
            $requestToken = $request->get('request_token');
            $uid = $request->get('uid',[]);
            if (!$requestToken) {
                $result = false;
                $response['result'] = $result;
                $response['status'] = "failed";
                $response['message'] = "Request token is not provided";
            }
            try {
                $analyzeRequest = $this->analyzerRequestDao->getRequestIds($requestToken);
                if (!(isset($analyzeRequest[0]) && !empty($analyzeRequest[0]))){
                    $result = false;
                    $response['result'] = $result;
                    $response['status'] = "failed";
                    $response['message'] = "Analysis request not found";
                }
                if ($result){
                    $analyzedData = $this->analyzedResponseDao->analyzedResponse($analyzeRequest[0],$uid);
                    if ($analyzedData->isNotEmpty()){
                        $common = new CommonCrypt(env('COMMON_CRYP_KEY'));
                        $responseData = collect();
                        $data = [];
                        foreach ($analyzedData as $analyzeResponse){
                            $analyzedObj = collect();
                            $analyzedObj->put('coordinates',json_decode(base64_decode($common->decrypt($analyzeResponse->coordinates))));
                            $analyzedObj->put('confidence',$analyzeResponse->confidence);
                            $analyzedObj->put('tags',json_decode(base64_decode($common->decrypt($analyzeResponse->tags))));
                            $analyzedObj->put('uid',$analyzeResponse->uid);
                            $analyzedObj->put('color',$analyzeResponse->color);
                            $data[] = $analyzedObj;
                        }
                        $responseData->put('analyzed_response',$data);
                        $response['message'] = "Analze response fetched successfully";
                        $response['data'] = $responseData->toArray();
                    }
                    else{
                        $result = false;
                        $response['result'] = $result;
                        $response['status'] = "failed";
                        $response['message'] = "Analysis response not found";
                    }
                }
            } catch (Throwable $th) {
                Log::error($th);
                $result = false;
                $response['result'] = $result;
                $response['status'] = "failed";
                $response['message'] = "Some error has occurred during fetching the data";
            }
            return $response;
        }

        /**
         * @param $request
         *
         * @throws \Exception
         */
        public function StoreAnalyzedResponse($request)
        {
            $result = true;
            $response = [
                "result"  => $result,
                "status"  => "success",
                "message" => "Request saved successfully",
                "data"    => []
            ];
            foreach ($request as $item) {
                $item = collect($item);
                $requestToken = $item->get('request_token');
                if (!$requestToken) {
                    $result = false;
                    $response['result'] = $result;
                    $response['status'] = "failed";
                    $response['message'] = "Request token is not provided";
                }
                $analyzeRequestId = $this->analyzerRequestDao->getAnalyzeRequestId($requestToken);
                if ($analyzeRequestId){
                    $common = new CommonCrypt(env('COMMON_CRYP_KEY'));
                    $upsertData = collect();
                    $upsertData->put('coordinates',$common->encrypt($item->get('coordinates')));
                    $upsertData->put('confidence',$item->get('confidence'));
                    $upsertData->put('uid',$item->get('uid'));
                    $upsertData->put('color',$item->get('color'));
                    $upsertData->put('tags',$common->encrypt($item->get('tags')));
                    $upsertData->put('analyze_request_id',$analyzeRequestId);
                    $this->analyzerRequestDao->upsertAnalyzedResponse($upsertData->toArray());
                }
            }
            return $response;
        }
    }
