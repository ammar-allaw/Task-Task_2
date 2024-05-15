<?php

namespace App\Http\Controllers\API;

use App\Events\EmailVerficationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\VerifiedEmailRequest;
use App\Models\User;
use App\Notifications\EmailVerficationNotification;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;

class UserControlle extends Controller
{
    public function signup(SignupRequest $request){
        $password=$request->password;
        $password_confirmation=$request->password_confirmation;

        if($password ==$password_confirmation){
            if ($request->hasFile('image') && $request->hasFile('certificate') ) {
                    // Store the uploaded file in the storage directory
                $path = $request->file('image')->store('images', 'public');
                $certificatePath = $request->file('certificate')->store('certificates', 'public');

                $user=User::create([
                    'name'=>$request->post('name'),
                    'email'=>$request->post('email'),
                    'phone_number'=>$request->post('phone_number'),
                    'image'=>$path,
                    'username'=>$request->post('username'),
                    'certificate'=>$certificatePath,
                    'password'=>Hash::make($password),
                    'password_confirmation'=>Hash::make($password_confirmation),
                ]);
            }
            
            event(new EmailVerficationEvent($user->id));
            return 'Add User Success';
         }else{
            return response()->json([
                ['Message'=>'Password does not match']
            ],422);
         }
    }

    public function verifiedEmail(VerifiedEmailRequest $request){
        $email=$request->email;
        $user=User::where('email','=',$email)->first();
        if($user){
            if($user->email_verified_at !=null){
                return response()->json([
                    'Message'=>'this email is verified'
                ],422);
            }else{
                $currentDateTime = Carbon::now();
                $user_verfication=User::where('email_verification_otp','=',$request->token)
                ->where('otp_verification_at','>=',$currentDateTime)->first();
                if($user_verfication){
                    $user->email_verified_at=now();
                    $user->save();
                    return response()->json([
                        'Message'=>'Nice verified'
                    ],422); 
                }
                else{
                    return response()->json([
                        'Message'=>'Your verified token time out'
                    ],422); 
                }
            }
        }else{
            return response()->json([
                    'Message'=>'Invalid Email'
                ],422);
        }
    }
    // email, phone number, password
    public function login(LoginRequest $request){
        $email=$request->email;
        $user=User::where('email',$email)
        ->where('phone_number',$request->phone_number)
        ->first();
        if($user){
            if($user->email_verified_at!=null){
                if (Hash::check($request->password, $user->password)) {
                    Auth::guard('sanctum')->user($user);
                    $dive_name = $request->post('dive_name', $request->userAgent());
                    $token = $user->createToken($dive_name,expiresAt:now()->addMinute(1));



                    // $newExpiration = Carbon::now()->addMinutes(config('sanctum.expiration'));
                    // // $new_token=$user->createToken('token-name')->plainTextToken;
                    // $nt = PersonalAccessToken::findToken($token->plainTextToken);
                    // $nt->expires_at = $newExpiration;

                    return response()->json([
                        'token' => $token->plainTextToken,
                        'user'=>$user,
                        // 'expiration'=>$nt->expires_at,
                    ]);
                } else {
                    return response()->json([
                        'Messagee' => 'password dismatch',
                    ],422);
                }
            }else{
                return response()->json([
                    'Messagee' => 'You should verfied youer email',
                ],422);
            }
        }else{
            return response()->json([
                'Messagee' => 'You should Login',
            ],422);
        }
    }

    public function logout(Request $request){
        if($request->user()){
            $request->user()->currentAccessToken()->delete();
            return [
                'message' => 'user logged out'
            ];
        }
    }

    public function refreshToken(Request $request){

        

        ////////////////

        $user = $request->user();
        $accessToken = $user->currentAccessToken();
    
        if (!$accessToken) {
            return response()->json(['error' => 'No access token found'], 401);
        }
    
        $newExpiration = Carbon::now()->addMinutes(config('sanctum.refresh_expiration'));
        $new_token=$user->createToken('token-name',expiresAt:now()->addMinutes(config('sanctum.refresh_expiration')))->plainTextToken;
        Auth::guard('sanctum')->user($user);
        return response()->json([
            'access_token' => $new_token,
            'expires_at' => $newExpiration->toDateTimeString(),
        ]);



        ////
        // $accessToken = $request->user()->currentAccessToken();

        // if (!$accessToken) {
        //     return response()->json(['error' => 'No access token found'], 401);
        // }
    
        // $newExpiration = Carbon::now()->addMinutes(config('sanctum.refresh_expiration'));
        // $accessToken->forceFill([
        //     'expires_at' => $newExpiration,
        // ])->save();
    
        // return response()->json([
        //     'access_token' => $accessToken->plainTextToken,
        //     'expires_at' => $newExpiration->toDateTimeString(),
        // ]);

        /////

        // $accessToken = $request->user()->createToken('access_token', null, Carbon::now()->addMinutes(config('sanctum.refresh_expiration')));

        // return response()->json([
        //     'access_refresh_token' => $accessToken,
        // ]);
    }
}
