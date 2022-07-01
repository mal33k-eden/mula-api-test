<?php

namespace App\Http\Controllers;
use App\Models\ConnectedCountry;
use App\Models\KycLevel;
use App\Models\KycLevelRequirement;
use App\Models\User;
use \App\Lib\StreamChatServices;
use \App\Notifications\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
         $stream =new StreamChatServices();
         $imageName = 'pic4.jpg';
         $request->validate([
             'first_name' => 'required|string|min:3',
             'last_name' => 'required|string|min:3',
             'email' => 'required|email|unique:users',
             'handle' => 'required|alpha_num|unique:users|min:3|max:8',
             'btc_wallet' => 'required|string|unique:users',
             'mula_wallet' => 'required|string|unique:users',
             'password' => 'required',
             'device_name' => 'required',
        ]);
         if ($request->has('profile_photo')){
             $request->validate([
                 'profile_photo' => 'image|mimes:jpeg,png,jpg',
             ]);
             $imageName = time().Str::slug($request->first_name.' '.$request->last_name,'_').'.'.$request->profile_photo->extension();
             $request->file('profile_photo')->storeAs(
                 'public/profile_photos', $imageName,'local'
             );
         }

        $input = $request->all();

        $input['handle']=$request->handle;
        $input['referral_code'] = User::generateInviteCode($request->handle);
        $input['stream_token']= $stream->createStreamUserToken($request->handle);
        $input['profile_photo']=  $imageName;

        $input['kyc_level_id']= 1;
        if ($request->can_receive_newsletter == "true"){
            $input['can_receive_newsletter']= true;
        }else{
            $input['can_receive_newsletter']= false;
        }

        $user = User::create($input);
        $user->generateTwoFactorCode('email');
        $user->setConnectedCountryId($request->country);
        $user->notify(new TwoFactorCode('email'));

        if ($request->has('referred_code')){
            User::addReferralPoints($request->referred_code);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

       $response = [
           'success'=>true,
           'user'=> self::buildUser($user->id),
           'token'=>$token,
       ];
       return response($response, 201);
    }
    public function updateBasicUserProfile(Request $request){
        $data = false;
        $user = $request->user();
        if ($request->has('email') && !empty( $request->email)){
            $request->validate([
                'email' => 'required|email|unique:users',
            ]);
            $user->generateTwoFactorCode('email');
            $user->notify(new TwoFactorCode('email'));
            $data['email']=$request->email;
        }
        if ($request->has('country') && !empty($request->country)){
            $data['country'] = $request->country;
            $data['country_code'] = $request->country_code;
            $data['email_verified_at']=null;
        }
        if ($request->has('mobile') && !empty($request->mobile )){
            $data['mobile'] = $request->mobile;
            $data['mobile_verified_at']=null;
        }
        if ($request->has('first_name') && !empty($request->first_name )){
            $data['first_name'] = $request->first_name;
        }
        if ($request->has('last_name') && !empty($request->last_name )){
            $data['last_name'] = $request->last_name;
        }
        if ($request->has('can_receive_newsletter') && !empty($request->can_receive_newsletter )){
            if ($request->can_receive_newsletter == "true"){
                $data['can_receive_newsletter']= true;
            }else{
                $data['can_receive_newsletter']= false;
            }
        }

        if ($request->has('handle')  && !empty($request->handle)){
            $request->validate([
                'handle' => 'required|alpha_num|unique:users|min:3|max:8',
            ]);
            $data['handle'] = $request->handle;
            $data['referral_code'] = User::generateInviteCode($request->handle);
        }
        if ($request->hasFile('profile_photo')){
            Storage::disk('public')->delete('/profile_photos/'.$user->profile_photo);
            $imageName = time().Str::slug($user->first_name.' '.$user->last_name,'_').'.'.$request->profile_photo->extension();
            $request->file('profile_photo')->storeAs('public/profile_photos', $imageName,'local');
            $data['profile_photo']=$imageName;
        }
        if ($data){
            $input = $user->fill($data);
            $input->save();
        }

        $response =[
            'success'=>true,
            'user'=>self::buildUser(Auth::id()),
        ];
        return response($response, 201);
    }
    public function getUser(Request $request){
        $response =[
            'success'=>true,
            'user'=>self::buildUser(Auth::id()),
        ];
        return response($response, 201);
    }
    private static function buildUser($user_id){

        $user = User::find($user_id);
        $user->kycLevel;
        $user->connectedCountry;
        $user->connectedCountry->kycLevelRequirement;
        return $user;
    }
    public function isFieldAvailable(Request $request){
        $request->validate([
            'handle' => 'required_without_all:email|alpha_num|unique:users|min:3|max:8',
            'email' => 'required_without_all:handle|email',
        ]);
        if ($request->email){
            $user = User::where('email',$request->email)->count();
        }
        if ($request->handle){
            $user = User::where('handle',$request->handle)->count();
        }


        $response = [
            'isAvailable'=> ($user == 0) ? true: false
        ];
        return response($response, 201);
    }
    public function isWalletLinked(Request $request){
        $request->validate([
            'mula_wallet' => 'required|string',
        ]);
        $user = User::where('mula_wallet',$request->mula_wallet)->first();


        $response = [
            'isLinked'=> ($user == null) ? false: true,
            'handle'=>($user == null) ? '': $user->handle,
        ];
        return response($response, 201);
    }
    public function sendOTP(Request $request){
        $request->validate([
            'type' => 'required', //email or mobile
        ]);
        $user = $request->user();
        $user->generateTwoFactorCode($request->type);
        $user->notify(new TwoFactorCode('email')); //$request->type has to change before go live
        $res = [
            'success'=> true,
            'otp'=>$user->two_factor_code,
            'otp_expires'=>$user->two_factor_expires_at
        ];
        $response =$res ;
        return response($response, 201);
    }
    public function verifyOTP(Request $request){

        $request->validate([
            'code' => 'integer|required',
            'type' => 'string|required', //either email or mobile
        ]);

        $user = auth()->user();
        if($user->two_factor_expires_at->lt(now())){
            $status = false;
            $msg = 'This code has expired. Resend code and try again';
            $code = 422; //unprocessable content
        }else if($request->code == $user->two_factor_code)
        {
            $user->resetTwoFactorCode($request->type);

            $status = true;
            $msg = 'OTP verified successfully';
            $code = 201;
        }else{
            $status = false;
            $msg = 'OTP could not be verified';
            $code = 422; //unprocessable content
        }

        $response = [
            'success'=> $status,
            'message'=>$msg
        ];
        return response($response, $code);
    }
    public function resetPassword(Request $request){
        $user = $request->user();
        $fields = $request->validate([
            'new_password' => 'required|string',
            'old_password' => 'required|string',
        ]);

        // Check password
        if( !Hash::check($fields['old_password'], $user->password)) {
            return response([
                'error' =>true,
                'message' => "password is incorrect"
            ], 401);
        }
        auth()->user()->tokens()->delete();
        $user->password= $request->new_password;
        $user->save();

        $response = [
            'success' => true,
            'msg'=>'password changed'
        ];
        return response($response, 201);
    }
    public function login(Request $request) {
        $fields = $request->validate([
            'handle' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string'
        ]);

        // Check email
        $user = User::where('handle', $fields['handle'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'error' =>true,
                'message' => "username and passwords don't match"
            ], 401);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        $response = [
            'success'=>true,
            'user' => self::buildUser($user->id),
            'token' => $token
        ];

        return response($response, 201);
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return [
            'message'=>'Logged Out',
        ];
    }

}
