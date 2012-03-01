<?php 

class YMSG{
    // Login stuff
    protected $user = NULL;
    protected $pass = NULL;
 
    // Details info
    private $verbtype = "Y-m-d H:i:s"; // Output date type for verbosity
    private $verbose = 0; // 0 = no Debug messages ; 1 = Debug messages ; 2 = include curl verbose
 
    // Protocol info
    protected $version = "\x10"; // In hex ! 16 = 10 => YMSG16 ; 15 - 0f => YMSG15
    protected $key = "\x00\x00\x00\x00"; // initial Key
    protected $delim = "\xC0\x80"; // Packet delimitator
 
    // Connection info
    protected $status = 0; // 1 = Online ; 0 = Invisible
    protected $newtoken = 0; // 0 = Use old token if available ; 1 = Always get new token
    protected $connected = 0; // 0 - Disconnected ; 1 - Connected
 
    // Connection Variables
    private $challenge = NULL; // The seed
    private $crumb = NULL; // Crumb
    private $y = NULL; // One of the cookies
    private $t = NULL; // Another cookie
    private $string307 = NULL; // Calculated string for auth
    private $token = NULL; // Connection token
 
    // Yahoo Variables
    private $buddylist = NULL;
    private $ignorelist = NULL;
    protected $fname = NULL;
    protected $lname = NULL;
    protected $pinginterval = NULL;
    protected $pingnr = NULL;
 
    // Resources
    protected $ch = NULL; // Curl Resource
    protected $yahoo_server = 'scs.msg.yahoo.com'; // YMSGR server
    protected $yahoo_port = 5050; // YMSGR port
    protected $conn_yahoo = NULL; // Socket Resource
 
    // Colors
    protected $black = "\033[30m";
    protected $red = "\033[31m";
    protected $green = "\033[32m";
    protected $blue = "\033[34m";
    protected $magenta = "\033[35m";
    protected $cyan = "\033[36m";
    protected $white = "\033[37m";
 
 
    protected function verblog($message, $color = "white"){
        switch($color){
            case "black":
                $color = $this->black;
                break;
            case "red":
                $color = $this->red;
                break;
            case "green":
                $color = $this->green;
                break;
            case "blue":
                $color = $this->blue;
                break;
            case "magenta":
                $color = $this->magenta;
                break;
            case "cyan":
                $color = $this->cyan;
                break;
            case "white":
            default:
                $color = $this->white;
                break;
        }
        return TRUE;
    }
 
    public function usenewtoken($token){
        if($token == 0 || $token == 1){
            if($token == 0){
                $this->verblog("usenewtoken() Use old token if available.", 'blue');
            }else{
                $this->verblog("usenewtoken() Always get new token.", 'blue');
            }
            $this->newtoken = $token;
        }
        return TRUE;
    }
 
    public function ConnectedStatus(){
        return $this->connected;
    }
 
    private function verbtype($type){
        $this->verblog("verbtype() Setting Verbosity date type to ".$type." .", 'blue');
        $this->verbtype = $type;
    }
 
    public function YahooUser($user){
        $this->user = trim($user);
        $this->verblog("YahooUser() set to ".$this->user, 'blue');
    }
 
    public function YahooPass($pass){
        $this->pass = trim($pass);
        $this->verblog("YahooPass() set pass to ".$this->pass, 'blue');
    }
 
    public function Verbosity($verbose){
        switch($verbose){
            case 0:
                if($this->verbose == 0){
                }else{
                    $this->verbose = 0;
                }
                break;
            case 1:
                if($this->verbose > 0){
                }else{
                    $this->verbose = 1;
                }
                break;
            case 2:
                if($this->verbose > 1){
                }else{
                    $this->verbose = 2;
                }
                break;
            default:
                return FALSE;
                break;
        }
        return TRUE;
    }
 
    private function MyCurlInit(){
        if(($this->ch = curl_init()) === FALSE){
            $this->verblog("MyCurlInit() Failed to initialize curl session.", 'red');
            return FALSE;
        }
        $this->verblog("MyCurlInit() Created curl session.", 'green');
    }
 
    private function MyCurlGet($url){
        if(!is_resource($this->ch)){
            $this->verblog("MyCurlGet() CH isn't a valid resource.", 'red');
            if(($this->MyCurlInit()) === FALSE){
                $this->verblog("MyCurlGet()::MyCurlInit() failed creating again.", 'red');
                return FALSE;
            }
        }
            curl_setopt($this->ch, CURLOPT_URL, $url);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        if($this->verbose > 1){
                curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        }
            return(curl_exec($this->ch));
    }
 
 
    private function MyCurlClose(){
        $this->verblog("MyCurlClose() Closed curl resource.", 'green');
        curl_close($this->ch);
        return TRUE;
    }
 
    public function ascii2hex($ascii){
        $hex = '';
            for($i=0;$i<strlen($ascii);$i++){
            $byte = dechex(ord($ascii{$i}));
            $byte = str_repeat('0', 2 - strlen($byte)).$byte;
            $hex .= $byte;
        }
            return($hex);
    }
 
    public function hex2ascii($hex){
        $ascii = '';
        $hex = str_replace(" ", "", $hex);
        for($i=0;$i<strlen($hex);$i=$i+2){
            $ascii .= chr(hexdec(substr($hex, $i, 2)));
        }
        return($ascii);
    }
 
    private function SockINIT(){
        $this->verblog("YahooConnect() Creating conn_yahooo socket.", 'blue');
        if(($this->conn_yahoo = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE){
            $this->verblog("SockINIT()::socket_create() failed with reason: ".socket_strerror(socket_last_error($this->conn_yahoo)), 'red');
                return FALSE;
        }
        if((@socket_connect($this->conn_yahoo, $this->yahoo_server, $this->yahoo_port)) === FALSE){
            $this->verblog("SockINIT()::socket_connect() failed with reason: ".socket_strerror(socket_last_error($this->conn_yahoo)), 'red');
                return FALSE;
        }
        socket_set_option($this->conn_yahoo, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 5, "usec" => 0));
        $this->verblog("SockINIT() created connection socket.", 'green');
        return TRUE;
    }
 
    private function sendsock($packet){
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("sendsock() If conn_yahoo isn't a resource, I can't work with it.", 'red');
            return FALSE;
        }
            $header = substr($packet, 11, 1);
            $this->key = substr($packet, 16, 4);
                $this->verblog("CLIENT > SERVER: Sending Packet Size: ".strlen($packet)." Bytes", 'magenta');
                $this->verblog("CLIENT > SERVER: Sending Header: 0x".strtoupper($this->ascii2hex($header)), 'magenta');
        $this->verblog("CLIENT > SERVER: Session: ".$this->ascii2hex($this->key), 'magenta');
        $this->verblog("CLIENT > SERVER: Sending Packet: ".substr($packet, 20), 'magenta');
        if($this->verbose > 1){
            $this->verblog("CLIENT > SERVER: FULL Sending packet in HEX:\n".$this->ascii2hex($packet), 'magenta');
        }
            socket_write($this->conn_yahoo, $packet, strlen($packet));
            return TRUE;
    }
 
    private function recvsock(){
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("recvsock() Conn_yahoo isn't a resource. I can't work with it.", 'red');
            return FALSE;
        }
        $return = '';
        do{
            if(!empty($return)){
                $this->verblog("recvsock() Packet didn't finish in C080 Getting next.", 'blue');
            }
                if(($return .= socket_read($this->conn_yahoo, 1441)) === FALSE){
                $this->verblog("recvsock() Error reading.", 'red');
                return FALSE;
            }
            if(substr($return, 11, 1) == "\x4C" || empty($return)){ break; }
            usleep(100);
        }while(substr($return, -2) != "\xC0\x80" && substr($return, -3) != "\xC0\x80\x00");
        if(empty($return)){ return FALSE; }
        $packetcount = substr_count($return, "YMSG\x00\x10\x00");
        if($packetcount > 1){
            $this->verblog("SERVER > CLIENT: Received ".$packetcount." packets from server.", 'cyan');
            $return = explode("YMSG\x00\x10\x00", $return);
            for($i = 1; $i <= $packetcount; $i++){
                $packet[$i] = "YMSG\x00".$this->version."\x00".$return[$i];
                    $header = substr($packet[$i], 11, 1);
                    $this->key = substr($packet[$i], 16, 4);
                        $this->verblog("SERVER > CLIENT: [".$i."] Receiving Packet Size: ".strlen($packet[$i])." Bytes", 'cyan');
                        $this->verblog("SERVER > CLIENT: [".$i."] Receiving Header: 0x".strtoupper($this->ascii2hex($header)), 'cyan');
                $this->verblog("SERVER > CLIENT: [".$i."] Session: ".$this->ascii2hex($this->key), 'cyan');
                $this->verblog("SERVER > CLIENT: [".$i."] Receiving Packet: ".substr($packet[$i], 20), 'cyan');
                if($this->verbose > 1){
                    $this->verblog("SERVER > CLIENT: [".$i."] FULL Receiving packet in HEX:\n".$this->ascii2hex($packet[$i]), 'cyan');
                }
                $packet[$i] = array($header, $packet[$i]);
            }
            return $packet;
        }else if(strlen($return) > 0){
                $header = substr($return, 11, 1);
                $this->key = substr($return, 16, 4);
                    $this->verblog("SERVER > CLIENT: Receiving Packet Size: ".strlen($return)." Bytes", 'cyan');
                    $this->verblog("SERVER > CLIENT: Receiving Header: 0x".strtoupper($this->ascii2hex($header)), 'cyan');
            $this->verblog("SERVER > CLIENT: Session: ".$this->ascii2hex($this->key), 'cyan');
            $this->verblog("SERVER > CLIENT: Receiving Packet: ".substr($return, 20), 'cyan');
            if($this->verbose > 1){
                $this->verblog("SERVER > CLIENT: FULL Receiving packet in HEX:\n".$this->ascii2hex($return), 'cyan');
            }
                return(array($header, $return));
        }else{
            $this->verblog("SERVER->CLIENT: There was a problem here.", 'red');
            return FALSE;
        }
    }
 
    private function SockClose(){
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("Can't close conn_yahoo if it's not a socket resource.", 'red');
            return FALSE;
        }
        $this->verblog("Closing conn_yahoo socket.", 'green');
        socket_close($this->conn_yahoo);
        $this->connected = 0;
        return TRUE;
    }
 
    private function Y64E($source){
        $yahoo64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789._";
        $inlen = 16;
        $in = 0;
        $dest = "";
        for(; $inlen >=3; $inlen -=3){
            $dest .= $yahoo64[ord($source[$in])>>2];
            $dest .= $yahoo64[((ord($source[$in])<<4)&0x30) | (ord($source[$in+1])>>4)];
            $dest .= $yahoo64[((ord($source[$in+1])<<2)&0x3C) | (ord($source[$in+2])>>6)];
            $dest .= $yahoo64[ord($source[$in+2])&0x3F];
            $in += 3;
        }
        if($inlen > 0){
            $dest .= $yahoo64[ord($source[$in])>>2];
            $fragment = ((ord($source[$in])<<4)&0x30);
            if($inlen > 1){
                $fragment |= (ord($source[$in+1])>>4);
            }
            $dest.=$yahoo64[$fragment];
            if($inlen < 2){
                $dest .= "-";
            }else{
                $dest .= $yahoo64[((ord($source[$in+1])<<2)&0x3c)];
            }
            $dest .= "-";
        }
        return($dest);
    }
 
    private function pachet($header, $packet = ''){
        if(empty($header)){
            $this->verblog("pachet() Empty header.", 'red');
            return FALSE;
        }
        if(strlen($header) != 1){
            $this->verblog("pachet() Wrong packet length.", 'red');
            return FALSE;
        }
        $value = "YMSG"; // Start # 4 bytes
        $value .= "\x00"; // NULL # 1 byte
            $value .= $this->version; // Version # 1 byte
            $value .= str_repeat("\x00", 2); // 2 NULLs # 2 bytes
            $value .= chr(intval(strlen($packet)/256)).chr(fmod(strlen($packet),256)); // Size # 2 bytes
            $value .= "\x00"; // NULL # 1 byte
            $value .= $header; // Packet type # 1 byte
            if($this->status == 0){
                    $value .= "\x00\x00\x00\x0C"; // 00 00 00 0C ( Invisible ) # 4 bytes
            }else{
                    $value .= str_repeat("\x00", 4); // 4 NULLs ( Available ) # 4 bytes
            }
            $value .= $this->key; // The Session Key # 4 bytes
        if(strlen($value) != 20){
            $this->verblog("pachet() Malformed 20byte packet header: ".$this->ascii2hex($value)." ".strlen($value)." size.", 'red');
            $this->verblog("pachet() Packet content: ".$this->ascii2hex($value.$packet), 'red');
            return FALSE;
        }
            return $value.$packet;
    }
 
    protected function YahooVRFY(){
        if($this->connected == 1){
            $this->verblog("YahooVRFY() Don't do this if already connected.", 'red');
            return FALSE;
        }
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("YahooVRFY() Can't verify if there is no connection.", 'red');
            return FALSE;
        }
        $this->verblog("YahooConnect() Sending VRFY 0x4C packet.", 'green');
        if(($this->sendsock($this->pachet("\x4C"))) === FALSE){
            $this->verblog("YahooVRFY() Problem sending packet.", 'red');
            return FALSE;
        }
        $return = $this->recvsock();
        if($return[0] != "\x4C"){
            $this->verblog("YahooVRFY() Expected 0x4C but got 0x".$this->ascii2hex($return[0]), 'red');
            return FALSE;
        }else{
            return $return[1];
        }
    }
 
    protected function processtokenerrors($error){
        switch($error){
            case 1212:
                $this->verblog("processtokenerrors() Invalid ID or password. Please try again.", 'red');
                return FALSE;
                break;
            case 1213:
                $this->verblog("processtokenerrors() As a security precaution please enter your Yahoo! ID and password and type in the code you see in the picture below.", 'red');
                return FALSE;
                break;
            case 1214:
                $this->verblog("processtokenerrors() Invalid ID or password. Please try again and type the text you see in the picture below", 'red');
                return FALSE;
                break;
            case 1235:
                $this->verblog("processtokenerrors() This ID is not yet taken", 'red');
                return FALSE;
                break;
            case 1236:
                $this->verblog("processtokenerrors() Invalid ID or password. Please try again.", 'red');
                return FALSE;
                break;
            case 100:
                $this->verblog("processtokenerrors() Required field missing", 'red');
                return FALSE;
                break;
            default:
                $this->verblog("processtokenerrors() No errors, continue.", 'red');
                return TRUE;
                break;
        }
        return TRUE;
    }
 
    protected function getyt(){
        if($this->connected == 1){
            $this->verblog("getyt() Don't do this if already connected.", 'red');
            return FALSE;
        }
        if(($this->MyCurlInit()) === FALSE){
            $this->verblog("getyt()::MyCurlInit() Can't create resource.", 'red');
            return FALSE;
        }
        $fh = @file_get_contents("yahoo_tokens");
        $search = "|".$this->user.":([^\n]*)|is";
        if($this->newtoken == 1){
            $this->verblog("getyt() Always getting new token.", 'blue');
            $ourtoken = $this->MyCurlGet('https://login.yahoo.com/config/pwtoken_get?src=ymsgr&ts=&login='.$this->user.'&passwd='.urlencode($this->pass));
            $this->processtokenerrors($ourtoken);
            preg_match_all("|ymsgr=([^\n]*)\n|", $ourtoken, $token);
            if(empty($token[1][0])){
                $this->verblog("getyt() Got empty token: ".trim($ourtoken), 'red');
                return FALSE;
            }else{
                $this->token = trim($token[1][0]);
                $this->verblog("getyt() Token set to ".$this->token, 'green');
                $fh = fopen("yahoo_tokens", "a+");
                fwrite($fh, $this->user.":".$this->token."\n");
                fclose($fh);
                $this->verblog("getyt() Token saved to file.", 'green');
            }
        }else if(!preg_match($search, $fh, $out)){
            $this->verblog("getyt() No saved token, getting one.", 'blue');
            $ourtoken = $this->MyCurlGet('https://login.yahoo.com/config/pwtoken_get?src=ymsgr&ts=&login='.$this->user.'&passwd='.urlencode($this->pass));
            $this->processtokenerrors($ourtoken);
            preg_match_all("|ymsgr=([^\n]*)\n|", $ourtoken, $token);
            if(empty($token[1][0])){
                $this->verblog("getyt() Got empty token: ".trim($ourtoken), 'red');
                return FALSE;
            }else{
                $this->token = trim($token[1][0]);
                $this->verblog("getyt() Token set to ".$this->token, 'green');
                $fh = fopen("yahoo_tokens", "a+");
                fwrite($fh, $this->user.":".$this->token."\n");
                fclose($fh);
                $this->verblog("getyt() Token saved to file.", 'green');
            }
        }else{
            $this->token = trim($out[1]);
            $this->verblog("getyt() Using already saved token: ".$out[0], 'green');
            $this->verblog("getyt() Token set to: ".$this->token, 'green');
        }
        $yt307 = $this->MyCurlGet('https://login.yahoo.com/config/pwtoken_login?src=ymsgr&ts=&token='.$this->token);
        if($this->verbose > 1){
            $this->verblog($yt307);
        }
        if(!preg_match_all("|crumb=([^\n]*)\nY=([^;]*);([^\n]*)\nT=([^;]*);|s", $yt307, $yt)){
            $this->verblog("getyt() Existing token is invalid, getting new one.", 'blue');
            $ourtoken = $this->MyCurlGet('https://login.yahoo.com/config/pwtoken_get?src=ymsgr&ts=&login='.$this->user.'&passwd='.urlencode($this->pass));
            preg_match_all("|ymsgr=([^\n]*)\n|", $ourtoken, $token);
            if(empty($token[1][0])){
                $this->verblog("getyt() Got empty token: ".trim($ourtoken), 'red');
                return FALSE;
            }else{
                $this->token = trim($token[1][0]);
                $this->verblog("getyt() Token set to ".$this->token, 'green');
                $yahoo_tokens = file_get_contents("yahoo_tokens");
                $yahoo_tokens = preg_replace('|'.$this->user.':(.*)\n|', $this->user.":".$this->token."\n", $yahoo_tokens);
                $fh = fopen("yahoo_tokens", "w+");
                fwrite($fh, $yahoo_tokens);
                fclose($fh);
                $this->verblog("getyt() Old saved token replaced.", 'green');
            }
            $yt307 = $this->MyCurlGet('https://login.yahoo.com/config/pwtoken_login?src=ymsgr&ts=&token='.$this->token);
            preg_match_all("|crumb=([^\n]*)\nY=([^;]*);([^\n]*)\nT=([^;]*);|s", $yt307, $yt);
        }
        $this->MyCurlClose();
        if(empty($yt[1][0])){
            $this->verblog("getyt() Empty crumb.");
            return FALSE;
        }else{
            $this->crumb = trim($yt[1][0]);
            $this->verblog("getyt() Crumb set to ".$this->crumb);
        }
 
        if(empty($yt[2][0])){
            $this->verblog("getyt() Empty Y cookie.");
            return FALSE;
        }else{
            $this->y = $yt[2][0];
            $this->verblog("getyt() Y set to ".$this->y);
        }
        if(empty($yt[4][0])){
            $this->verblog("getyt() Empty T cookie.");
            return FALSE;
        }else{
            $this->t = $yt[4][0];
            $this->verblog("getyt() T set to ".$this->t);
        }
        return TRUE;
    }
 
    protected function gen307(){
        if(!$this->getyt()){
            $this->verblog("gen307()::getyt Getting CRUMB, Y and T");
            return FALSE;
        }
        $this->verblog("YahooConnect() Getting Y, T, Crumb and generating String307.");
        if($this->verbose > 1){
            $this->verblog("gen307() Step1: crumb.challenge : ".$this->crumb.$this->challenge);
            $this->verblog("gen307() Step2: md5(crumb.challenge): ".md5($this->crumb.$this->challenge));
            $this->verblog("gen307() Step3: hex2ascii(md5(crumb.challenge)): ".$this->hex2ascii(md5($this->crumb.$this->challenge)));
            $this->verblog("gen307() Step4: y64e(hex2ascii(md5(crumb.challenge))): ".$this->Y64E($this->hex2ascii(md5($this->crumb.$this->challenge))));
        }
        $this->string307 = $this->Y64E($this->hex2ascii(md5($this->crumb.$this->challenge)));
        $this->verblog("genyt307() String307 set to ".$this->string307." .");
        return TRUE;
    }
 
    private function YahooAUTH(){
        if($this->connected == 1){
            $this->verblog("YahooAUTH() Can't be used if already connected.", 'red');
            return FALSE;
        }
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("YahooAUTH()::SockINIT() Can't auth without an open socket.", 'red');
            return FALSE;
        }
        $this->verblog("YahooConnect() Sending AUTH 0x57 packet.", 'green');
            $packet = "1".$this->delim.$this->user.$this->delim;
        $this->sendsock($this->pachet("\x57", $packet));
        $return = $this->recvsock();
        if($return[0] != "\x57"){
            $this->verblog("YahooAUTH() Expected 0x57 on return but got 0x".$this->ascii2hex($return[0]), 'red');
            return FALSE;
        }
        if($this->hex2ascii($this->key) == "00000000"){
            $this->verblog("YahooAUTH() Empty KEY.", 'red');
            return FALSE;
        }
        if(!stristr($return[1], "\xC0\x8094\xC0\x80")){
            $this->verblog("YahooAUTH() Expected to have 0xC0 0x80 94 0xC0 0x80.", 'red');
            return FALSE;
        }
        preg_match_all("|\xC0\x8094\xC0\x80(.*)\xC0\x80|", $return[1], $challenge);
        if(empty($challenge[1][0])){
            $this->verblog("YahooAUTH() Challenge is empty.", 'red');
            return FALSE;
        }else{
            $this->challenge = trim($challenge[1][0]);
            $this->verblog("YahooAUTH() Challenge set to ".$this->challenge, 'green');
        }
        return TRUE;
    }
 
    private function YahooLogin(){
        if($this->connected == 1){
            $this->verblog("YahooLogin() don't do this if you're already connected.", 'red');
            return FALSE;
        }
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("YahooLogin() Can't work if conn_yahoo isn't a resource.", 'red');
            return FALSE;
        }
        $this->verblog("YahooConnect() Sending Login 0x54 packet.");
            $packet = "1".$this->delim.$this->user.$this->delim;
            $packet .= "0".$this->delim.$this->user.$this->delim;
            $packet .= "277".$this->delim.$this->y."; path=/; domain=.yahoo.com".$this->delim;
            $packet .= "278".$this->delim.$this->t."; path=/; domain=.yahoo.com".$this->delim;
            $packet .= "307".$this->delim.$this->string307.$this->delim;
            $packet .= "244".$this->delim."4194239".$this->delim;
            $packet .= "2".$this->delim.$this->user.$this->delim;
            $packet .= "2".$this->delim."1".$this->delim;
            $packet .= "98".$this->delim."us".$this->delim;
            $packet .= "135".$this->delim."9.0.0.2162".$this->delim;
            $this->sendsock($this->pachet("\x54", $packet));
        $return = $this->recvsock();
        if($return[0] != "\x55"){
            $this->verblog("YahooLogin() expects a 0x55 but got 0x".$this->ascii2hex($return[0])." .");
            return FALSE;
        }else{
            preg_match_all('|c080323136c080(.*)c080323534c080(.*)c080323735c080|is', $this->ascii2hex($return[1]), $out);
            //$this->fname = $this->hex2ascii($out[1][0]);
            $this->verblog("YahooLogin() First name set to: ".$this->fname);
            //$this->lname = $this->hex2ascii($out[2][0]);
            $this->verblog("YahooLogin() Last name set to: ".$this->lname);
            return TRUE;
        }
    }
 
    private function getusergroup($user){
        foreach($this->buddylist as $buddylist => $name){
            foreach($name as $userul => $info){
                if($userul == $user){
                    return $buddylist;
                }
            }
        }
        return FALSE;
    }
 
    public function YahooBuddyList($packet = NULL){
        // We may receive multiple packets here.
        if(!is_resource($this->conn_yahoo)){
            $this->verblog("YahooBuddyList() Can't work if conn_yahoo isn't a resource.");
            return FALSE;
        }
        if($this->connected == 1){
            return($this->buddylist);
        }
        switch($packet[0]){
            case "\xF1":
                $this->verblog("YahooBuddyList() (0xF1) Parsing Buddy list.");
                $packet = $this->ascii2hex($packet[1]);
                preg_match_all('|c080333030c080333138c080([a-z0-9]+?)c080333033c080333139c080|', $packet, $outf1);
                if(is_array($outf1[1]) && !empty($outf1[1][0])){
                    foreach($outf1[1] as $list){
                        preg_match('|3635c080(.*)c080333032c080333139c080|', $list, $name);
                        $name = $this->hex2ascii($name[1]);
                        preg_match_all('|c080333030c080333139c08037c080([a-z0-9]+?)c080333031c080333139|', $list, $buddys);
                        foreach($buddys[1] as $buddy){
                            $buddy = $this->hex2ascii($buddy);
                            $status = "offline";
                            if(stristr($buddy, "\xC0\x80223\xC0\x801")){
                                $buddy = str_replace("\xC0\x80223\xC0\x801", '', $buddy);
                                $status = "Didn't accept your add request yet.";
                            }
                            $buddylist[$name][$buddy] = NULL;
                            $this->buddylist[$name][$buddy]['status'] = $status;
                            $status = NULL;
                        }
                        $name = NULL;
                    }
                }
                preg_match_all('|c080333030c080333230c08037c080([a-z0-9]+?)c080333031c080333230|', $packet, $ignored);
                if(is_array($ignored[1]) && !empty($ignored[1][0])){
                    foreach($ignored[1] as $person){
                        $this->ignorelist[] = $this->hex2ascii($person);
                    }
                }
                $outf1 = NULL;
                break;
            case "\xF0":
                $this->verblog("YahooBuddyList() (0xF0) Parsing Buddy statuses.");
                $packet = $this->ascii2hex($packet[1]);
                preg_match_all('|c080333030c080333135c08037c080([a-z0-9]+?)c0803130c080([a-f0-9]+?)c080([a-f0-9]+)c080333031c080333135|', $packet, $outf0);
                if(is_array($outf0[1]) && !empty($outf0[1][0])){
                    foreach($outf0[1] as $key => $user){
                        $user = $this->hex2ascii($user);
                        $buddylist = $this->getusergroup($user);
                        $this->buddylist[$buddylist][$user]['status'] = "online";
                        switch($outf0[2][$key]){
                            case "31":
                                $msg = "(AWAY) Be Right Back";
                                break;
                            case "32":
                                $msg = "(AWAY) Busy";
                                break;
                            case "33":
                                $msg = "(AWAY) Not At Home";
                                break;
                            case "34":
                                $msg = "(AWAY) Not At Desk";
                                break;
                            case "35":
                                $msg = "(AWAY) Not in Office";
                                break;
                            case "36":
                                $msg = "(AWAY) On the Phone";
                                break;
                            case "37":
                                $msg = "(AWAY) On Vacation";
                                break;
                            case "38":
                                $msg = "(AWAY) Out to lunch";
                                break;
                            case "39":
                                $msg = "(AWAY) Stepped out";
                                break;
                            case "3939":
                                preg_match('|c0803139c080([a-f0-9]+?)c0803937c08031c080|is', $outf0[3][0], $sts);
                                $msg = "(AVAILABLE) ".$this->hex2ascii($sts[1]);
                                break;
                            default:
                                $msg = "";
                                break;
                        }
                        $this->buddylist[$buddylist][$user]['message'] = $msg;
                    }
                }
                break;
            default:
                return FALSE;
                break;
        }
        return TRUE;
    }
 
    public function YahooKeepAlive(){
        // This has to be send every 60 seconds.
        if($this->connected == 0){
            $this->verblog("YahooKeepAlive() Can't do this if not connected.");
            return FALSE;
        }
        $this->verblog("YahooKeepAlive() Sending Keep Alive packet (0x8A) .");
        $packet = "0".$this->delim.$this->user.$this->delim;
        return $this->sendsock($this->pachet("\x8A", $packet));
    }
 
    public function YahooAddAbuddy($dest, $msg = '', $list = 'Buddies'){
        if($this->connected == 0){
            $this->verblog("YahooAddABuddy() Can't do this if not connected.", 'red');
            return FALSE;
        }
        if(empty($dest)){
            $this->verblog("YahooAddABuddy() No user given.", 'red');
            return FALSE;
        }
        $slist = $this->getusergroup($dest);
        if(!empty($slist)){
            $this->verblog("YahooAddABuddy() Can't add ".$dest." since he/she already is in '".$slist."'", 'red');
            return FALSE;
        }
        if(!empty($msg)){
            $this->verblog("YahooAddABuddy() Adding ".$dest." in ".$list." with message: ".$msg, 'green');
        }else{
            $this->verblog("YahooAddABuddy() Adding ".$dest." in ".$list, 'green');
        }
            $packet = "1".$this->delim.$this->user.$this->delim;
            $packet .= "14".$this->delim.$msg.$this->delim;
            $packet .= "65".$this->delim.$list.$this->delim;
            $packet .= "97".$this->delim."1".$this->delim;
        if(!empty($this->fname)){
            $packet .= "216".$this->delim.$this->fname.$this->delim;
        }
        if(!empty($this->lname)){
            $packet .= "254".$this->delim.$this->lname.$this->delim;
        }
            $packet .= "302".$this->delim."319".$this->delim;
            $packet .= "300".$this->delim."319".$this->delim;
            $packet .= "7".$this->delim.$dest.$this->delim;
            $packet .= "301".$this->delim."319".$this->delim;
            $packet .= "303".$this->delim."319".$this->delim;
            return $this->sendsock($this->pachet("\x83", $packet));
    }
 
    public function YahooDenyABuddy($dest, $msg = ''){
        // The great fun here is that even after you have accepted the add request, you can still deny it and remove yourself from his list.
        if($this->connected == 0){
            $this->verblog("YahooDenyABuddy() Can't do this if not connected.");
            return FALSE;
        }
        if(empty($dest)){
            $this->verblog("YahooDenyABuddy() No user given.");
            return FALSE;
        }
        if(empty($msg)){
            $this->verblog("YahooDenyABuddy() Denying buddy ".$dest);
        }else{
            $this->verblog("YahooDenyABuddy() Denying buddy ".$dest." with message: ".$msg);
        }
            $packet = "1".$this->delim.$this->user.$this->delim;
            $packet .= "5".$this->delim.$dest.$this->delim;
            $packet .= "13".$this->delim."2".$this->delim; // Reject Authorization
        if(!empty($msg)){
                $packet .= "14".$this->delim.$msg.$this->delim;
        }
            return $this->sendsock($this->pachet("\xD6", $packet));
    }
 
    private function YahooAddABuddyRequest($packet){
        if(empty($packet[1])){
            $this->verblog("YahooAddABuddyRequest() No packet given.");
            return FALSE;
        }
        if($packet[0] != "\xD6"){
            $this->verblog("YahooAddABuddyRequest() You've given me a wrong packet type to parse, I need 0xD6, got 0x".$this->ascii2hex($packet[0]));
            return FALSE;
        }
        $packet = $this->ascii2hex($packet[1]);
        preg_match('|34c080([a-f0-9]+?)c08035c080([a-f0-9]+?)c080|is', $packet, $request);
        if(stristr($packet, 'c0803133c08032c080')){
            if(preg_match('|c0803134c080([a-f0-9]+?)c080|is', $packet, $message)){
                $this->verblog("YahooAddABuddyRequest() ".$this->hex2ascii($request[1])." denied our add buddy request: ".$this->hex2ascii($message[1]), 'red');
            }else{
                $this->verblog("YahooAddABuddyRequest() ".$this->hex2ascii($request[1])." denied our request.", 'red');
            }
            unset($this->buddylist[$this->getusergroup($this->hex2ascii($request[1]))][$this->hex2ascii($request[1])]);
            return TRUE;
        }
        if(isset($request[1]) && isset($request[2]) && $this->hex2ascii($request[2]) == $this->user){
            if(stristr($packet, 'c080323136c080')){
                preg_match('|c080323136c080([a-f0-9]+?)c080|is', $packet, $fname);
                $fname = $this->hex2ascii($fname[1]);
            }
            if(stristr($packet, 'c080323534c080')){
                preg_match('|c080323534c080([a-f0-9]+?)c080|is', $packet, $lname);
                $lname = $this->hex2ascii($lname[1]);
            }
            if(stristr($packet, 'c0803134c080')){
                preg_match('|c0803134c080([a-f0-9]+?)c080|is', $packet, $message);
                $message = $this->hex2ascii($message[1]);
            }
            $reply = "YahooAddABuddyRequest() ".$this->hex2ascii($request[1]);
            if((isset($fname) && !empty($fname)) || (isset($lname) && !empty($lname))){
                $reply .= " (";
                            if((isset($fname) && !empty($fname)) && (isset($lname) && !empty($lname))){
                    $reply .= $fname." ".$lname;
                }else{
                    if(isset($fname) && !empty($fname)){
                        $reply .= $fname;
                    }else{
                        $reply .= $lname;
                    }
                }
                $reply .= ")";
            }
 
            if(isset($message[1]) && !empty($message[1])){
                $reply .= " wants to add you to his/hers buddy list: ".$message;
            }else{
                $reply .= " wants to add you to his/hers buddy list.";
            }
            $this->verblog($reply);
        }else{
            $this->verblog("YahooAddABuddyRequest() Error getting buddy request.");
            return FALSE;
        }
        return TRUE;
    }
 
    public function YahooRemoveABuddy($user){
        if($this->connected == 0){
            $this->verblog("YahooRemoveABuddy() Unable to process this if not connected.");
            return FALSE;
        }
        if(!$this->getusergroup($user)){
            $this->verblog("YahooRemoveABuddy() ".$user." isn't in your buddy list.");
            return FALSE;
        }
        $group = $this->getusergroup($user);
        $packet = "1".$this->delim.$this->user.$this->delim;
        $packet .= "7".$this->delim.$user.$this->delim;
        $packet .= "65".$this->delim.$group.$this->delim;
        $packet .= "66".$this->delim."0".$this->delim;
        $this->verblog("YahooRemoveABuddy() Removing ".$user." from your '".$group."' list.");
        unset($this->buddylist[$group][$user]);
        return $this->sendsock($this->pachet("\x84", $packet));
    }
 
    public function YahooAvatarLink($dest){
        if($this->connected == 0){
            $this->verblog("YahooAvatarLink() Can't do this if not connected.");
            return FALSE;
        }
        if(empty($dest)){
            $this->verblog("YahooAvatarLink() No user given.");
            return FALSE;
        }
        $this->verblog("YahooAvatarLink() Getting ".$dest."'s avatar link.");
        // C0 $packeti="1".$delim.$user.$delim."5".$delim.$vict.$delim."198".$delim."0".$delim.'197'.$delim.$delim; // not working anymore
        // C1 $packeti="1".$delim.$user.$delim."5".$delim.$vict.$delim."206".$delim."2".$delim; // not working anymore
        // BE $packeti="1".$delim.$user.$delim."5".$delim.$vict.$delim."13".$delim."1".$delim; // Only works for people using yahoo messenger
        $packet = "1".$this->delim.$this->user.$this->delim;
        $packet .= "5".$this->delim.$dest.$this->delim;
        $packet .= "13".$this->delim."1".$this->delim;
        return $this->sendsock($this->pachet("\xBE", $packet));
    }
 
    public function YahooPrivateMessage($dest, $msg){
        /*
             63 - imvironment  string;11
             64 - imvironment enabled/allowed
                        0 - enabled imwironment ;0 - no imvironment
                        2 - disabled                '' - empty cause we don;t do these
        */
        if($this->connected == 0){
            $this->verblog("YahooPrivateMessage() Can't do this if not connected.");
            return FALSE;
        }
        if(empty($dest)){
            $this->verblog("YahooPrivateMessage() No user given.");
            return FALSE;
        }
        if(empty($msg)){
            $this->verblog("YahooPrivateMessage() No message given.");
            return FALSE;
        }
        $this->verblog("YahooPrivateMessage() Sending to ".$dest." message: ".$msg);
            $packet = "1".$this->delim.$this->user.$this->delim;
            $packet .= "5".$this->delim.$dest.$this->delim;
            $packet .= '97'.$this->delim.'1'.$this->delim;
            $packet .= "14".$this->delim.$msg.$this->delim;
            $packet .= '63'.$this->delim.';0'.$this->delim;
            $packet .= '64'.$this->delim.'0'.$this->delim;
            $packet .= '1002'.$this->delim.'1'.$this->delim;
            $packet .= '206'.$this->delim.'0'.$this->delim;
            return $this->sendsock($this->pachet("\x06", $packet));
    }
 
    private function YahooIncommingMessage($packet){
        $_messages = array();
        if(empty($packet[1])){
            $this->verblog("YahooIncommingMessage() No packet given.");
            return FALSE;
        }
        if($packet[0] != "\x06"){
            $this->verblog("YahooIncommingMessage() You've given me a wrong packet type to parse, I need 0x06, got 0x".$this->ascii2hex($packet[0]));
            return FALSE;
        }
        $this->verblog("YahooIncommingMessage() (0x06) parsing incomming message.");
        $packet = $this->ascii2hex($packet[1]);
        if(stristr($packet, "c0803633c080") && stristr($packet, "c0803634c080")){
            preg_match_all('|34c080([a-f0-9]+?)c08035c080'.$this->ascii2hex($this->user).'c0803134c080([a-f0-9]+?)c080|is', $packet, $messages);
            foreach($messages[1] as $key => $source){
                $this->verblog("YahooIncommingMessage() Message at ".date("Y-m-d H:i:s")." from ".$this->hex2ascii($source)." : ".$this->hex2ascii($messages[2][$key]));
                $_messages[] = $this->hex2ascii($messages[2][$key]);
            }
        }else{
            preg_match_all('|3331c08036c0803332c08036c08034c080([a-f0-9]+?)c08035c080'.$this->ascii2hex($this->user).'c0803134c080([a-f0-9]+?)c0803135c080([a-f0-9]+?)c0803937c08031c080|is', $packet, $messages);
            foreach($messages[1] as $key => $source){
                $this->verblog("YahooIncommingMessage() Offline message at ".date("Y-m-d H:i:s", $this->hex2ascii($messages[3][$key]))." from ".$this->hex2ascii($source)." : ".$this->hex2ascii($messages[2][$key]));
                $_messages[] = $this->hex2ascii($messages[2][$key]);
            }
        }
        return $_messages;
    }
 
    private function YahooNewMail($packet){
        if(empty($packet[1])){
            $this->verblog("YahooNewMail() No packet given.");
            return FALSE;
        }
        if($packet[0] != "\x0B"){
            $this->verblog("YahooNewMail() You've given me a wrong packet type to parse, I need 0x0B, got 0x".$this->ascii2hex($packet[0]));
            return FALSE;
        }
        $this->verblog("YahooNewMail() (0x0B) parsing mail number.");
        $packet = $this->ascii2hex($packet[1]);
        if(stristr($packet, "c0803138c080")){
            preg_match('|39c08031c0803138c080([a-f0-9]+?)c0803432c080([a-f0-9]+?)c0803433c080([a-f0-9]+?)c080|is', $packet, $mails);
            $this->verblog("YahooNewMail() You've got a mail from ".$this->hex2ascii($mails[3])." (".$this->hex2ascii($mails[2]).") : ".$this->hex2ascii($mails[1]));
        }else{
            preg_match('|39c080([a-f0-9]+?)c080|is', $packet, $mails);
            if(isset($mails[1]) && is_numeric($mails[1])){
                if($mails[1] != '30'){
                    if($mails[1] > 31){
                        $this->verblog("YahooNewMail() You've got ".$this->hex2ascii($mails[1])." new mails.");
                    }else{
                        $this->verblog("YahooNewMail() You've got ".$this->hex2ascii($mails[1])." new mail.");
                    }
                }else{
                    $this->verblog("YahooNewMail() You've got no new mail.");
                }
            }else{
                $this->verblog("YahooNewMail() Error getting mail number.");
                return FALSE;
            }
        }
        return TRUE;
    }
 
    private function YahooSkinname(){
        $packet = "211".$this->delim."Skin:Name=Purple\nSettings:StartLaunchcast=0\tShowInsider=0\tAutologin=1\tArchiving=2\tHiddenLogin=1\nEnv:OS=7\nPlugins:ContainerWindowState=0\n";
        return $this->sendsock($this->pachet("\x15", $packet));
    }
 
    private function YahooPing($packet){
        if(empty($packet[1])){
            $this->verblog("YahooPing() No packet given.", 'red');
            return FALSE;
        }
        if($packet[0] != "\x12"){
            $this->verblog("YahooPing() You've given me a wrong packet type to parse, I need 0x12, got 0x".$this->ascii2hex($packet[0]), 'red');
            return FALSE;
        }        
        $this->verblog("YahooPing() (0x12) parsing incomming ping request.", 'green');
        $packet = $this->ascii2hex($packet[1]);
        preg_match('|313433c080([a-f0-9]+?)c080313434c080([a-f0-9]+?)c080|is', $packet, $pings);
        if(isset($pings[1]) && isset($pings[2]) && is_numeric($this->hex2ascii($pings[1])) && is_numeric($this->hex2ascii($pings[2]))){
            $this->pinginterval = $this->hex2ascii($pings[1]);
            $this->pingnr = $this->hex2ascii($pings[2]);
            $this->verblog("YahooPing() Yahoo wants ".$this->pingnr." ping every ".$this->pinginterval." seconds.");
        }else{
            $this->verblog("YahooPing() Error getting ping numbers.");
            return FALSE;
        }
        return TRUE;
    }
 
    private function YahooPersonLogOff($packet){
        if(empty($packet[1])){
            $this->verblog("YahooPersonLogOff() No packet given.", 'red');
            return FALSE;
        }
        if($packet[0] != "\x02"){
            $this->verblog("YahooPersonLogOff() You've given me a wrong packet type to parse, I need 0x02, got 0x".$this->ascii2hex($packet[0]), 'red');
            return FALSE;
        }        
        $this->verblog("YahooPersonLogOff() (0x02) parsing incomming log off persson.", 'green');
        $packet = $this->ascii2hex($packet[1]);
        if(preg_match('|37c080([a-f0-9]+?)c080|', $packet, $out)){
            $this->verblog('YahooPersonLogOff() '.$this->hex2ascii($out[1]).' logged off.', 'green');
        }else{
            $this->verblog("YahooPersonLogOff() Regex failed, here's the packet: ".$packet, 'red');
            return FALSE;
        }
        return TRUE;
    }
 
    public function YahooAddABuddyProcess($packet){
        if($this->connected == 0){
            $this->verblog("YahooAddABuddyProcess() Can't do this if not connected.", 'red');
            return FALSE;
        }
        if($packet[0] != "\x83"){
            $this->verblog("YahooAddABuddyProcess() You've given me a wrong packet type to parse, I need 0x83, got 0x".$this->ascii2hex($packet[0]), 'red');
            return FALSE;
        }
        $packet = $this->ascii2hex($packet[1]);
        if(preg_match('|31c080'.$this->ascii2hex($this->user).'c08037c080([a-f0-9]+?)c0803635c080([a-f0-9]+?)c0803636c080([a-f0-9]+?)c080|', $packet, $out)){
            switch($out[3]){
                case "30":
                    $this->buddylist[$this->hex2ascii($out[2])][$this->hex2ascii($out[1])]['status'] = "Didn't accept your add request yet.";
                    $this->verblog("YahooAddABuddyProcess() Everything's ok, awayting buddy deny/accept.", 'green');
                    return TRUE;
                    break;
                case "32":
                    $this->verblog("YahooAddABuddyProcess() ".$this->hex2ascii($out[1])." is already in the buddy list.", 'red');
                    return FALSE;
                    break;
                case "33":
                    $this->verblog("YahooAddABuddyProcess() ".$this->hex2ascii($out[1])." doesn't exist.", 'red');
                    return FALSE;
                    break;                    
            }
        }else{
            $this->verblog("YahooAddABuddyProcess() Regex failed, here's the packet: ".$packet, 'red');
            return FALSE;
        }
        return TRUE;
    }
 
    private function YahooDisconnect(){
        $this->verblog("YahooDisconnect() Got disconnection packet.");
        $this->SockClose();
        $this->connected = 0;
    }
    private function ParseAvatarLink($packet){
        if($packet[0] != "\xBE"){
            $this->verblog("ParseAvatarLink() You've given me the wrong packet type to parse, I need 0xBE, got 0x".$this->ascii2hex($packet[0]), 'red');
            return FALSE;
        }
        $avatarlink = explode("c080", $this->ascii2hex($packet[1]));
        $yahooid = $this->hex2ascii($avatarlink[1]);
        $avatarlink = $this->hex2ascii($avatarlink[7]);
        $this->verblog("ParseAvatarLink() ".$yahooid."'s avatar link is: ".$avatarlink, 'green');
        return($avatarlink);
    }
 
    private function parseaccess($packet){
        $_messages[] = array();
        switch($packet[0]){
            case "\x02":
                // Person logs off (or goes invisible)
                $this->YahooPersonLogOff($packet);
                break;
            case "\x06":
                // Incomming messages
                $_messages = $this->YahooIncommingMessage($packet);
                break;
            case "\x0B":
                // Mail notifications
                $this->YahooNewMail($packet);
                break;
            case "\x12":
                // Ping
                $this->YahooPing($packet);
                break;
            case "\x4B":
                // Typing notification ( or other kind )
                break;
            case "\x83":
                // Buddy request response
                $this->YahooAddABuddyProcess($packet);
                break;
            case "\xBD":
                // Avatar checksum
                break;
            case "\xC6":
                // Person status update
                break;
            case "\xD1":
                // Disconnect 
                $this->YahooDisconnect($packet);
                break;
            case "\xD6":
                // Add request
                $this->YahooAddABuddyRequest($packet);
                break;
            case "\xF0":
                // Status updates
            case "\xF1":
                // Buddy List 
                $this->YahooBuddyList($packet);
                break;
            case "\xBE":
                // Avatar link
                $this->ParseAvatarLink($packet);
                break;
            default:
                // Discard the rest
                $this->verblog("parseaccess() Don't know what to do with 0x".$this->ascii2hex($packet[0])." packet.");
                break;
        }        
        return $_messages;
    }
 
    public function parsepackets(){
        $_messages = array();
        $return = $this->recvsock();
        if(is_array($return[1])){
            foreach($return as $packet){
                $_messages[] = $this->parseaccess($packet);
            }
        }else if(!empty($return)){
            $_messages[] = $this->parseaccess($return);
        }
        return $_messages;
    }
 
    public function YahooConnect(){
        if($this->connected == 1){
            $this->verblog("YahooConnect() Can't reconned since I'm already connected.");
            return FALSE;
        }
        if(!$this->SockINIT()){
            $this->verblog("YahooConnect()::SockINIT() Can't create socket.");
            return FALSE;
        }
        usleep(100);
        if(!$this->YahooVRFY()){
            $this->verblog("YahooConnect()::YahooVRFY() failed.");
            $this->YahooDisconnect();
            return FALSE;
        }
        usleep(100);
        if(!$this->YahooAUTH()){
            $this->verblog("YahooConnect()::YahooAUTH() failed.");
            $this->YahooDisconnect();
            return FALSE;
        }
        usleep(100);
        if(!$this->gen307()){
            $this->verblog("YahooConnect()::genyt307() failed.");
            $this->YahooDisconnect();
            return FALSE;
        }
        usleep(100);
        if(!$this->YahooLogin()){
            $this->verblog("YahooConnect()::YahooLogin() failed.");
            $this->YahooDisconnect();
            return FALSE;
        }
        usleep(100);
        if(!$this->YahooSkinname()){
            $this->verblog("YahooConnect()::YahooSkinname() failed.");
            $this->YahooDisconnect();
            return FALSE;
        }
        $_messages = $this->parsepackets();
        $this->verblog("YahooConnect() Finished login procedure, feel free to do your thing now.");
        $this->connected = 1;
        $this->YahooKeepAlive();
        
        return $_messages;
    }
}

?>