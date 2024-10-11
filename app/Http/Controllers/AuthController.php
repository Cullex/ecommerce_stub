<?php

namespace App\Http\Controllers;
use App\Events\PasswordUpdatedEvent;
use App\Jobs\SendEmailResetToken;
use App\Jobs\SendLoginAlert;
use App\Jobs\SendWelcomeName;
use App\Mail\SendWelcomeEMail;
use App\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function registerView()
    {
        return view('auth.register');
    }


    public function obfuscate_email($email)
    {
        $em   = explode("@",$email);
        $name = implode('@',array_slice($em, 0, count($em)-1));
        $len  = floor(strlen($name)/2);

        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
    }

    public function password(Request $request)
    {
        $request->validate([
            'old_password' => ['required','current_password'],
            'password' => ['required', 'string', 'min:8', 'max:20' ,  'confirmed'],
        ],[
            'old_password.current_password' => 'Your old password is wrong'
        ]);

        /** @var User $user */
        $user = auth()->user();

        $user->update([
            'password_reset' => null,
            'password' => Hash::make($request->get('password'))
        ]);

        return api()->message('Password Updated')->build();

    }

    public function maskPhoneNumber($number)
    {
        return str_repeat("*", strlen($number)-4) . substr($number, -4);
    }


    public function login(Request $request)
    {

        $request->validate([
            'email' => [ 'required' , 'email'],
            'password' => 'required',
        ]);

        /** @var User $user */
        $user = User::query()->where('email' , '=' , $request->get('email'))->first();


        // Check if User Exists

        if ($user)
        {
            // Check if user password matches

            if (Hash::check($request->get('password'), $user->password))
            {
                Auth::loginUsingId($user->id);
                Auth::logoutOtherDevices($request->get('password'));
                $this->dispatch(new SendLoginAlert($user->email , $user->name));
                return  api()->success(true)->build('Welcome Back , ' . $user->name );
            }
        }

        return api()->success(false)->build('Your login credentials are incorrect');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function getResetForm()
    {
       return view('auth.reset');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => [ 'required' , 'email'],
        ]);

        /** @var User $user */
        $user = User::query()->where('email' , '=' , $request->get('email'))->first();

        if ($user) {
            $msisdn = $user->msisdn;
                $reset = random_int(100000 , 999999);
                $message = 'Your password reset Token Sent to ' . $this->obfuscate_email($user->email);
                $message_code =  'Your password reset Token is ' . $reset;
                $user->update([
                    'password_reset_token' => Hash::make($reset),
                    'password_reset_token_expiry' => now()->addHour()
                ]);

                $this->dispatch(new SendEmailResetToken($user->email , $reset));
                return api()->success(true)->build($message);

        }

        return api()->success(false)->build('Invalid Credentials');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => [ 'required' , 'email'],
            'token' => 'required',
        ]);

        /** @var User $user */
        $user = User::query()->where('email' , '=' , $request->get('email'))->first();

        // Check if User Exists

        if ($user)
        {
            // check if user otp matches

            if (Hash::check($request->get('token'), $user->password_reset_token))
            {
                // check if otp is still valid

                if ($user->password_reset_token_expiry >= now())
                {
                    $request->validate([
                        'password' => ['required', 'string', 'min:8', 'max:20' ,  'confirmed']
                    ]);

                    $user->update([
                        'password' => Hash::make($request->get('password')),
                        'password_reset' => null
                    ]);
                    return  api()->success(true)->build('Password Changed Successfully');
                }

                return api()->success(false)->build('Token expired , request new Token');
            }
        }

        return api()->success(false)->build('Invalid Email or Token');
    }

    public function getChangeForm()
    {
        return view('auth.change');
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'msisdn' => 'required|starts_with:263',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        try {
            $user = User::create([
                'name' => $request->get('name'),
                'last_name' => $request->get('last_name'),
                'msisdn' => $request->get('msisdn'),
                'address' =>$request->get('address'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->password),
            ]);

            $this->dispatch(new SendWelcomeName($user->email, $user->name));

            return api()->success(true)->data('user', $user)->build('Registration successful.');

        } catch (\Exception $e) {
            return api()->success(false)->message($e)->build($e->getMessage());
        }
    }


    public function initiateSmsAlert($msisdn, $message){

        $client = new Client([
            'base_uri' =>env('SMS_URL'),
            'headers' => [
                'Authorization' => env('AUTHORIZATION_KEY'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $response = $client->request(
            'POST',
            'sms/2/text/advanced',
            [
                RequestOptions::JSON => [
                    'messages' => [
                        [
                            'from' => env('SMS_SENDER_ID'),
                            'destinations' => [
                                ['to' => $msisdn]
                            ],
                            'text' => $message,
                        ]
                    ]
                ],
            ]
        );

        echo("HTTP code: " . $response->getStatusCode() . PHP_EOL);
        echo("Response body: " . $response->getBody()->getContents() . PHP_EOL);

    }

}
