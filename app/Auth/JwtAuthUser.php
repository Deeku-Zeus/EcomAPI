<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class JwtAuthUser implements Authenticatable, JWTSubject
{
    /**
     * Connect Service Name
     * @var string
     */
    protected $serviceName;

    /**
     * Get Url Access Permission
     * @var int
     */
    protected $getPermission = 0;

    /**
     * Put Url Access Permission
     * @var int
     */
    protected $putPermission = 0;

    /**
     * Delete Url Access Permission
     * @var int
     */
    protected $delPermission = 0;


    /**
     * Construct
     *
     * @param array $credentials This value will be input from AuthController
     *
     * @return void
     */
    public function __construct(array $credentials)
    {
        $this->serviceName = $credentials['service_name'];
        $this->getPermission = $credentials['get_permission'] ?? config('const.flag.off');
        $this->putPermission = $credentials['put_permission'] ?? config('const.flag.off');
        $this->delPermission = $credentials['del_permission'] ?? config('const.flag.off');
    }

    /**
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // Return the name of unique identifier for the user (e.g. "id")
    }

    /**
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // Return the unique identifier for the user (e.g. their ID, 123)
    }

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        // Returns the (hashed) password for the user
    }

    /**
     * @return string
     */
    public function getRememberToken()
    {
        // Return the token used for the "remember me" functionality
    }

    /**
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Store a new token user for the "remember me" functionality
    }

    /**
     * @return string
     */
    public function getRememberTokenName()
    {
        // Return the name of the column / attribute used to store the "remember me" token
    }

    /**
     * Get JWT Identifier
     *
     * @return string
     */
    public function getJWTIdentifier(): string
    {
        return $this->serviceName;
    }

    /**
     * Get JWT Custom Claims
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        // JWT トークンに埋め込む追加の情報を返す
        return [
            'service_name' => $this->serviceName,
            'get_permission' => $this->getPermission,
            'put_permission' => $this->putPermission,
            'del_permission' => $this->delPermission,
            'authorized' => true,
        ];
    }

    public function getAuthPasswordName()
    {
        // TODO: Implement getAuthPasswordName() method.
    }
}
