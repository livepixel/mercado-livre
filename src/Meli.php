<?php namespace Livepixel\MercadoLivre;

class Meli {

    /**
     * Configuration for urls
     */
    protected $urls = array(
        'API_ROOT_URL' => 'https://api.mercadolibre.com', 
        'AUTH_URL'     => 'http://auth.mercadolivre.com.br/authorization', 
        'OAUTH_URL'    => '/oauth/token'
    );

    /**
     * Configuration for CURL
     */
    protected $curl_opts = array(
        CURLOPT_USERAGENT => "MELI-PHP-SDK-1.0.0",
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 60
    );

    protected $client_id;
    protected $client_secret;

    /**
     * Constructor method. Set all variables to connect in Meli
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_token
     */
    public function __construct($client_id, $client_secret, $urls = null, $curl_opts = null) {
        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
        $this->urls          = $urls ? $urls : $this->urls;
        $this->curl_opts     = $curl_opts ? $curl_opts : $this->curl_opts;
    }

    /**
     * Return an string with a complete Meli login url.
     *
     * @param string $redirect_uri
     * @return string
     */
    public function getAuthUrl($redirect_uri) {
        $params = array("client_id" => $this->client_id, "response_type" => "code", "redirect_uri" => $redirect_uri);
        $auth_uri = $this->urls['AUTH_URL']."?".http_build_query($params);
        return $auth_uri;
    }

    /**
     * Executes a POST Request to authorize the application and take
     * an AccessToken.
     *
     * @param string $code
     * @param string $redirect_uri
     *
     */
    public function authorize($code, $redirect_uri) {

        

        $body = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "code" => $code,
            "redirect_uri" => $redirect_uri
        );

        $opts = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body
        );

        return $this->execute($this->urls['OAUTH_URL'], $opts);

    }
    /**
     * Execute a POST Request to create a new AccessToken from a existent refresh_token
     *
     * @param string $refresh_token
     *
     * @return string|mixed
     */
    public function refreshAccessToken($refresh_token = null) {
        if($refresh_token) {

            $body = array(
                "grant_type" => "refresh_token",
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "refresh_token" => $refresh_token
            );

            $opts = array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body
            );

            return $this->execute($this->urls['OAUTH_URL'], $opts);

        } else {
            $result = array(
                'error' => 'Offline-Access is not allowed.',
                'httpCode'  => null
            );
            return $result;
        }
    }

    /**
     * Execute a GET Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function get($path, $params = null) {
        $exec = $this->execute($path, null, $params);
        return $exec;
    }

    /**
     * Execute a POST Request
     *
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body
        );

        $exec = $this->execute($path, $opts, $params);
        return $exec;
    }

    /**
     * Execute a PUT Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        );

        $exec = $this->execute($path, $opts, $params);
        return $exec;
    }

    /**
     * Execute a DELETE Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "DELETE"
        );

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a OPTION Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function options($path, $params = null) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        );

        $exec = $this->execute($path, $opts, $params);
        return $exec;
    }

    /**
     * Execute all requests and returns the json body and headers
     *
     * @param string $path
     * @param array $opts
     * @param array $params
     * @return mixed
     */
    public function execute($path, $opts = array(), $params = array()) {
        $uri = $this->make_path($path, $params);
        $ch = curl_init($uri);
        curl_setopt_array($ch, $this->curl_opts);
        if(!empty($opts)){
            curl_setopt_array($ch, $opts);
        }
        $return["body"] = json_decode(curl_exec($ch));
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $return;
    }

    /**
     * Check and construct an real URL to make request
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array()) {
        if (!preg_match("/^http/", $path)) {
            if (!preg_match("/^\//", $path)) {
                $path = '/'.$path;
            }
            $uri = $this->urls['API_ROOT_URL'].$path;
        } else {
            $uri = $path;
        }
        if(!empty($params)) {
            $paramsJoined = array();
            foreach($params as $param => $value) {
               $paramsJoined[] = "$param=$value";
            }
            $params = '?'.implode('&', $paramsJoined);
            $uri = $uri.$params;
        }
        return $uri;
    }
}
