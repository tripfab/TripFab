<?php

class WS_Googlemap {
    
    protected $apikey = "ABQIAAAARetlkF30MSbUj-VJMYvjgBRJTpYW4dXIqbdJoYXVKZNUrqWSrBTOH7TYNPd_R7DqgYhynsR700-kEw";
    protected $user   = "cristian@tripfab.com";
    protected $passw  = "caaj0789titi";
    
    protected $js     = "http://maps.google.com/maps?file=api&amp;v=2&amp;key=%s&amp;sensor=false";
    
    public function __construct() {
        
    }
    
    public function getScript()
    {
        return sprintf($this->js, $this->apikey);
    }
    
    public function findAddress($q)
    {
        $client = new Zend_Http_Client();
        $client->setUri('http://maps.google.com/maps/geo');
        $client->setParameterGet('q', urlencode($q));
        $client->setParameterGet('output', 'json');
        $client->setParameterGet('sensor', 'false');
        $client->setParameterGet('key',$this->apikey);
        
        $result = $client->request();
        
        $response = Zend_Json_Decoder::decode($result->getBody(), Zend_Json::TYPE_OBJECT);
        
        return $response;
        
    }
    
    public function findLatLng($q)
    {        
        $response = $this->findAddress($q);
        
        $lat = $response->Placemark[0]->Point->coordinates[0];
        $lng = $response->Placemark[0]->Point->coordinates[1];
        
        return array($lat, $lng);
    }
}