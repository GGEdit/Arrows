<?php

namespace App\Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;

class HttpRequest{

    private $ch;

    public function get($url, $header = NULL){
        $this->ch = curl_init();
        if($header != NULL){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($this->ch);
        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        curl_close($this->ch);

        return ['status' => $statusCode, 'response' => $response];
    }

    public function post($url, $data){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($this->ch);
        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        curl_close($this->ch);

        return ['status' => $statusCode, 'response' => $response];
    }

    public function post_json($url, $data, $header = NULL){
        $this->ch = curl_init();
        if($header == NULL){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
        }
        else{
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($this->ch);
        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        curl_close($this->ch);

        return ['status' => $statusCode, 'response' => $response];
    }

    public function get_request($url){
        $client = new Client();
        $response = $client->request('GET', $url);
        if($response->getStatusCode() == 200){
            return $response->getBody();
        }
        return NULL;
    }

    public function get_async($urls){
        $client = new Client();
        $contents = [];
        $requests = function($urls) use($client){
            foreach($urls as $url){
                yield function() use($client, $url){
                    return $client->requestAsync('GET', $url);
                };
            }
        };
        $pool = new Pool($client, $requests($urls), [
            'concurrency' => 20,
            'fulfilled' => function ($response, $index) use ($urls, &$contents) {
                $contents[$urls[$index]] = [
                    'html' => $response->getBody()->getContents(),
                    'status_code' => $response->getStatusCode(),
                    'response_header' => $response->getHeaders()
                ];
            },
            'rejected' => function ($reason, $index) use ($urls, &$contents) {
                $contents[$urls[$index]] = [
                    'html' => '',
                    'reason' => $reason
                ];
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();

        return $contents;
    }
}

?>