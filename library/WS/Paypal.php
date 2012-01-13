<?php


class WS_Paypal {
    
    const PROXY_HOST    = '127.0.0.1';
    
    const PROXY_PORT    = '808';
    
    const API_USERNAME  = 'tripfa_1317066018_biz_api1.hotmail.com';
    
    const API_PASSWORD  = '1317066055';
    
    const API_SIGNATURE = 'A5v4GyIKrekw6hV6lVLlYyq-Yp4XAZvpijuJgeiKhWK5.e.P-w7E6BgN';
    
    const SBNCODE       = 'PP-ECWizard';
    
    protected $SandboxFlag;
    
    protected $API_Endpoint;
    
    protected $PAYPAL_URL;
    
    protected $version;
    
    public function __construct($sandbox = false) 
    {        
        $this->SandboxFlag = $sandbox;
               
        if($this->SandboxFlag == true) {
            $this->API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
            $this->PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
	} else {
            $this->API_Endpoint = "https://api-3t.paypal.com/nvp";
            $this->PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
	}

	$this->USE_PROXY = false;
	$this->version ="64";

	if (session_id() == "") 
                session_start();
    }
    
    public function CallShortcutExpressCheckout( 
            $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL) 
    {    
        $nvpstr="&PAYMENTREQUEST_0_AMT=". $paymentAmount;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
        $nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
        $nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;

        $_SESSION["currencyCodeType"] = $currencyCodeType;	  
        $_SESSION["PaymentType"] = $paymentType;

        $resArray = $this->_hash_call("SetExpressCheckout", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
        {
            $token = urldecode($resArray["TOKEN"]);
            $_SESSION['TOKEN']=$token;
        }

        return $resArray;
    }
    
    public function CallMarkExpressCheckout( 
            $paymentAmount, $currencyCodeType, $paymentType, $returnURL, 
            $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState,
            $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum) 
    {
        $nvpstr="&PAYMENTREQUEST_0_AMT=". $paymentAmount;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
        $nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
        $nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
        $nvpstr = $nvpstr . "&ADDROVERRIDE=1";
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTONAME=" . $shipToName;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET=" . $shipToStreet;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET2=" . $shipToStreet2;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCITY=" . $shipToCity;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTATE=" . $shipToState;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=" . $shipToCountryCode;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOZIP=" . $shipToZip;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOPHONENUM=" . $phoneNum;

        $_SESSION["currencyCodeType"] = $currencyCodeType;	  
        $_SESSION["PaymentType"] = $paymentType;
        
        $resArray = $this->_hash_call("SetExpressCheckout", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
        {
                $token = urldecode($resArray["TOKEN"]);
                $_SESSION['TOKEN']=$token;
        }

        return $resArray;
    }
    
    public function GetShippingDetails( $token )
    {
        $nvpstr="&TOKEN=" . $token;
        
        $resArray = $this->_hash_call("GetExpressCheckoutDetails",$nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if($ack == "SUCCESS" || $ack=="SUCCESSWITHWARNING")
        {	
                $_SESSION['payer_id'] =	$resArray['PAYERID'];
        } 
        return $resArray;
    }
    
    public function ConfirmPayment( $FinalPaymentAmt )
    {
        $token 				= urlencode($_SESSION['TOKEN']);
        $paymentType 		= urlencode($_SESSION['PaymentType']);
        $currencyCodeType 	= urlencode($_SESSION['currencyCodeType']);
        $payerID 			= urlencode($_SESSION['payer_id']);

        $serverName 		= urlencode($_SERVER['SERVER_NAME']);

        $nvpstr  = '&TOKEN=' . $token . '&PAYERID=' . $payerID . '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType . '&PAYMENTREQUEST_0_AMT=' . $FinalPaymentAmt;
        $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencyCodeType . '&IPADDRESS=' . $serverName; 

        $resArray = $this->_hash_call("DoExpressCheckoutPayment",$nvpstr);
        $ack = strtoupper($resArray["ACK"]);

        return $resArray;
    }
    
    public function DirectPayment( 
            $paymentType, $paymentAmount, $creditCardType, $creditCardNumber,
            $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, 
            $countryCode, $currencyCode )
    {
        $nvpstr = "&AMT=" . $paymentAmount;
        $nvpstr = $nvpstr . "&CURRENCYCODE=" . $currencyCode;
        $nvpstr = $nvpstr . "&PAYMENTACTION=" . $paymentType;
        $nvpstr = $nvpstr . "&CREDITCARDTYPE=" . $creditCardType;
        $nvpstr = $nvpstr . "&ACCT=" . $creditCardNumber;
        $nvpstr = $nvpstr . "&EXPDATE=" . $expDate;
        $nvpstr = $nvpstr . "&CVV2=" . $cvv2;
        $nvpstr = $nvpstr . "&FIRSTNAME=" . $firstName;
        $nvpstr = $nvpstr . "&LASTNAME=" . $lastName;
        $nvpstr = $nvpstr . "&STREET=" . $street;
        $nvpstr = $nvpstr . "&CITY=" . $city;
        $nvpstr = $nvpstr . "&STATE=" . $state;
        $nvpstr = $nvpstr . "&COUNTRYCODE=" . $countryCode;
        $nvpstr = $nvpstr . "&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];

        $resArray = $this->_hash_call("DoDirectPayment", $nvpstr);

        return $resArray;
    }
    
    private function _hash_call($methodName,$nvpStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
        if($this->USE_PROXY) 
                curl_setopt ($ch, CURLOPT_PROXY, self::PROXY_HOST. ":" . self::PROXY_PORT);

        //NVPRequest for submitting to server
        $nvpreq = "METHOD=" . urlencode($methodName);
        $nvpreq.= "&VERSION=" . urlencode($this->version);
        $nvpreq.= "&PWD=" . urlencode(self::API_PASSWORD);
        $nvpreq.= "&USER=" . urlencode(self::API_USERNAME);
        $nvpreq.= "&SIGNATURE=" . urlencode(self::API_SIGNATURE) . $nvpStr;
        $nvpreq.= "&BUTTONSOURCE=" . urlencode(self::SBNCODE);

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec($ch);

        //convrting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;

        if (curl_errno($ch)) 
        {
            $_SESSION['curl_error_no']=curl_errno($ch) ;
            $_SESSION['curl_error_msg']=curl_error($ch);
        } 
        else 
        {
            curl_close($ch);
        }

        return $nvpResArray;
    }
    
    public function RedirectToPayPal($token)
    {
        $payPalURL = $this->PAYPAL_URL . $token;
        header("Location: ".$payPalURL); die;
    }
    
    protected function deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();

        while(strlen($nvpstr))
        {
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }
        return $nvpArray;
    }
    
    public function proceed($ammout, $returnUrl, $cancelUrl)
    {
        $resArray = $this->CallShortcutExpressCheckout ($ammout, 'USD', 'Sale', $returnUrl, $cancelUrl);
        $ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
            $this->RedirectToPayPal($resArray["TOKEN"]);
        else 
            throw new Exception();
    }
}