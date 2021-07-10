<?php

use Exception;
use Psr\Container\ContainerInterface;

class Razorpay
{
    protected $ci;
    
    //Constructor
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->base_url ='https://api.razorpay.com/v1/virtual_accounts/';

    }

    // create virtual account
    public function VirtualAccount($post_data, $gateway_params){
        return $this->thisguzzle('POST',$gateway_params,$this->base_url,$post_data);
    }
    
    //fetch accout by id and payment
    public function FetchAccountByID($gateway_params,$virtualid,$payments = false){
        if($payments){
            $url = $this->base_url.$virtualid.'/payments';
        }else{
            $url = $this->base_url.$virtualid;
        }
        return $this->thisguzzle('GET',$gateway_params,$url);
    }
    
    // Fetch all accounts
    public function FetchAllAccount($gateway_params){
        $url = $this->base_url.'?count=100';
        return $this->thisguzzle('GET',$gateway_params,$url);
    }

    // close account by ID
    public function CloseAccount($gateway_params,$virtualid){
        $url = $this->base_url.$virtualid.'/close';
        return $this->thisguzzle('POST',$gateway_params,$url);

    }

    public function thisguzzle($method,$gateway_params,$url,$post_data = ''){
        $client_guzzle = new \GuzzleHttp\Client();
        try{
            if($method === 'GET'){
                $result = $client_guzzle->get($url,['auth'=>[$gateway_params['key_id'] , $gateway_params['key_secret']]]);
            }else{
                $result = $client_guzzle->post($url,['headers'=>['Content-type' => 'application/json'],'json'=>$post_data,'auth'=>[$gateway_params['key_id'] , $gateway_params['key_secret']]]);
            }
            return json_decode($result->getBody(),true);
        } catch(Exception $e){
            $this->ci->get('logger')->addDebug("Exception Occur Guzzle Execution",[$e]);
            return False;
        }
    }

}
