# CI-IdPay
Codeigniter 4.x library for IdPay payment gateway

This library let you make the payment procces based on IdPay, effortless and easy.

## Installation
***
1. [Download](https://github.com/amirhosein45/CI-IdPay/archive/refs/heads/main.zip) the source files
2. Copy the folder `Libraries` to `app` folder of your CodeIgniter installation
3. That's it! Start using with the examples below 

## Quick Start 
***
Let's get started :)
First, we will load the IdPay Library into the system


```php
use App\Libraries\IdPay;
```

That was easy!

Now let's create object of Zarinpal

```php
$idpay = new IdPay("web-service-api-key");
```

Note: For testing purposes, you can turn on sandbox mode like this

```php
$idpay = new IdPay("web-service-api-key", true);
```

OK, now we can send user to gateway with idpay

```php
$params = array(
  'order_id' => '101',
  'amount' => 10000,
  'phone' => '09171111111',
  'name' => 'نام پرداخت کننده',
  'desc' => 'توضیحات پرداخت کننده',
  'callback' => 'https://pay2.bispee.ir/idpay',
);
if ($this->idpay->request_payment($params)){
  $id = $this->idpay->get_id(); 
  // do database stuff
  return redirect()->to($this->idpay->get_link());
}else {
  // Unable to connect to gateway
  $data['er'] = $this->idpay->get_errormsg();
}
```

For verifying a user's payment, use these codes:


```php
$status = $this->request->getVar('status');
$track_id = $this->request->getVar('track_id');
$id = $this->request->getVar('id');
$order_id = $this->request->getVar('order_id');

if ($status === NULL || $track_id === NULL || $id === NULL || $order_id === NULL){
  //do nothing
}

if ($this->idpay->payment_inquiry($id, $order_id)){
    $result = $this->idpay->payment_verify($id, $order_id);
      if ($result !== FALSE){
        // payment succeeded, do database stuff   
        // payment data stored in $result
        // payment amount: $result->amount;
      }
}else{
  $error = $this->idpay->get_errormsg();
  // payment failed or payment canceled by user
}
```

You have reached the end of the Quick Start Guide, but please take a look at the Example code section

## Example code

```php
<?php

namespace App\Controllers;

use App\Libraries\IdPay;

class Home_idpay extends BaseController{

    public $idpay;

    public function __construct(){
        $this->idpay = new IdPay("04b19e56-fe97-404c-9b98-3d3b7f9cadf9", true);
    }

    public function index(){

    
        $status = $this->request->getVar('status');
        $track_id = $this->request->getVar('track_id');
        $id = $this->request->getVar('id');
        $order_id = $this->request->getVar('order_id');

        if ($status === NULL || $track_id === NULL || $id === NULL || $order_id === NULL){
            //do nothing
        }

        if ($this->idpay->payment_inquiry($id, $order_id)){
            $result = $this->idpay->payment_verify($id, $order_id);
            if ($result !== FALSE){
                // payment succeeded, do database stuff   
                // payment data stored in $result
                // payment amount: $result->amount;
                $data['er'] = 'payment succeeded';
            }
        }else{
            $error = $this->idpay->get_errormsg();
            // payment failed or payment canceled by user
            $data['er'] = 'payment failed';
        }
        
        return view('welcome_message', $data);
    }

    public function test(){

        $params = array(
            'order_id' => '101',
            'amount' => 10000,
            'phone' => '09382198592',
            'name' => 'نام پرداخت کننده',
            'desc' => 'توضیحات پرداخت کننده',
            'callback' => 'https://pay2.bispee.ir/idpay',
          );
          

        if ($this->idpay->request_payment($params)){
            $id = $this->idpay->get_id();
            // do database stuff
            return redirect()->to($this->idpay->get_link());
        }else {
            // Unable to connect to gateway
            $data['er'] = $this->idpay->get_errormsg();
        }
        return view('welcome_message', $data);
    }
}


```
