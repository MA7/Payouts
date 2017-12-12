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
                'headers' => ['Authorization' => 'Bearer ' . $token . ''],
            ]);
            $content = $response->getBody()->getContents();
            $content = json_decode($content);
            if ($response->getStatusCode() == 200) {
                $zpId = $content->data->zp_id;
                $name = $content->data->name;
                $name = explode(' ', $name, 2);
                return view('ajax-settlement-form', [
                    'zp' => $zpId,
                    'name' => $name[0],
                    'mobile' => $mobile,
                    'family' => isset($name[1]) ? $name[1] : null
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

        try {
            $token = config('app.zarinpal_api_token');
            $client = new Client();
            $response = $client->request("post", "https://api.zarinpal.com/rest/v3/oauth/registerUser.json", [
                'headers' => ['Authorization' => 'Bearer ' . $token . ''],
                'body' => [
                    "first_name" => $name,
                    "last_name" => $family,
                    "mobile" => $mobile,
                    "ssn" => $ssn,
                    "birthday" => $birthdate
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                return redirect()->route('add', [
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
            return response()->json([
                'code' => $exception->getCode(),
                'error' => 'UnknownError',
                'message' => $exception->getMessage()
            ])->setStatusCode(500);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get request data and create one in zarinpal.
     */
    public function createRequest()
    {

    }
}
