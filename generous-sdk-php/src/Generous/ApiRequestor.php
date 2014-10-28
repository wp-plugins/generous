<?php

class Generous_ApiRequestor
{
    public $apiKey;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function request($method, $endpoints, $params = null)
    {
        return $this->requestRaw(strtolower($method), $endpoints, $params);
    }

    private function requestRaw($method, $endpoints, $params)
    {
        $user_agent = array(
            'sdk_version' => Generous::VERSION,
            'lang' => 'php',
            'lang_version' => phpversion(),
            'publisher' => 'generous',
            'uname' => php_uname()
        );

        $headers = array(
            'X-Generous-Client-User-Agent: ' . json_encode($user_agent),
            'User-Agent: Generous/' . Generous::$apiBaseUrlVersion . ' SDK-PHP/' . Generous::VERSION
        );

        if (isset(Generous::$apiVersion) && Generous::$apiVersion != null) {
            $headers[] = 'Generous-Version: ' . Generous::$apiVersion;
        }

        if(isset(Generous::$apiKey) && isset(Generous::$apiSecret)) {
            $auth_token = base64_encode(Generous::$apiKey . ':' . Generous::$apiSecret);
            $headers[] = 'Authorization: Basic ' . $auth_token;
        }

        return $this->curlRequest($method, $endpoints, $params, $headers);
    }

    private function curlRequest($method, $endpoints, $params, $headers)
    {
        $curl   = curl_init();
        $opts   = array();
        $absURL = $this->apiUrl($endpoints);

        switch ($method) {
            case 'get':
                if (count($params) > 0) {
                    $encoded = self::encode($params);
                    $absURL = "$absURL?$encoded";
                }
            break;

            case 'post':
                $opts[CURLOPT_POST] = true;
                $opts[CURLOPT_POSTFIELDS] = self::encode($params);
            break;

            case 'put':
                $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $opts[CURLOPT_POSTFIELDS] = self::encode($params);
            break;

            case 'delete':
                $opts[CURLOPT_CUSTOMREQUEST] = 'delete';
                $opts[CURLOPT_POSTFIELDS] = http_build_query($params);
            break;
        }

        $opts[CURLOPT_URL] = $absURL;
        $opts[CURLOPT_CONNECTTIMEOUT] = 30;
        $opts[CURLOPT_TIMEOUT] = 80;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_HTTPHEADER] = $headers;
        $opts[CURLOPT_SSL_VERIFYPEER] = 0;

        curl_setopt_array($curl, $opts);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    private static function apiUrl($endpoints = '')
    {
        $apiBaseUrl = Generous::getBaseUrl();

        return $apiBaseUrl . $endpoints;
    }

    public static function encode($arr, $prefix = null)
    {
        if (!is_array($arr)) return $arr;
        $r = array();

        foreach ($arr as $k => $v) {
            if (is_null($v)) continue;

            if ($prefix && $k && !is_int($k)) {
                $k = $prefix."[".$k."]";
            } else if ($prefix) {
                $k = $prefix."[]";
            }

            if (is_array($v)) {
                $r[] = self::encode($v, $k, true);
            } else {
                $r[] = urlencode($k)."=".urlencode($v);
            }
        }

        return implode("&", $r);
    }
}