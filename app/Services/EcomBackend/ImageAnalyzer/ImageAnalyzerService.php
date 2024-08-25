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
                "result"=>$result,
                "status"=>"success",
                "message"=>"Request saved successfully"
            ];
            $request = collect($request);
            try {
                $profileid = $this->userProfileDao->fetchUserProfileId(collect($request));
                if (!$profileid){
                    $result = false;
                    $response['result'] = $result;
                    $response['status'] = "failed";
                    $response['message'] = "User Profile data not found";
                }
                if ($result){
                    $upsertData = [
                        "profileId"=>$profileid,
                        "image"=>$request->get('image',null),
                        "videoName"=>$request->get('videoName',null),
                        "timestamp"=>$request->get('timestamp',null),
                        "request_token"=>$request->get('request_token',null),
                        "is_analyzed"=>$request->get('is_analyzed',false)
                    ];
                    $this->analyzerRequestDao->storeAnalyzeRequest($upsertData);
                }
            }
            catch (Throwable $th){
                Log::error($th);
                $result = false;
                $response['result'] = $result;
                $response['status'] = "failed";
                $response['message'] = "Some error has occurred during saving the data";
            }
            return $response;
        }
    }
