<?php

namespace App\Http\Controllers;

use aminkt\normalizer\Normalize;
use App\Settlement;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        // get all the Settlements
        $settlements = Settlement::all();

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
            $content = $response->getBody()->getContents();
            $content = json_decode($content);
            if ($response->getStatusCode() == 200) {
                $request->session()->flash('status', 'درخواست واریز با موفقیت ثبت شد.');

                $log = new Settlement();
                $log->name = $request->input('name');
                $log->family = $request->input('family');
                $log->mobile = $request->input('mobile');
                $log->zp = $zp;
                $log->purseId = $purse;
                $log->iban = $iban;
                $log->amount = $amount;
                $log->description = $description;
                $log->createAt = date('Y-m-d H:i');
                $log->save();
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
}
