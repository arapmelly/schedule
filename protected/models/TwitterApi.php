<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/27/11
 * Time: 10:16 PM
 * To change this template use File | Settings | File Templates.
 */
Yii::import('ext.OAuth', true);


class TwitterApi
{
    const API_URL = 'https://api.twitter.com/1/';
    const URL_REQ_TOK = 'https://api.twitter.com/oauth/request_token';
    const URL_AUTH_TOK = 'https://api.twitter.com/oauth/authorize';
    const URL_ACCESS_TOK = 'https://api.twitter.com/oauth/access_token';
    const DEFAULT_FORMAT = '.json';

    private $_application;
    private $_account;

    private $_consumer;
    private $_accessToken;
    private $_signMethod;
    private $_format = '.json';

    private static $_lastGetResponse;
    private static $_lastGetError;

    private $_lastError;
    private $_lastCode = 'empty';
    private $_lastResponse;

    private $_defaults = array(
        'statuses/update' => array(
            'wrap_links' => false,
            'include_entities' => false,
        ),
        'friendships/create' => array(
            'follow' => true
        ),
        'statuses/user_timeline' => array(
            'trim_user' => true,
            'include_entities' => true,
        ),
        'statuses/retweet' => array(

        )
    );

    public function getLastError()
    {
        return $this->_lastError;
    }

    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    public function getLastCode()
    {
        return $this->_lastCode;
    }

    public function getApplication()
    {
        return $this->_application;
    }

    public function __construct(TwitterApplication $application, TwitterAccount $account)
    {
        $this->_application = $application;
        $this->_account = $account;
        $this->_consumer = new OAuthConsumer($this->_application->consumer_key, $this->_application->consumer_secret);
        $this->_accessToken = new OAuthToken($this->_account->oauth_token, $this->_account->oauth_token_secret);
        $this->_signMethod = new OAuthSignatureMethod_HMAC_SHA1();
    }

    public static function queryRequestToken($consumerKey, $consumerSecret)
    {
        $consumer = new OAuthConsumer($consumerKey, $consumerSecret);

        $request = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", self::URL_REQ_TOK);
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);

        $requestToken = OAuthUtil::parse_parameters(file_get_contents($request->to_url()));
        if (isset($requestToken['oauth_token']))
            return $requestToken;

        return false;
    }

    public static function queryAccessToken($consumer_key, $consumer_secret, $requestToken)
    {
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
        $requestTokenClass = new OAuthToken($requestToken['oauth_token'], $requestToken['oauth_token_secret']);
        $authorizedRequestToken = OAuthRequest::from_consumer_and_token(
            $consumer,
            $requestTokenClass,
            "GET",
            self::URL_ACCESS_TOK,
            array());
        $authorizedRequestToken->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $requestTokenClass);
        $response = @file_get_contents($authorizedRequestToken->to_url());
        $accessToken = OAuthUtil::parse_parameters($response);

        if (isset($accessToken['oauth_token']) && $accessToken['user_id'])
            return $accessToken;

        return false;
    }

    public static function createAuthUrl($requestToken, $callbackUrl)
    {
        return self::URL_AUTH_TOK . "?" . "&oauth_token={$requestToken['oauth_token']}&oauth_callback=" . urlencode($callbackUrl);
    }

    public function statusesUpdate($tweetText)
    {
        return $this->post('statuses/update', array('status' => $tweetText));
    }

    public function statusesRetweet($messageId)
    {
        return $this->post('statuses/retweet' . '/' . $messageId, array());
    }

    public function friendshipsCreate($accountId)
    {
        if (empty($accountId)) return false;
        return $this->post('friendships/create', array('user_id' => $accountId));
    }

    public function statusesUserTimeline($screenName, $sinceId = null, $includeRts = false)
    {
        $params = array('screen_name' => $screenName, 'include_rts' => $includeRts);
        if (!empty($sinceId))
            $params['since_id'] = $sinceId;
        return $this->get('statuses/user_timeline', $params);
    }

    public static function friendshipsShow($source_id, $target_id)
    {
        if (!isset($source_id, $target_id)) return false;
        return self::staticGet('friendships/show', array('source_id' => $source_id, 'target_id' => $target_id));
    }

    public static function friendshipsExists($source_id, $target_id)
    {
        if (!isset($source_id, $target_id)) return false;
        return self::staticGet('friendships/exists', array('user_id_a' => $source_id, 'user_id_b' => $target_id));
    }


    public function post($method, $params, $proxy = null)
    {
        $defaults = isset($this->_defaults[$method]) ? $this->_defaults[$method] : array();
        $req = OAuthRequest::from_consumer_and_token(
            $this->_consumer,
            $this->_accessToken,
            'POST',
            self::API_URL . $method . $this->_format,
            array_merge($defaults, $params)
        );
        $req->sign_request($this->_signMethod, $this->_consumer, $this->_accessToken);

        $h = curl_init();
        curl_setopt($h, CURLOPT_URL, $req->get_normalized_http_url());
        curl_setopt($h, CURLOPT_POST, true);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_POSTFIELDS, $req->to_postdata());
        curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 30);
        if (!is_null($proxy))
            curl_setopt($h, CURLOPT_PROXY, "$proxy");
        $this->_lastResponse = json_decode(curl_exec($h), true);
        $this->_lastCode = curl_getinfo($h, CURLINFO_HTTP_CODE);
        if ($this->_lastCode != 200) {
            $this->_lastError = $this->_lastResponse;
            return false;
        }
        return true;
    }

    public function get($method, $params, $proxy = null)
    {
        if (!key_exists($method, $this->_defaults))
            throw new Exception('Method not exists');
        $req = OAuthRequest::from_consumer_and_token(
            $this->_consumer,
            $this->_accessToken,
            'GET',
            self::API_URL . $method . $this->_format,
            array_merge($this->_defaults[$method], $params)
        );
        $req->sign_request($this->_signMethod, $this->_consumer, $this->_accessToken);

        $h = curl_init();
        curl_setopt($h, CURLOPT_URL, $req->to_url());
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 30);
        if (!is_null($proxy))
            curl_setopt($h, CURLOPT_PROXY, "$proxy");
        $this->_lastResponse = json_decode(curl_exec($h), true);
        $this->_lastCode = curl_getinfo($h, CURLINFO_HTTP_CODE);
        if ($this->_lastCode != 200) {
            $this->_lastError = $this->_lastResponse;
            return false;
        }
        return true;
    }

    public static function staticGet($method, $params, $proxy = null)
    {
        $url = self::API_URL . $method . self::DEFAULT_FORMAT . '?' . http_build_query($params);
        $h = curl_init();
        curl_setopt($h, CURLOPT_URL, $url);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 30);
        if (!is_null($proxy))
            curl_setopt($h, CURLOPT_PROXY, "$proxy");

        $response = curl_exec($h);
        $arr = json_decode($response, true);
        //        var_dump(curl_getinfo($h, CURLINFO_HTTP_CODE));

        return $arr;
    }

    function httpProxy($url, $proxy)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
        curl_setopt($ch, CURLOPT_PROXY, "$proxy");
        $ss = curl_exec($ch);
        curl_close($ch);
        return $ss;
    }


    private static function _log($method, $response)
    {

    }
}