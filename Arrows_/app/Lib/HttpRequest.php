<?php

namespace App\Lib;

class HttpRequest{

    private $ch;

    public function __construct(){
        $this->ch = curl_init();
    }

    public function post($url, $data){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        if($statusCode != 200){
            return "";
        }
        return $result;
    }
}
?>