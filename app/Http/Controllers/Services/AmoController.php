<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Exceptions\AmoCRMApiException;
use App\OauthClientToken as Oauth;
use App\Http\Controllers\Controller;

class AmoController extends Controller
{

    public function get_token()
    {
        
        $domain = 'abik50000.amocrm.com';
        $current = Oauth::where('domain', $domain)->first();

        $apiClient = new AmoCRMApiClient(Oauth::AMOCRM_CLIENT_ID, Oauth::AMOCRM_CLIENT_SECRET, Oauth::AMOCRM_REDIRECT_URI);
        
  

        $accessToken = new AccessToken([
            'access_token' => $current->access_token,
            'refresh_token' => $current->refresh_token,
            'expires' => strtotime($current->expires_at),
        ]);
        
        $apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain($domain)
                ->onAccessTokenRefresh(
                    function (AccessTokenInterface $accessToken, string $domain) {
                        saveToken(
                            [
                                'accessToken' => $accessToken->getToken(),
                                'refreshToken' => $accessToken->getRefreshToken(),
                                'expires' => $accessToken->getExpires(),
                                'baseDomain' => $domain,
                            ]
                        );
                        Oauth::saveToken($accessToken, $domain);
                    });

        $leads = $apiClient->leads()->get();
                    
        $lead = $leads->first();
        //dump($lead->toArray());
        //dump($apiClient->companies()->get()->first()->toArray());

        dd($apiClient->getRequest()->get('/api/v4/leads/3183651', [
            'with' => '_embedded[contacts][0]'
        ]));
        
    }

    public function first_enter() {
        
        //if (isset($_GET['referer'])) $apiClient->setAccountBaseDomain($_GET['referer']);

        $domain = 'abik50000.amocrm.com';
        $auth_code = 'def502007b0bd909b9847a3f2725efa98ac3224da6e481e392edfb4e6b02abd8f74ec91a08d6dad4f0e9eddb484e0eb2a50ef7b5a9481a407b47d2aa8dcd9e3b15ff139dfe15ecee169fcf78e74193eac8a4b44f92a2eee2daf002f8711e844ad49eb08ad4500a8358991f38fc52a94cb93cb4cfd76cd2e44755e7805b91c5298af8aaaeed3bf90781aa9b5f77fbf3c502263d6e33861c67672f0d9c3df912b12be2615581ec8e6e210f439b98f4a2effe47bf902b2238c3158f78893dc9a9e2dfe1ca730060543bb02039d89816d24b94f5057a4d52de5ebbce498dfe46c4b315c2f61a66561a697802e8e5f879ca63e10aa45d7ee21b13cfcb7c58aae2a3c040f7a7c69eeab2dcc3467fef3dc199516ea02626d8fcd2abdbfcfc3ae045775e6c8eedb223057790ad6d10b5d886b7f789928a4205a19c907e66cb76a3ac37655f0523afa75089d3a87362948532ef2e65a451a2bd17103697525a74a147242c3d48c6801f7db31a1368fbccdc431f6d99db779cbbb2d4508f2e02942b496efdfdfb212e704465937a25c6c209508886db316ab194cb8e5224ef969d05f0bbf4823f42e9e10fcf2390734e45579b95e7e7b3f1e08926ada268c0e9c413afdd2bc1a02a7afac11a4fd91f6f966db1a3e06327';  
        
        $apiClient = new AmoCRMApiClient(Oauth::AMOCRM_CLIENT_ID, Oauth::AMOCRM_CLIENT_SECRET, Oauth::AMOCRM_REDIRECT_URI);
       
        $accessToken = $apiClient->setAccountBaseDomain($domain)->getOAuthClient()->getAccessTokenByCode($auth_code);
            
        Oauth::saveToken($accessToken, $domain);

    }

    public function amocrm_get_token() // переименовать как только передадим в модерацию в get_token()
    {
        $apiClient = new AmoCRMApiClient(Oauth::AMOCRM_CLIENT_ID, Oauth::AMOCRM_CLIENT_SECRET, Oauth::AMOCRM_REDIRECT_URI);

        define('TOKEN_FILE', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json');

        Oauth::create([
            'user_id' => 0, 
            'auth_code' => 0,
            // 'access_token',
            // 'refresh_token',
            // 'server', // amocrm
            // 'grant_type', // authorization_code
            // 'scope', // public
            // 'domain', // https://test456.amocrm.com
            // 'expires_at', // timestamp
        ]);

        if (isset($_GET['referer'])) $apiClient->setAccountBaseDomain($_GET['referer']);

        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;
            if (isset($_GET['button'])) {
                echo $apiClient->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );
                die;
            } else {
                $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
                die;
            }
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }

        /**
         * Ловим обратный код
         */
        try {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $apiClient->getAccountBaseDomain(),
                ]);
            }

        } catch (Exception $e) {
            die((string)$e);
        }

        $ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

        printf('Hello, %s!', $ownerDetails->getName());
    }
    
    public function api(Request $request)
    {
        return $request->all(); 
    }
    
    private function getToken()
    {
        if (!file_exists(TOKEN_FILE)) {
            exit('Access token file not found');
        }
    
        $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);
    
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    private function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];
    
            file_put_contents(TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    private function printError(AmoCRMApiException $e): void
    {
        $errorTitle = $e->getTitle();
        $code = $e->getCode();
        $debugInfo = var_export($e->getLastRequestInfo(), true);
    
        $error = <<<EOF
            Error: $errorTitle
            Code: $code
            Debug: $debugInfo
            EOF;
    
        echo '<pre>' . $error . '</pre>';
    }
}
