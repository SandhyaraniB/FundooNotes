<?php
namespace App\Http\Controllers;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
//header("Access-Control-Allow-Headers: Authorization");
//header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization
//');
header("Access-Control-Request-Method: POST");

//   headers.append('Content-Type', 'application/json');
//   headers.append('Accept', 'application/json');

//   headers.append('Access-Control-Allow-Origin', 'http://localhost:3000');
//   headers.append('Access-Control-Allow-Credentials', 'true');

//   headers.append('GET', 'POST', 'OPTIONS');

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\UserRegistered;

class UserController extends Controller
{
    public $successStatus = 200;
/**
 * login api
 *
 * @return \Illuminate\Http\Response
 */
    public function login()
    {
        $email = request('email');
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            //$success['token'] = $user->createToken('MyApp')->accessToken;
            if($user->email_verified_at === null){
               return  response()->json(['message' => 'Email Not verified'],211);
            }
              $token = $user->createToken('fundoo')->accessToken;
              return response()->json(['token' => $token,'userdetails'=>Auth::user()],200);
            // return response()->json(123456);

            //return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 204);
        }
    }
/**
 * Register api
 *
 * @return \Illuminate\Http\Response
 */
    public function register(Request $request)
    {   
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:25',
            'lastname' => 'required|string|max:25',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:15',
            'c_password' => 'required|same:password',
        ]); 
        if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 201);
        }
        // $input = $request->all();
        $input['created_at'] = now();
        $input['password'] = bcrypt($input['password']);
        $input['verifytoken'] = str_random(60);
        $user = User::create($input);
        $success['token'] = $user->createToken('fundoo')->accessToken;
        $success['firstname'] = $user->firstname;
        event(new UserRegistered($user,$user->verifytoken));
        return response()->json(['success' => $success,'message' =>'registation successfull'], $this->successStatus);
    }
/**
 * details api
 *
 * @return \Illuminate\Http\Response
 */
    public function userDetails()
    {
        // $user = Auth::user();
        // return response()->json(['success' => $user], $this->successStatus);
        $user = User::with('labels')->find(Auth::user()->id);
        return response()->json([$user],200);
    }

    /**
     * write the function to verify the user & the add the time stamp to the verified_at field
     * 
     * @return response
     */
        public function verifyEmail($token){
        // $id = request('id');
        // $token = request('token');
        $user = User::where('verifytoken',$token)->first();
        if(!$user){
            return response()->json(['message' => "Not a Registered Email"], 200);
        }else if($user->email_verified_at === null){
         $user->email_verified_at = now();
         $user->save();
            return response()->json(['message' => "Email is Successfully verified"],201);      
        }else{
            return response()->json(['message' => "Email Already verified"],202);
        }
    }

    /**
     * write the function for forgot password.
     * 
     * @return response
     */
    public function forgotPassword(){
        $validator = validator::make($request->all(),[
           'email' => 'bail|required|email|unique:users',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],200);
        }
    }
      /**
       * write the function logout 
       * 
       * @group logout
       * @return response
       */
       public function logout()
       {
          Auth::user()->token()->revoke();
          response()->json(['message' => 'Logout succesfully'],200);
       }

       /**
        * create the function to login user by social service websites like google,facebook
        *
        * all things handled by frontend only authentication part handlen by backend
        */

        public function socialLogin(Request $request)
        {
            $input = $request->all();
            /* $input['created_at'] = now(); */
            $input['password'] = bycrypt(str_random(8));
            $input['verifytoken'] = str_random(60);

            $user = User::where([['email',$input['email']]])->first();

            if(!$user) {
                 $user = User::create($input);
                 /* verify it first that email is is as it is of SocialLogin email*/
                 $user->email_verified_at = now();
            }
            $user->provider = $input['provider'];
            $user->providerprofile = $input['providerprofile'];
            $user->save();
            Auth::login($user,true);
            $token = Auth::user()->createtoken('fundoo')->accessToken;
            return response()->json(['token' => $token ,'userdetails' => Auth::user()],200);   
        }

        /**
          * function to add the profile pic of the user
          * 
          * @var Request
          * @return Response
          */
           public function addProfilepic(Request $req){
           $reqss = $req->all();
           if($req->hasFile('profilepic')){
            //filename
            $origImage=$req->file('profilepic');
            $ext = $req->file('profilepic')->getClientOriginalExtension();
            //if image is svg
            if($ext==='svg'){
                $ext='svg+xml';
            }
            //getting the path of image in temp folder
            $path = $req->file('profilepic')->getRealPath();
            //converting to base64 to save it in database
            $base64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($path));
            //getting the authenticated user
            $user= Auth::user();
            //adding the profile pic to the user and saving it in the database
            $user->profilepic = $base64;
            $user->save();
            //returning the response to the user
            return response()->json(['message'=>'done','data'=> User::with('labels')->find($user->id)],200);
        }
     }
}
 
