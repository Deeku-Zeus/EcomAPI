<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware as AuthBaseMiddleware;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthenticate extends AuthBaseMiddleware
{
    /**
     * Handle an incoming request.
     * Attempt to authenticate a user via the token in the request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $result = false;

        try {
            $this->authenticateCustom($request);
        } catch (UnauthorizedHttpException $e) {
            $msg = "Failed to authenticate with jwt!";
            Log::warning($msg . $e->getMessage());
            return $this->sendResponse($result, $msg, 401);
        } catch (\Exception $e) {
            $msg = "Error occurred during authenticating!";
            Log::warning($msg . $e->getMessage());
            return $this->sendResponse($result, $msg, 500);
        }

        return $next($request);
    }

    /**
     * authenticate.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return void
     */
    private function authenticateCustom($request)
    {
        $this->checkForToken($request);

        try {
            if ($this->auth->parseToken()->authenticate()) {
                // check token payload after authenticate.
                $key = $this->auth->payload();

                if ($key['service_name'] === config('app.utAppName')) {
                    return;
                }

                // Future plans check Read Write Authority From PayLoad
                if ($key['get_permission'] === config('const.flag.off') && (str_contains($request->url(), '/get/'))) {
                    throw new UnauthorizedHttpException('jwt-auth', 'This Token can not access get url.');
                }

                if ($key['put_permission'] === config('const.flag.off') && (str_contains($request->url(), '/put/'))) {
                    throw new UnauthorizedHttpException('jwt-auth', 'This Token can not access put url.');
                }

                if ($key['del_permission'] === config('const.flag.off') && (str_contains($request->url(), '/delete/'))) {
                    throw new UnauthorizedHttpException('jwt-auth', 'This Token can not access delete url.');
                }
            } else {
                throw new UnauthorizedHttpException('jwt-auth', 'User not found');
            }
        } catch (JWTException $e) {
            // include expire time error
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }
    }

    /**
     * send response directly.
     *
     * @param mixed $msg
     * @param string $msg
     * @param int $code (http response code)
     *
     * @return \Illuminate\Container\Container|mixed|object
     */
    public function sendResponse($isAuth, string $msg, int $code)
    {
        return response([
            'result' => $isAuth ? 'SUCCESS' : 'FAILURE',
            'message' => $msg,
        ], $code);
    }
}
