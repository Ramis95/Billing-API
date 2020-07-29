<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BillingModel extends Model
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
    }

    public function get_user_balance($msisdn)
    {
        $result = [];

        $orc = oci_parse($this->connection, "SELECT  to_number(round(ac.current_balance,2)) vCurrentBalance, TRIM(LEADING '0' from  cl.EXTERNAL_ID) nClient
           FROM billing.tcontractcommon cc,billing.taccount ac, BILLING.TCLIENT cl,Billing.TContractMobile cm,     Billing.tphonenumber pn, billing.tcontract c
           WHERE  cc.account_id = ac.object_no and cl.object_no=ac.client_id and c.object_no=cm.object_no and c.date_out>sysdate and c.date_in <=sysdate
            and c.contractcommon_id=cc.object_no
            and cm.phonenumber_id = pn.object_no and pn.object_name=:msisdn");


        $err = OCIError();

        oci_bind_by_name($orc, ":msisdn", $msisdn);
        oci_execute($orc);


        while ($row = oci_fetch_assoc($orc)) {
            $result = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        return $result;
    }

    public function user_subtract_balance($msisdn)
    {

    }

    public function get_user_bonus($nClient)
    {
        $result = [];

        $orc = oci_parse($this->connection, "select BALLANS vBALLANS from
l_loyaltymember@CRM_TTK where CLIENT_ID=:nClient and E_DATE>sysdate");


        $err = OCIError();

        oci_bind_by_name($orc, ":nClient", $nClient);
        oci_execute($orc);


        while ($row = oci_fetch_assoc($orc)) {
            $result = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        return $result;
    }

    public function check_user_additional_number($msisdn)
    {
        $result = [];
        $orc = oci_parse($this->connection, "select n.object_name Num
      from billing.tadditionalnumber a,billing.tphonenumber n, Billing.TContractMobile cm,     Billing.tphonenumber pn, billing.tcontract c
      where (a.contractcommon_id=c.contractcommon_id) and (a.phonenumber_id=n.object_no) and c.object_no=cm.object_no and c.date_out>sysdate and c.date_in <=sysdate
			and cm.phonenumber_id = pn.object_no and pn.object_name=:MSISDN and
            (a.date_in<sysdate) and (a.date_out>sysdate)");
        $err = OCIError();

        oci_bind_by_name($orc, ":MSISDN", $msisdn);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $result = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);


        if (isset($result['NUM'])) {
            $result = $result['NUM'];
        }

        return $result;
    }

    public function check_user_bonus($nClient)
    {
        $result = [];
        $orc = oci_parse($this->connection, "select count(*) nCount from l_loyaltymember@CRM_TTK where CLIENT_ID=:nClient and E_DATE>sysdate");
        $err = OCIError();

        oci_bind_by_name($orc, ":nClient", $nClient);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $result = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        return $result;
    }

    public function get_user_tariff($msisdn)
    {
        $result = [];
        $orc = oci_parse($this->connection, "select t.DESCRIPTION SmsText from billing.tcontract c, BILLING.TTARIFFPLAN t,Billing.TContractMobile cm, Billing.tphonenumber pn
                where  c.TARIFFPLAN_ID = t.OBJECT_NO  and c.date_out > sysdate and  date_in<=sysdate
                and c.object_no=cm.object_no
                and cm.phonenumber_id = pn.object_no
                and pn.object_name=:MSISDN");

        $err = OCIError();

        oci_bind_by_name($orc, ":MSISDN", $msisdn);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $result = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        return $result;
    }

    public function user_service_together_beneficial($msisdn)
    {
        $result = [];

        $orc = oci_parse($this->connection, "select nvl(ROUND(sum(ch.CHR_ERS),2),0)  nSUM from BILLING.TCHARGE  ch, billing.tcontract c ,Billing.TContractMobile cm,     Billing.tphonenumber pn
          where ch.period = to_char(sysdate, 'yyyymm') and c.object_no = ch.contract_id
		  and c.object_no=cm.object_no and c.date_out>sysdate and c.date_in <=sysdate
    and cm.phonenumber_id = pn.object_no and pn.object_name=:MSISDN");

        $err = OCIError();

        oci_bind_by_name($orc, ":MSISDN", $msisdn);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $result = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        return $result;

    }

    public function user_home_cashback($msisdn)
    {
        $result = [];

        $orc = oci_parse($this->connection, "select  c.contractcommon_id pSubscriberID
from Billing.TContract c, Billing.TContractMobile cm, Billing.tphonenumber pn
where  c.object_no=cm.object_no
    and cm.phonenumber_id = pn.object_no
    and pn.object_name=:MSISDN and sysdate between c.date_in  and c.date_out");

        $err = OCIError();

        oci_bind_by_name($orc, ":MSISDN", $msisdn);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $result = $row;
        }


        if (isset($result['PSUBSCRIBERID'])) {

            $orc = oci_parse($this->connection, "begin :SU_M := ttk_billing.TAV_CALCACTIONVV2(:pSubscriberID, :kh); end;");

            if ($orc) {

                oci_bind_by_name($orc, ":SU_M", $SU_M, 15);

                oci_bind_by_name($orc, ":kh", $kh, 15);

                oci_bind_by_name($orc, ":pSubscriberID", $result['PSUBSCRIBERID']);

                oci_execute($orc);
                oci_result($orc, $SU_M);

            }
            oci_close($this->connection);

            $result['sum'] = $SU_M;
            $result['kh'] = $kh;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        return $result;
    }

    public function get_user_family_cashback($msisdn)
    {
        $result = [];

        $orc = oci_parse($this->connection, "select  c.contractcommon_id pSubscriberID
                from Billing.TContract c, Billing.TContractMobile cm, Billing.tphonenumber pn
                where  c.object_no=cm.object_no
                    and cm.phonenumber_id = pn.object_no
                    and pn.object_name=:MSISDN and sysdate between c.date_in and c.date_out");

        $err = OCIError();

        oci_bind_by_name($orc, ":MSISDN", $msisdn);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $db_result = $row;
        }


        if (isset($db_result['PSUBSCRIBERID'])) {

            $pSubscriberID = $db_result['PSUBSCRIBERID'];

            $orc = oci_parse($this->connection, "begin :SU_M := ttk_billing.TAV_CALCFAMILYCASHBACK(:pSubscriberID, :v); end;");

            if ($orc) {

                oci_bind_by_name($orc, ":SU_M", $SU_M, 15);
                oci_bind_by_name($orc, ":v", $v, 15);


                oci_bind_by_name($orc, ":pSubscriberID", $pSubscriberID);

                oci_execute($orc);
                oci_result($orc, $SU_M);
            }
            oci_close($this->connection);

            $result['pSubscriberID'] = $pSubscriberID;
            $result['sum'] = $SU_M;

        }
        return $result;
    }

    public function get_user_tariff_balance($msisdn)
    {

        $result = [];
        $orc = oci_parse($this->connection, "select
        (case   when MN is not null then 'по МГ/МН: '
                when RT is not null and RF is null then 'по РТ: '
				when RT  is null and RF is not null then 'по РФ: '
				when RT is not null and RF is not null then 'по РТ и РФ: '
				when name like '%Роуминг%' and name not like '%РТ и РФ%' then 'в Роуминге: '
                when id_paket='378314030' then 'внутри сети: '
		else null end) ||
        round(sum(current_volume))||' из '||INITIAL_VOLUME||' '||decode(upper(unit_code),'ФАКТ',case when instr(upper(name),'SMS')>0 then ' SMS' when  instr(upper(name),' MMS')>0 then 'MMS' else '' end,unit_code) ||
    max (decode (date_out, to_date('01.01.4000','dd.mm.yyyy'), '', ' до '||to_char(date_out,'dd.mm'))) free_packet
    from (select p.current_volume, p.INITIAL_VOLUME,u.unit_code, u.unit_name, t.object_no, t.name, t.object_no id_paket,p.date_out, us.RT,us.RF,us.MN,
              (select TARIFFPLAN_ID from billing.tcontract where DATE_OUT>sysdate and CONTRACTCOMMON_ID=c.contractcommon_id) tariff
          from Billing.TContractMobile cm,     Billing.tphonenumber pn, billing.tcontract c,Billing.TTrafficpacket p,  Billing.TUnit u, Billing.TPacketType t  left join  (select DISTINCT t3.C_F1, RT,RF,MN from
                ( select DISTINCT C_F1 from  billing.container c1
                        where c1.scd_id=(select max(d.scd_id) from billing.scaledates d where d.scl_id=500 and d.deletor_id is null and d.scd_date<=sysdate)
                            and c1.C_F2 in (225641805,601490948,225641809,335081041,587596563,225641810,225642494))t3
                    left join ( select  C_F1, C_F2 RF from billing.scaledates d, billing.container c1
                        where c1.scd_id=(select max(d.scd_id) from billing.scaledates d where d.scl_id=500 and d.deletor_id is null and d.scd_date<=sysdate)
                            and c1.C_F2 in( 601490948,335081041,225641810))t on t.C_F1=t3.C_F1
                    left join ( select  C_F1, C_F2 MN from billing.scaledates d, billing.container c1
                        where c1.scd_id=(select max(d.scd_id) from billing.scaledates d where d.scl_id=500 and d.deletor_id is null and d.scd_date<=sysdate)
                            and c1.C_F2 in( 225642494))t4 on t4.C_F1=t3.C_F1
                    left join ( select C_F1, C_F2 RT from billing.scaledates d, billing.container c1
                        where c1.scd_id=(select max(d.scd_id) from billing.scaledates d where d.scl_id=500 and d.deletor_id is null and d.scd_date<=sysdate)
                            and c1.C_F2 in( 225641805,225641809,587596563))t1 on t1.C_F1=t3.C_F1  )  us on us.C_F1=t.object_no
          where  p.packettype_id=t.object_no and u.object_no=t.unit_id
          and p.date_in<sysdate and p.date_out>sysdate and p.current_volume>0 and upper(u.unit_code) in ('МИН','МБ','ФАКТ')
          and p.contractcommon_id=c.contractcommon_id and c.object_no=cm.object_no and c.date_out>sysdate and c.date_in <=sysdate
    and cm.phonenumber_id = pn.object_no and pn.object_name=:MSISDN)
    group by unit_code, unit_name, object_no, name, tariff, RT,RF,MN,INITIAL_VOLUME");

        $err = OCIError();

        oci_bind_by_name($orc, ":MSISDN", $msisdn);
        oci_execute($orc);

        while ($row = oci_fetch_array($orc, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $result[] = $row;
        }

        oci_free_statement($orc);
        oci_close($this->connection);

        /* Отложено */
    }

    public function call_billing_procedure($request)
    {
        $faza = 0;

        if ($request['faza']) {
            $faza = $request['faza'];
        }

        $orc = oci_parse($this->connection,
            "begin :db_result := smsservice.T2_psmsservice.ProcessUSSDRequest(:msisdn, :serviceNumber, :channel, :sessionId, :request, :errorCode, :faza, :text, :endSession); end;");

        if ($orc) {
            oci_bind_by_name($orc, "db_result", $db_result, 15);
            oci_bind_by_name($orc, "text", $text, 1000);
            oci_bind_by_name($orc, "endSession", $endSession, 100);

            oci_bind_by_name($orc, ":msisdn", $request['msisdn']);
            oci_bind_by_name($orc, ":serviceNumber",
                $request['serviceNumber']);
            oci_bind_by_name($orc, ":channel", $request['channel']);
            oci_bind_by_name($orc, ":sessionId", $request['sessionId']);
            oci_bind_by_name($orc, ":request", $request['request']);
            oci_bind_by_name($orc, ":errorCode", $request['errorCode']);
            oci_bind_by_name($orc, ":faza", $faza);

            oci_execute($orc);
            oci_result($orc, $db_result);
            oci_result($orc, $text);
            oci_result($orc, $endSession);

                $result["db_result"] = $db_result;
                $result["text"] = $text;
                $result["endSession"] = $endSession;

        }
        oci_close($this->connection);

        return $result;

    }

}
