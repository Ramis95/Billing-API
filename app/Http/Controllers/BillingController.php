<?php

namespace App\Http\Controllers;

use App\Models\BillingModel;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;


class BillingController extends BaseController
{

    private $model;
    private $userRequest;

    public function __construct(BillingModel $model, Request $request)
    {
        $this->model = $model;
        $userRequest = json_decode($request->getContent(), true);
        $this->userRequest = $userRequest;
    }

    public function get_balance()
    {
        /* Get user balance from billing */

        $result = $this->model->get_user_balance($this->userRequest['msisdn']);

        return response()->json([
            'vCurrentBalance' => $result['VCURRENTBALANCE'],
            'nClient' => $result['NCLIENT']
        ], 200);

    }

    public function subtract_balance()
    {
        $result = $this->model->user_subtract_balance($this->userRequest['nClient']);

//        return response()->json([
//            'vBallans' => $result['VBALLANS'],
//        ], 200);

    }

    public function get_bonus()
    {
        /* Get user balance from billing */

        $result = $this->model->get_user_bonus($this->userRequest['nClient']);

        return response()->json([
            'vBallans' => $result['VBALLANS'],
        ], 200);

    }

    public function check_bonus()
    {
        $result = $this->model->check_user_bonus($this->userRequest['nClient']);

        return response()->json([
            'nClient' => $this->userRequest['nClient'],
            'nCount' => $result['NCOUNT'],
        ], 200);
    }

    public function get_tariff()
    {
        $result = $this->model->get_user_tariff($this->userRequest['msisdn']);

        return response()->json([
            'smsText' => $result['SMSTEXT'],
        ], 200);
    }

    public function check_installments()
    {
        $result = $this->model->check_user_installments($this->userRequest['msisdn']);
    }

    public function billing_procedure()
    {
        $result = $this->model->call_billing_procedure($this->userRequest);

        return response()->json([
            'db_result' => $result['db_result'],
            'text' => $result['text'],
            'endSession' => $result['endSession']
        ], 200);

    }

    public function check_additional_number()
    {
        $result = $this->model->check_user_additional_number($this->userRequest['msisdn']);


        return response()->json([
            'num' => $result,
        ], 200);
    }

    public function get_family_cashback()
    {
        $result = $this->model->get_user_family_cashback($this->userRequest['msisdn']);
        $result = json_encode($result, true);

        return response()->json([
            'family_cashback' => $result,
        ], 200);
    }

    public function home_cashback()
    {

        $result = $this->model->user_home_cashback($this->userRequest['msisdn']);

        $result = json_encode($result);

        return response()->json([
            'home_cashback' => $result,
        ], 200);
    }

    public function service_together_beneficial()
    {
        $result = $this->model->user_service_together_beneficial($this->userRequest['msisdn']);
        return response()->json([
            'nSum' => $result['NSUM'],
        ], 200);
    }

    public function get_tariff_balance()
    {
        /* Отложено */

        $result = $this->model->get_user_tariff_balance($this->userRequest['msisdn']);

//        return response()->json([
//            'num' => $result,
//        ], 200);
    }


}

