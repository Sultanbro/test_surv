<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessToken;

class OauthClientToken extends Model
{
    protected $table = 'oauth_client_tokens';

    public $timestamps = true;

    const AMOCRM = 'amocrm';
    
    // const AMOCRM_CLIENT_ID = '4214e582-78d3-4880-8aba-4d0ae8c6c1e4';
    // const AMOCRM_CLIENT_SECRET = 'nhmq7q3JtZs68YdZmeon7E9Dx52lJKriTLRiwUxdEldZXA3rDzD48FyvKpSZ04rf';
    const AMOCRM_REDIRECT_URI = 'https://jobtron.org/api/apix';
    const AMOCRM_GRANT_TYPES = ['authorization_code', 'refresh_token'];

    const AMOCRM_CLIENT_ID = '69b6bca1-2ac1-4b1b-b06a-6de341588cb5';
    const AMOCRM_CLIENT_SECRET = 'QOsnV7FpCASwYYR4z7skMc3AJAwnTaxljMs9T1dkR3WdcaZnav3bg09ufEjI8ZPM';

    

    protected $fillable = [
        'id',
        'user_id', // User()
        'auth_code',
        'access_token',
        'refresh_token',
        'server', // amocrm
        'grant_type', // authorization_code
        'scope', // public
        'domain', // test456.amocrm.com
        'expires_at', // timestamp
    ];
    

    public static function get_token(int $user_id, String $server)
    {
        return self::where([
            'user_id' => $user_id,
            'server' => $server,
            ])->first();
    }
        
    public static function saveToken(AccessToken $accessToken, String $domain) {
        
        $client = self::where('domain', $domain)->first();

        if($client) {
            $client->update([
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_at' => Carbon::createFromTimeStamp($accessToken->getExpires()),
                'user_id' => 5,
                'auth_code' => 'x',
                'grant_type' => 'authorization_code',
                'scope' => 'bearer'
            ]);
        } else {
            self::create([
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_at' => Carbon::createFromTimeStamp($accessToken->getExpires()),
                'user_id' => 5,
                'auth_code' => 'x',
                'grant_type' => 'authorization_code',
                'scope' => 'bearer',
                'domain' => $domain,
            ]);
        }
        
    }


    // public static function amocrm_refresh_token(int $user_id) {

    //     $token_info = self::get_token($user_id, self::AMOCRM);
    //     if(!$token_info) return null;

    //     $link = $token_info->domain . '/oauth2/access_token'; //Формируем URL для запроса

    //     $data = [
    //         'client_id' => self::AMOCRM_CLIENT_ID,
    //         'client_secret' => self::AMOCRM_CLIENT_SECRET,
    //         'grant_type' => 'refresh_token',
    //         'code' => $token_info->refresh_token,
    //         'redirect_uri' => self::AMOCRM_REDIRECT_URI,
    //     ];

    //     $response = self::amocrm_curl($link, 'POST', $data);
        
    //     $token_info->access_token = $response['access_token']; //Access токен
    //     $token_info->refresh_token = $response['refresh_token']; //Refresh токен
    //     $token_info->scope = $response['token_type']; //Тип токена
    //     $token_info->expires_at = Carbon::createFromTimeStamp(time() + (int)$response['expires_in']); //Через сколько действие токена истекает
    //     $token_info->save();    
        
    //     return $token_info;
    // }

    // public static function amocrm_access_token(int $user_id, String $auth_code) {

    //     $token_info = self::get_token($user_id, self::AMOCRM);
    //     if(!$token_info) return null;

    //     $link = $token_info->domain . '/oauth2/access_token'; //Формируем URL для запроса

    //     $data = [
    //         'client_id' => self::AMOCRM_CLIENT_ID,
    //         'client_secret' => self::AMOCRM_CLIENT_SECRET,
    //         'grant_type' => 'authorization_code',
    //         'code' => $auth_code,
    //         'redirect_uri' => self::AMOCRM_REDIRECT_URI,
    //     ];

    //     $response = self::amocrm_curl($link, 'POST', $data);

    //     $token_info->access_token = $response['access_token']; //Access токен
    //     $token_info->refresh_token = $response['refresh_token']; //Refresh токен
    //     $token_info->scope = $response['token_type']; //Тип токена
    //     $token_info->expires_at = Carbon::createFromTimeStamp(time() + (int)$response['expires_in']); //Через сколько действие токена истекает
    //     $token_info->save();

    //     return $response;
    // }

    // private static function amocrm_curl(String $link, String $type = 'POST', array $data = [])
    // {
    //     $curl = curl_init();
        
    //     curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    //     curl_setopt($curl,CURLOPT_URL, $link);
    //     curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
    //     curl_setopt($curl,CURLOPT_HEADER, false);
    //     curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $type);
    //     curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
    //     curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    //     curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    //     $out = curl_exec($curl); 
    //     //$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
    //     curl_close($curl);
    //     return json_decode($out, true); 
    // }
}
