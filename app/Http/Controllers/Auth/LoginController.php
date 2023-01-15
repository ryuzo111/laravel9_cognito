<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//追加
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

use Ellaisys\Cognito\AwsCognitoClient;

use Exception;
use Illuminate\Validation\ValidationException;
use Ellaisys\Cognito\Exceptions\AwsCognitoException;
use Ellaisys\Cognito\Exceptions\NoLocalUserException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    //TODO オーバーライドして書き換える
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }
    }

    //TODO attemptLoginとlogin失敗時の更新をする

    protected function attemptLogin(Collection $request, string $guard='web', string $paramUsername='email', string $paramPassword='password', bool $isJsonResponse=false)
    {
        try {
            //Get key fields
            $keyUsername = 'email';
            $keyPassword = 'password';
            $rememberMe = $request->has('remember')?$request['remember']:false;

            //Generate credentials array
            $credentials = [
                $keyUsername => $request[$paramUsername],
                $keyPassword => $request[$paramPassword]
            ];

            //Authenticate User
            $claim = Auth::guard($guard)->attempt($credentials, $rememberMe);

        } catch (NoLocalUserException $e) {
            Log::error('AuthenticatesUsers:attemptLogin:NoLocalUserException');

            if (config('cognito.add_missing_local_user_sso')) {
                $response = $this->createLocalUser($credentials, $keyPassword);

                if ($response) {
                    return $response;
                } //End if
            } //End if

            return $this->sendFailedLoginResponse($request, $e, $isJsonResponse, $paramUsername);
        } catch (CognitoIdentityProviderException $e) {
            Log::error('AuthenticatesUsers:attemptLogin:CognitoIdentityProviderException');
            return $this->sendFailedCognitoResponse($e, $isJsonResponse, $paramUsername);
        } catch (Exception $e) {
            Log::error('AuthenticatesUsers:attemptLogin:Exception');
            return $this->sendFailedLoginResponse($request, $e, $isJsonResponse, $paramUsername);
        } //Try-catch ends

        return $claim;
    } 
}
