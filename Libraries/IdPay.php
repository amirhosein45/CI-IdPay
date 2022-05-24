<?php

namespace App\Libraries;

class IdPay{

    private $API_KEY;
    private $Sandbox = 0;
    private $id;
    private $link;
    private $ErrorID;
    private $ErrorMessage;
    private $URL_PAYMENT = "https://api.idpay.ir/v1.1/payment";
    private $URL_INQUIRY = "https://api.idpay.ir/v1.1/payment/inquiry";
    private $URL_VERIFY = "https://api.idpay.ir/v1.1/payment/verify";
    private $header;

    public function __construct($api_key, $sandbox = false){
        $this->API_KEY = $api_key;
        if($sandbox){
        $this->Sandbox = 1;
        }

        $this->header = array(
            'Content-Type: application/json',
            'X-API-KEY:' . $this->API_KEY,
            'X-SANDBOX:' . $this->Sandbox,
        );
    }

    public function set_link($link) {
        $this->link = $link;
    }
    public function get_link() {
        return $this->link;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_errorid() {
        return $this->ErrorID;
    }

    public function get_errormsg() {
        return $this->ErrorMessage;
    }

    public function request_payment($params){
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->URL_PAYMENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    
        $result = curl_exec($ch);
        curl_close($ch);
    
        $result = json_decode($result);
    
        if (empty($result) || empty($result->link)) {

            $this->ErrorID = $result->error_code;
            $this->ErrorMessage = $result->error_message;
    
            return FALSE;
        }
      
        $this->set_id($result->id);
        $this->set_link($result->link);

        return TRUE;
      
    }

    public function payment_inquiry($id, $order_id){

        $params = array(
            'id' => $id,
            'order_id' => $order_id,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->URL_INQUIRY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if (empty($result) || empty($result->status)) {

            $this->ErrorID = $result->error_code;
            $this->ErrorMessage = $result->error_message;

            return FALSE;
        }

        if ($result->status == 10) {
            return TRUE;
        }

        $this->ErrorID = $result->status;
        $this->ErrorMessage = $this->payment_get_message($result->status);

        return FALSE;
    }

    public function payment_verify($id, $order_id) {
      
        $params = array(
            'id' => $id,
            'order_id' => $order_id,
        );
      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->URL_VERIFY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      
        $result = curl_exec($ch);
        curl_close($ch);
      
        $result = json_decode($result);
      
        if (empty($result) || empty($result->status)) {
      
            $this->ErrorID = $result->error_code;
            $this->ErrorMessage = $result->error_message;
      
          return FALSE;

        }

        if($result->status == 100){
            return $result;
        }
      }
      

    public function payment_get_message($status) {
        
        switch ($status) {
            case 1:
                return 'پرداخت انجام نشده است';
            case 2:
                return 'پرداخت ناموفق بوده است';
            case 3:
                return 'خطا رخ داده است';
            case 10:
                return 'در انتظار تایید پرداخت';
            case 100:
                return 'پرداخت تایید شده است';
            case 101:
                return 'پرداخت قبلاً تایید شده است';
            default:
                return 'Error handeling';
        }
    }
    
}

?>