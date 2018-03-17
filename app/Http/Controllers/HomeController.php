<?php

namespace App\Http\Controllers;

use aminkt\normalizer\Normalize;
use App\Settlement;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware(['auth','isActive']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * List of all settlements that requested since now.
     *
     * @return \Illuminate\Http\Response
     */
    public function settlementsList()
    {
        if(isset($_GET['s'])){
            $settlements = Settlement::where('status',(int) $_GET['s'])->orderBy('id','DESC')->get();
        }else{
            $settlements = Settlement::orderBy('id','DESC')->get();
        }
        return view('list', [
            'settlements' => $settlements
        ]);
    }

    /**
     * Create a new settlement request.
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function addSettlement(Request $request)
    {
        $mobile = $request->get('mobile', '');
        return view('add', ['mobile' => $mobile]);
    }


    public function inquirySettlement(Request $request)
    {
        $zp_id = $request->get('zp_id', '');
        $amount = $request->get('amount', '');
        $transaction_public_id = $request->get('transaction_public_id', '');
        return view('inquiry', [
            'zp_id' => $zp_id,
            'amount' => $amount,
            'transaction_public_id' => $transaction_public_id,
            ]);
    }

    /**
     * Check mobile number that user exist or not.
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function getCheckMobile(Request $request)
    {
        $mobile = Normalize::normalizeMobile($request->get('mobile'), Normalize::STRATEGY_BY_ZERO);
        if (!$mobile)
            return response()->json([
                'code' => '400',
                'error' => 'MobileIsNotValid',
                'message' => 'Mobile is not valid.'
            ])->setStatusCode(400);

        try {
            $token = config('app.zarinpal_api_token');
            $client = new Client();
            $response = $client->request("get", "https://api.zarinpal.com/rest/v3/purse/$mobile.json", [
                'headers' => ['Authorization' => 'Bearer ' . $token],
            ]);
            $content = $response->getBody()->getContents();
            $content = json_decode($content);
            if ($response->getStatusCode() == 200) {
                $zpId = $content->data->zp_id;
                $name = $content->data->name;
                $name = explode(' ', $name, 2);

                $client = new Client();
                $response = $client->request("get", "https://api.zarinpal.com/rest/v3/purse.json", [
                    'headers' => ['Authorization' => 'Bearer ' . $token],
                ]);
                $content = $response->getBody()->getContents();
                $content = json_decode($content);

                return view('ajax-settlement-form', [
                    'zp' => $zpId,
                    'name' => $name[0],
                    'mobile' => $mobile,
                    'family' => isset($name[1]) ? $name[1] : null,
                    'purses' => $content->data
                ])->render();
            } else {
                return response()->json([
                    'code' => '500',
                    'error' => 'UnknownError',
                    'message' => 'Unknown Error.'
                ])->setStatusCode(500);
            }
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $content = $response->getBody()->getContents();
            $content = json_decode($content);
            if ($response->getStatusCode() == 400 and $content->meta->error_type == "UserNotFound") {
                return view('ajax-register-user', [
                    'mobile' => $mobile
                ])->render();
            } else {
                return response()->json([
                    'code' => '500',
                    'error' => 'UnknownError',
                    'message' => 'Unknown Error.'
                ])->setStatusCode(500);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get user data and register him in zarinpal. then redirect to create settlement request.
     * @param Request $request
     * @return $this|string
     * @throws \Exception
     */
    public function registerNewUser(Request $request)
    {
        $name = $request->input('first-name');
        $family = $request->input('last-name');
        $mobile = $request->input('mobile-number');
        $ssn = $request->input('ssn');
        $birthdate = $request->input('birth-date');


        $validatedData = $request->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'mobile-number' => 'required',
            'ssn' => 'required',
            'birth-date' => 'required',
        ]);

        try {
            $client = new Client();
            $postParams = [
                "first_name" => $name,
                "last_name" => $family,
                "mobile" => $mobile,
                "ssn" => $ssn,
                "birthday" => $birthdate
            ];
            $response = $client->request('post', "https://api.zarinpal.com/rest/v3/oauth/registerUser.json", [
                'form_params' => $postParams
            ]);
            if ($response->getStatusCode() == 200) {
                $content = $response->getBody()->getContents();
                $content = json_decode($content);
                return redirect()->route('add', [
                    'zp' => $content->data->zp_id,
                    'mobile' => $mobile
                ]);
            } else {
                return response()->json([
                    'code' => '500',
                    'error' => 'UnknownError',
                    'message' => 'Unknown Error.'
                ])->setStatusCode(500);
            }
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $content = $response->getBody()->getContents();
            $content = json_decode($content);
            return response()->json($content)->setStatusCode(500);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get request data and create one in zarinpal.
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function createRequest(Request $request)
    {
        $purse = $request->input('purse');
        $amount = $request->input('amount');
        $description = $request->input('description');
        $iban = $request->input('iban');
        $zp = $request->input('zp');
        $name = $request->input('name');

        $validatedData = $request->validate([
            'purse' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'iban' => 'required',
            'zp' => 'required',
            'name' => 'required',
        ]);

        try {
            $token = config('app.zarinpal_api_token');
            $client = new Client();

            $postParams = [
                "purse" => $purse,
                "amount" => $amount,
                "description" => $description,
                "zp_id" => $zp,
                "iban" => $iban,
//                "name" => $name
            ];
            $response = $client->request("post", "https://www.zarinpal.com/rest/v3/transaction/withdrawToUser.json", [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'form_params' => $postParams

            ]);
            $content = json_decode((string) $response->getBody(), true);
            if ($response->getStatusCode() == 200) {
                $request->session()->flash('status', 'درخواست واریز با موفقیت ثبت شد.');

                $log = new Settlement();
                $log->user_id = Auth::id();
                $log->name = $request->input('name');
                $log->family = $request->input('family');
                $log->mobile = $request->input('mobile');
                $log->zp = $zp;
                $log->purseId = $purse;
                $log->iban = $iban;
                $log->amount = $amount;
                $log->description = $description;
                $log->withdraw_ref_id = $content['data']['withdraw_ref_id'];
                $log->transfer_ref_id = $content['data']['transfer_ref_id'];
                $log->transaction_public_id = $content['data']['transaction_public_id'];
                $log->save();
                //dd($content);
            } else {
                $request->session()->flash('status', 'در ارسال درخواست مشکلی به وجود آمده است.');
            }
            return redirect()->route('home');
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $content = $response->getBody()->getContents();
            $content = json_decode($content);
            return response()->json($content)->setStatusCode(500);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }


    public function postCheckInquiry(Request $request)
    {

        foreach (Settlement::where('status', 0)->get() as $settlement){

            try {
                $token = config('app.zarinpal_api_token');
                $client = new Client();

                $postParams = [
                    "zp_id" => $settlement->zp,
                    "amount" => $settlement->amount,
                    "transaction_public_id" => $settlement->transaction_public_id,
                ];
                $response = $client->request("post", "https://api.zarinpal.com/rest/v3/transaction/withdrawToUserFollowup.json", [
                    'headers' => ['Authorization' => 'Bearer ' . $token],
                    'form_params' => $postParams

                ]);
                $content = json_decode((string)$response->getBody(), true);
                $err = '';
                $err .= 'وضعیت تراکنش : ' . $content['data']['confirmed'] . ' <br> ';
                $err .= 'توضیحات تراکنش : ' . $content['data']['description'] . ' <br> ';
                $err .= 'تاریخ تسویه : ' . $content['data']['reconciled_at'] . ' <br> ';

                if ($content['data']['confirmed'] == 'confirmed') {
                    $settlement->status = 1;
                }
                $settlement->paydescription = $err;
                $settlement->save();
            } catch (ClientException $exception) {
                $response = $exception->getResponse();
                $content = $response->getBody()->getContents();
                $content = json_decode($content);
                return response()->json($content)->setStatusCode(500);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }

        if(!empty($_GET['withdraw_ref_id'])){
            return redirect('/settlementShow/'.$_GET['withdraw_ref_id']);
        }
        return redirect('/settlements?s=0');

    }


    public function settlementShow($id)
    {
        $settlement = Settlement::where('withdraw_ref_id',$id)->first();
        return view('show', [
            'settlement' => $settlement
        ]);
    }

    public function userList()
    {
        $users = User::orderBy('id','DESC')->get();
        return view('user.list', [
            'users' => $users
        ]);
    }


    public function newUser(Request $request)
    {

        if ($request->isMethod('get')) {
            return view('user.new');
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
            if($validator->fails()){
                return redirect::back()
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
            User::create([
                'name'=>$request->name,
                'password' => bcrypt($request->password),
                'email'=>$request->email
            ]);
            return redirect('/user/list');
        }

    }


    public function userChangePassword(Request $request,$id)
    {

        $user = User::find((int) $id);
        if(!$user){
            return '404';
        }
        if ($request->isMethod('get')) {
            return view('user.changepas',compact('user'));
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(),[
                'password' => 'required|string|min:6|confirmed',
            ]);
            if($validator->fails()){
                return redirect::back()
                    ->withErrors($validator)
                    ->withInput($request->all());
            }
            $user->update([
                'password' => bcrypt($request->password),
            ]);
            return redirect('/user/list');
        }

    }


    public function userStatus(Request $request,$id)
    {

        $user = User::find((int) $id);
        if(!$user){
            return '404';
        }
        if($user->status==1){
            $user->status=0;
        }else{
            $user->status=1;
        }

        $user->save();

        return redirect('user/list');

    }



}
