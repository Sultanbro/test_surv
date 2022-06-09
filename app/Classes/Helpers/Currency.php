<?php

namespace App\Classes\Helpers;
use GuzzleHttp\Client as Guzzle;
use App\Setting;

class Currency 
{
    const API_KEY = '9e3036e89c9116ba35ec255aaecb692d';

    /**
     * Refresh currency rates
     */
    public static function update() {

        $client = new Guzzle([
			'base_uri' => 'http://api.currencylayer.com',
			'timeout'  => 10.0,
		]); 

		$url = '/live?access_key=' . self::API_KEY . '&source=USD&format=1';
		$response = $client->request('GET', $url);

        $rates = [];

        if($response->getStatusCode() == '200') {
            $arr = json_decode($response->getBody(), true);
            
            if($arr['success']) {
                $quotes = $arr['quotes'];
                $rates = [
                    'KZT' => 1,
                    'USD' => 1 / $quotes['USDKZT'],
                    'KGS' => $quotes['USDKGS'] / $quotes['USDKZT'],
                    'UZS' => $quotes['USDUZS'] / $quotes['USDKZT'],
                    'RUB' => $quotes['USDRUB'] / $quotes['USDKZT'],
                    'BYN' => $quotes['USDBYN'] / $quotes['USDKZT'],
                    'UAH' => $quotes['USDUAH'] / $quotes['USDKZT'],
                ];

                foreach($rates as $key => $rate) {
                    $s = Setting::where('name', 'currency_'. strtolower($key))->first();

                    if($s) {
                        $s->value = $rate;
                        $s->save();
                    }
                }
                
            } else {
                // logs
            }
        }  else {   
            // logs
        }
		
    }

    public static function rates() {
        $settings = Setting::where('name', 'like', 'currency_%')->get();

        $rates = [];
        foreach($settings as $s) {
            $rates[substr($s->name, 9, 3)] = $s->value;
        }

        return $rates;
    }
}
