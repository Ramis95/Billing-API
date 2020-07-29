<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/v1/billing_procedure', 'BillingController@billing_procedure');
$router->post('/v1/check_additional_number', 'BillingController@check_additional_number');
$router->post('/v1/check_bonus', 'BillingController@check_bonus');
$router->post('/v1/get_balance', 'BillingController@get_balance');
$router->post('/v1/get_bonus', 'BillingController@get_bonus');
$router->post('/v1/get_family_cashback', 'BillingController@get_family_cashback');
$router->post('/v1/get_tariff', 'BillingController@get_tariff');
$router->post('/v1/service_together_beneficial', 'BillingController@service_together_beneficial');
$router->post('/v1/subtract_balance', 'BillingController@subtract_balance');
$router->post('/v1/home_cashback', 'BillingController@home_cashback');

//$router->post('/v1/check_installments', 'BillingController@check_installments'); /* Отложено */
//$router->post('/v1/get_tariff_balance', 'BillingController@get_tariff_balance'); /* Отложено */

$router->post('/v1/sendsms', 'SMSController@send_SMS');






//$router->get('/v1/tmt_testing', 'BillingController@get_balance');
//$router->get('/v1/deposit_friend_balance', 'BillingController@tmt_testing');

