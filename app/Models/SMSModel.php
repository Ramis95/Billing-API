<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SMSModel extends Model
{
    protected $connection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $username = env("orc_username");
        $password = env("orc_password");
        $connection_string = env("orc_host") . '/' . env("orc_service_name");
        $this->connection = oci_connect($username, $password, $connection_string,
            "UTF8");
        /* Временное решение. Нужно продумать общее подключение к бд */
    }

    public function send_SMS_toUser($data)
    {
        $orc = oci_parse($this->connection,
            "begin :db_result := psmsManager_TTK.SendSMSpay(:MSISDN, :SMSText, :HeaderName, :BulkId, :Delivery_type, :Response); end;");
        //$err = OCIError();

        if ($orc) {
            oci_bind_by_name($orc, "db_result", $db_result, 15);
            oci_bind_by_name($orc, "Response", $Response, 1000);

            oci_bind_by_name($orc, ":MSISDN", $data['MSISDN']);
            oci_bind_by_name($orc, ":SMSText", $data['SMSText']);
            oci_bind_by_name($orc, ":HeaderName", $data['HeaderName']);
            oci_bind_by_name($orc, ":BulkId", $data['BulkId']);
            oci_bind_by_name($orc, ":Delivery_type", $data['Delivery_type']);

            oci_execute($orc);
            oci_result($orc, $db_result);
        }
        oci_close($this->connection);

        $result['id'] = $db_result;

        return $result;


    }

}
