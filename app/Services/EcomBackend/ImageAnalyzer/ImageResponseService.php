<?php

    namespace App\Services\EcomBackend\ImageAnalyzer;

    use App\Dao\EcomBackend\AnalyzeRequestDao;
    use App\Dao\EcomBackend\AnalyzeResponseDao;
    use App\Dao\EcomBackend\UserProfileDao;
    use App\Services\CommonCrypt;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Storage;
    use Throwable;

    class ImageResponseService
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
         * @param $request
         *
         * @return array
         * @throws \Exception
         */
        public function getUserRequests($request):array
        {
            $userRequests = $this->analyzerRequestDao->getUserRequests();
            if ($userRequests->isEmpty()) {
                return [
                    "result"  => false,
                    "status"  => "failed",
                    "message" => "No Requests are found",
                    "data"    => []
                ];
            }
            $data = $userRequests->map(function ($userRequest)  {
                return [
                    'image'=> pathinfo($userRequest->image, PATHINFO_FILENAME),
                    'videoName'=>$userRequest->videoName,
                    'timestamp'=>$userRequest->timestamp,
                    'request_token'=>$userRequest->request_token,
                ];
            });

            return [
                "result"  => true,
                "status"  => "success",
                "message" => "User requests fetched successfully",
                "data"    => $data->toArray()
            ];
        }
    }
