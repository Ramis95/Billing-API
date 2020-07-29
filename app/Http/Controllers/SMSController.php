<?php

namespace App\Http\Controllers;

use App\Models\SMSModel;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;


class SMSController extends BaseController
{
    private $model;
    private $userRequest;

    public function __construct(SMSModel $model, Request $request)
    {
        $this->model = $model;
        $userRequest = json_decode($request->getContent(), true);
        $this->userRequest = $userRequest;
    }

    public function send_SMS()
    {
        $result = $this->model->send_SMS_toUser($this->userRequest);

        return response()->json([
            'id' => $result['id'],
        ], 200);
    }
}
