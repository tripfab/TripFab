<?php

require_once "WS/anet_php_sdk/AuthorizeNet.php";

class WS_Creditcard {
    
    const API_LOGIN   = '6XUsfg56vJ';
    const API_KEY     = '7v29P3zF2p52U2Pz';
    const API_HASH    = '123';
    const API_VERSION = '3.1';
    const API_CHAR    = '|';
    const API_TRUE    = 'TRUE';
    const API_FALSE   = 'FALSE';
    const API_TYPE    = 'AUTH_CAPTURE';
    const API_METHOD  = 'CC';
    
    protected $cnumber;
    protected $date;
    protected $code;
    protected $fname;
    protected $lname;
    protected $street;
    protected $city;
    protected $country;
    protected $state;
    protected $zip;
    
    protected $api_url;
    

    public function __construct($test = false){
        $this->cnumber = $_POST['cnumber'];
        $this->date    = $_POST['cmonth'] .'/'. $_POST['cyear'];
        $this->code    = '123';//$_POST['ccode'];
        $this->fname   = $_POST['cname'];
        $this->street  = $_POST['street1'];
        $this->city    = $_POST['city'];
        $this->zip     = $_POST['zip'];
        $this->country = $_POST['country'];
        
        $this->state = '';
        $this->lname = '';
        
        $this->api_url = ($test) ? AuthorizeNetAIM::SANDBOX_URL : AuthorizeNetAIM::LIVE_URL;
    }
    
    public function proceed($ammount, $returnURL, $cancelURL){
        
        $request = new Zend_Http_Client($this->api_url);
        
        $data = array(
            'x_login'          => self::API_LOGIN,
            'x_tran_key'       => self::API_KEY,
            
            'x_version'        => self::API_VERSION,
            'x_delim_data'     => self::API_TRUE,
            'x_delim_char'     => self::API_CHAR,
            'x_relay_response' => self::API_FALSE,
            
            'x_type'           => self::API_TYPE,
            'x_method'         => self::API_METHOD,
            'x_card_num'       => $this->cnumber,
            'x_exp_date'       => $this->date,
            
            'x_first_name'     => $this->fname,
            'x_last_name'      => $this->lname,
            'x_address'        => $this->street,
            'x_state'          => $this->state,
            'x_zip'            => $this->zip,
            
            'x_amount'         => $ammount,
            'x_description'    => 'Sample TRansaction',
        );
        
        foreach($data as $var => $val)
            $request->setParameterPost($var,$val);
        
        $response = $request->request('POST');
        $response = explode(self::API_CHAR, $response->getBody());
        
        if($response[0] == 1){
            $response_vars = array(
                'hash'=>$response[37],
                'code'=>$response[6],
                'method'=>$response[51] . ' ' . $response[50],
            );
            $url = $returnURL .'?'.http_build_query($response_vars);
            header("Location: {$url}");die;
        } else {
            $response_vars = array(
                'hash'=>$response[37],
                'error_message'=>$response[3]
            );
            $url = $cancelURL .'?'.http_build_query($response_vars);
            header("Location: {$url}");die;
        }
    }
}