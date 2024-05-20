<?php

namespace App\Http\Controllers\API;

use App\Events\EmailVerficationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\VerifiedEmailRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function signup(SignupRequest $request){
        $password=$request->password;
        $password_confirmation=$request->password_confirmation;

        if($password ==$password_confirmation){
            if ($request->hasFile('image') && $request->hasFile('certificate') ) {
                    // Store the uploaded file in the storage directory

                    $imageFile = $request->file('image');
                    $certificateFile = $request->file('certificate');
    
                    $imagePath = $this->uploadFile($imageFile, 'images','public');
                    $certificatePath = $this->uploadFile($certificateFile, 'certificates','public');

                $user=User::create([
                    'name'=>$request->post('name'),
                    'email'=>$request->post('email'),
                    'phone_number'=>$request->post('phone_number'),
                    'image'=>$imagePath,
                    'username'=>$request->post('username'),
                    'certificate'=>$certificatePath,
                    'password'=>Hash::make($password),
                    // 'password_confirmation'=>Hash::make($password_confirmation),
                ]);
            }
            
         event(new EmailVerficationEvent($user->id));
         return $this->successResponse($user, 'User created successfully',201);
         }else{
            throw ValidationException::withMessages([
                 'Password does not match',
            ])->status(404);
         }
    }

    public function verifiedEmail(VerifiedEmailRequest $request){
        $email=$request->email;
        $user=User::where('email','=',$email)->first();
        if($user){
            if($user->email_verified_at !=null){
                throw ValidationException::withMessages([
                     'This email is already verified',
                ])->status(422);

            }else{
                $currentDateTime = Carbon::now();
                $verfication_code=User::where('email_verification_otp','=',$request->token)->first();
                if($verfication_code){
                    $user_verfication=User::where('otp_verification_at','>=',$currentDateTime)->first();
                    if($user_verfication){
                        $user->email_verified_at=now();
                        $user->save();
                        return $this->successResponse(null, 'User verified successfully',200);
                    }else{
                        throw ValidationException::withMessages([
                            'This  verification code is time out',
                       ])->status(408);
                    }
                }else{
                    throw ValidationException::withMessages([
                        'This verification code Invalid',
                   ])->status(404);
                }
            }
        }else{
            throw ValidationException::withMessages([
                'The email invalid',
           ])->status(404);
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
                    $dive_name = $request->post('dive_name', $request->userAgent('sanctum.expiration'));
                    $token = $user->createToken($dive_name,expiresAt:now()->addMinute());
                    $data=[
                         'token' => $token->plainTextToken,
                         'user'=>$user,
                    ];
                    return $this->successResponse($data, 'User Login successfully',200);

                } else {
                    throw ValidationException::withMessages([
                        'Password dismatch',
                   ])->status(404);
                }
            }else{
                throw ValidationException::withMessages([
                    'You should verfied your email',
               ])->status(422);
            }
        }else{
            throw ValidationException::withMessages([
                'Email or phone number do not match',
           ])->status(404);
        }
    }

    public function logout(Request $request){
        if($request->user()){
            $request->user()->tokens()->delete();
            return $this->successResponse(null, 'User Logout successfully',200);

        }else{
            throw ValidationException::withMessages([
                'You should Login',
           ])->status(401);
        }
    }

    public function refreshToken(Request $request){
        $user = $request->user();
        $accessToken = $user->currentAccessToken();
    
        if (!$accessToken) {
            throw ValidationException::withMessages([
                'You should Login',
           ])->status(401);        }
        $request->user()->tokens()->delete();
        $newExpiration = Carbon::now()->addMinutes(config('sanctum.refresh_expiration'));
        $new_token=$user->createToken('token-name',expiresAt:now()->addMinutes(config('sanctum.refresh_expiration')))->plainTextToken;

        Auth::guard('sanctum')->user($user);
        $data=[
            'access_token' => $new_token,
            'expires_at' => $newExpiration->toDateTimeString(),
        ];
        return $this->successResponse($data, 'Refreach token successfully',200);
    }
}


