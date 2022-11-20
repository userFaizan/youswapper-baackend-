<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserIntrest_pivot;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request)
    {

        $user = User::where('email',$request->email)->first();
        $this->validate(
            $request, 
            ['name' => 'required'],
            ['address' => 'required'],
            ['lat' => 'required'],
            ['lng' => 'required'],
            ['surname' => 'required'],
            ['age' => 'required'],
            // ['county' => 'required'],
            ['password' => 'required'],
            ['password_confirmation' => 'required']
        );
        if(strlen($request->name)<4)
        {
            return   response()->json([
                "message"=>"The name must be at least 4 characters.",
                'error' => True
            ]);
        
        }

    elseif(strlen($request->password)<8)
{
    return   response()->json([
        "message"=>"The password must be at least 8 characters.",
        'error' => True
    ]);

}

elseif($request->password != $request->password_confirmation)
{
    return   response()->json([
        "message"=>"The password does not match.",
        'error' => True
    ]);

}
        
elseif($user != null)
{
    return   response()->json([
        "message"=>"Email already taken",
        'error' => True
    ]);

} 
elseif(strlen($request->surname)<4)
{
    return   response()->json([
        "message"=>"The surname must be at least 4 characters.",
        'error' => True
    ]);

}
elseif(strlen($request->age)>13)
{
    return   response()->json([
        "message"=>"The age must be at least 13 years.",
        'error' => True
    ]);

}
// elseif(strlen($request->country)<4)
// {
//     return   response()->json([
//         "message"=>"The country must be at least  4 characters.",
//         'error' => True
//     ]);

// }
else{
        $description= "Verification code ";  
        $otp = rand(1111,9999);
        Mail::to($request->email)->send(new OtpMail($description.$otp));
        $user = User::where('email',$request->email)->create([
        'otp'=>$otp,
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'address' => $request->address,
        'lat' => $request->lat,
        'lng' => $request->lng,
        'surname' => $request->surname,
        'age' => $request->age,
        'country' => $request->country,
        
    ]);
    // dd($user); 
    
    $token = $user->createToken('LaravelAuthApp')->accessToken;

          
    if($user)
    {
    return response()->json([
        "data"=>[
            "token"=>$token,
            "id"=>$user->id,
            "name"=>$user->name,
            "email"=>$user->email,
            "address"=>$user->address,
            "lat"=>$user->lat,
            "lng"=>$user->lng,

        ],
        "message"=>"User Register Successfully!",
        'error' => False

    ]);
}else{
    return response()->json([
        "message"=>"Validation Error",
        'error' => True
    ]);
}
   
    }
       
    }
    /**
     * Login
     */
    public function login(Request $request)
    {
        $description= "Verification code ";
        $otp = rand(1111,9999);
         $user =User::where('email',$request->email)->first();
     
        if($user)
        {   
            if(Hash::Check($request->password, $user->password))
            {
         $interest =UserIntrest_pivot::where('user_id',$user->id)->get();

                $token=$user->createToken('LaravelAuthApp')->accessToken;
                if(count($interest)>0)
                $interestStatus =true;
                  else 
                  $interestStatus = false;
                  if($user->email_otp == 0)
                  {
                User::where('email',$request->email)->update(['otp'=>$otp]);
                 Mail::to($request->email)->send(new OtpMail($description.$otp));
                  }
                return response()->json([
                    "data"=>[
                        "token"=>$token,
                        "otp"=>$otp,
                        "id"=>$user->id,
                        "email_otp"=>$user->email_otp,
                        "name"=>$user->name,
                        "email"=>$user->email,
                        'surname' => $user->surname,
                        'avatar' => $user->avatar,
                        "interest_status"=> $interestStatus,
                    ],
                    "message"=>"User Login Successfully!",
                    'error' => False
                    
                ]);
            }
            else{
                return response()->json([
                    "message"=>"Password Missmatch",
                    'error' => True
                ]);
            }
        }else{
            return response()->json([
                "message"=>"user Not Found",
                'error'=> True
            ]);
        }
        }  
        public function match_otp_email(Request $request)
        {
            $user = User::where('email',$request->email)->first();
            if($user != null){
                if($user->otp == $request->otp)
                {
                    $user->update(['email_otp'=> 1]);
                    $token = $user->createToken('token generated')->accessToken;
                     $response = [
                        'token' => $token,
                        'user' => $user,
                        "message"=>"Match Otp Successfully!",
                         'error' => False
                    ];
                    return response($response, 200);
                }else{
                    return response()->json(['message'=>'otp mismatch', 'error' => True],200);
                }
            }else{
                return response()->json(['message'=>'user does not exist!','error' => True],200);
            }
        }     
    public function match_otp(Request $request)
    {
        
        $user = User::where('email',$request->email)->first();
        if($user != null){
            if($user->otp == $request->otp)
            {
                $user->update(['otp'=>null]);
                $token = $user->createToken('token generated')->accessToken;
                $response = [
                    'token' => $token,
                    'user' => $user,
                    'message'=>'otp matched successfully',
                    'error' => False
                ];
                return response($response, 200);
            }else{
                return response()->json(['message'=>'otp mismatch','error' => True],200);
            }
        }else{
            return response()->json(['message'=>'user does not exist!','error' => True],200);
        }
    }
   
    public function reset_password(Request $request)
    {
        
        $request->validate([
            'password'=>'required|min:8',
            'confirm_password'=>'required',
         ]);
         if ($request->password == $request->confirm_password){
            Auth::user()->update(['password'=>Hash::make($request->password)]);
            return response()->json([
               'message' => 'Password Reset Successfully',
                'error' => FALSE

            ]);
        }else{
            return response()->json([
                'message' => 'Your Confirm Password Does Not Match With Your Password',
                'error' => True

            ]);
        }
         
         }
        
        public function logout()
        {
            auth()->user()->token()->revoke();
            return response()->json([
                'message'=>'Successfully Logout',
                'error' => FALSE


            ]);
        }
    public function send_email(Request $request){
       
        $request->validate([
           'email'=>'required|email',
        ]);
        if (User::where('email',$request->email)->doesntExist()){
            return response()->json([
                'message' => "Your Email Does Not Exist",
                'error'=> TRUE
            ]);
        }else{
            $description= "Verification code ";  
            $otp = rand(1111,9999);
            $user = User::where('email',$request->email)->update(['otp'=>$otp]);
            Mail::to($request->email)->send(new OtpMail($description.$otp));
            return response()->json([
               'message'=> 'Please Kindly Check Your Email',
               'error'=> FALSE
               
            ]);
        }
    }
    public function update_password(Request $request){

        $request->validate([
           'new_password'=>'required|min:8',
        ]);
        if(Hash::check($request->old_password , auth()->user()->password)){
       
            if ($request->new_password == $request->confirm_password){
                Auth::user()->update(['password'=>Hash::make($request->new_password)]);
                return response()->json([
                   'message' => 'Password Has Been Updated',
                    'error'=>FALSE
                ]);
            }else{
                return response()->json([
                    'message' => 'Your Confirm Password Does Not Match With Your New Password',
                    'error'=> TRUE
                ],403);
            }
        }else{
            return response()->json([
                'message' => 'Your Old Password Does Not Match',
                'error'=> TRUE
            ],403);
        }
    }

       
}