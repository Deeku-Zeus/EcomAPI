<?php

    namespace App\Dao\EcomBackend;

    use App\Models\EcomBackend\AnalyzeRequest;
    use App\Models\EcomBackend\User;
    use App\Models\EcomBackend\UserProfile;
    use Illuminate\Support\Collection;

    class UserProfileDao
    {
        /**
         * @var \App\Models\EcomBackend\User
         */
        protected User $user;
        /**
         * @var \App\Models\EcomBackend\UserProfile
         */
        protected UserProfile $userProfile;

        /**
         * AnalyzeRequestDao constructor.
         *
         * @param \App\Models\EcomBackend\User        $userModel
         * @param \App\Models\EcomBackend\UserProfile $userProfileModel
         */
        public function __construct(
            User $userModel,
            UserProfile $userProfileModel
        )
        {
            $this->user = $userModel;
            $this->userProfile = $userProfileModel;
        }

        /**
         * Upsert the request data to database
         *
         * @param \Illuminate\Support\Collection $user
         */
        public function fetchUserProfileId(Collection $user)
        {
            return $this->userProfile->where('profile_name',$user->get('profile_name'))->get('id')->pluck('id')->first();
        }
    }
