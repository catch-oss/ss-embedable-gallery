<?php

use Firebase\JWT\JWT;

/**
 * @Todo - off load some of this to the JWT Helper
 */
abstract class JWTAPIClient extends APIClient {

    /**
     * [$inst description]
     * @var [type]
     */
    protected static $inst = null;

    // -------------
    // configuration
    // -------------

    /**
     * @config
     */
    protected static $conf = [];

    /**
     * default config fallbacks
     * @var array
     */
    protected static $defaults = [
        'debug'         => false,
        'printDebug'    => false,
        'iss'           => null,
        'aud'           => null,
        'keyDir'        => null,
        'baseUrl'       => null,
    ];

    /**
     * [validate_resource_method description]
     * @param  [type] $resource [description]
     * @param  [type] $method   [description]
     * @return void
     * @throws Exception
     */
    public static function validate_resource_method($resource, $method) {

        // get the resource list
        $resources = static::resources();

        // sanity check
        if (!array_key_exists($resource, $resources))   throw new Exception('Resource not found');
        if (!in_array($method, $resources[$resource]))  throw new Exception('Method not found');
        return true;
    }

    /**
     *  @return void
     */
    protected function configure() {

        // configure from YAML if available
        static::set_conf_from_yaml();

        // apply settings to JSON Web token Library
        JWT::$leeway = 300;
    }

    // ----
    // KEYS
    // ----

    /**
     * Gets a public or private key
     * @param  string $token    ISS or token name e.g. 'financial' or 'Catch Development'
     * @param  string $type     'public' or 'private'
     * @return string           the key
     * @throws Exception        can't find the key directory
     */
    public function getKey($token, $type) {
        $conf = static::get_conf();
        return JWTHelper::inst()->getKey($token, $type, $conf->keyDir);
    }

    // --------
    // API CAll
    // --------

    /**
     * make a request to a location with the provided payload via cURL
     * @param  [type] $url     [description]
     * @param  [type] $payload [description]
     * @return [type]          [description]
     */
    protected function request($url, $payload) {

        // get conf
        $conf = static::get_conf();

        // prepare curl request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)));

        // debug
        if ($conf->debug) {

            // verbose output
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            // pipe the output to log file unless we have print debug on
            if (!$conf->printDebug) {
                $log = APP_PATH . '/log/debug.log';
                if (!file_exists($log)) $log = BASE_PATH . '/' . SS_ERROR_LOG;
                if (file_exists($log)) {
                    $fp = fopen(APP_PATH . '/log/debug.log', 'a+');
                    curl_setopt($ch, CURLOPT_STDERR, $fp);
                }
            }
        }

        // make request and cleanup
        $envelope = curl_exec($ch);

        // log any errors
        if (curl_errno($ch)) {
            CLog::log(static::APIID() . ' API Request Error: ' . curl_error($ch), CLog::DEBUG);
        }

        // cleanup
        curl_close($ch);

        // debug output
        if ($conf->debug) {
            $dbg = "RESPONSE ENVELOPE\n";
            $dbg.= "---------------\n";
            $dbg.= $envelope . "\n";
            $dbg.= "\n";
            if ($conf->printDebug) echo $dbg;
            else CLog::log($dbg, CLog::DEBUG);
        }

        // return output
        return $envelope;
    }

    /**
     * [parseResponse description]
     * @param  [type] $payload [description]
     * @return [type]          [description]
     */
    protected function parseResponse($payload) {

        // decode
        $decoded = json_decode($payload);

        // check if it's actually a legit response
        if (empty($decoded->message))
            throw new Exception('api error');

        // check if it's actually a legit response
        if ($decoded->code != 200)
            throw new Exception($decoded->message);

        // return the message
        if (!empty($decoded->message))
            return $decoded->message;

        return $decoded;
    }

    /**
     * [preparePayload description]
     * @param  [type] $resource [description]
     * @param  [type] $method   [description]
     * @param  array  $payload  [description]
     * @param  [type] $txId     [description]
     * @return [type]           [description]
     */
    protected function prepareRequestPayload($resource, $method, array $payload, $txId) {

        // load stuff
        $conf = static::get_conf();
        $issPrivateKey = $this->getKey($conf->iss, 'private');

        // inject the txnid
        $payload['id'] = $txId;

        // prepare jwt
        $token = [
            'jti'       => 'JWT',
            'iss'       => $conf->iss,
            'iat'       => time(),
            'payload'   => $payload,
        ];

        // debug output
        if ($conf->debug) {
            $dbg = "REQUEST PAYLOAD\n";
            $dbg.= "---------------\n";
            $dbg.= print_r($token, 1);
            $dbg.= "\n\n";
            if ($conf->printDebug) echo $dbg;
            else CLog::log($dbg, CLog::DEBUG);
        }

        // generate envelope and encoded jwt
        $envelope = json_encode(['message' => JWT::encode($token, $issPrivateKey, 'RS256')]);

        // debug output
        if ($conf->debug) {
            $dbg = "REQUEST ENVELOPE\n";
            $dbg.= "---------------\n";
            $dbg.= $envelope . "\n";
            $dbg.= "\n";
            if ($conf->printDebug) echo $dbg;
            else CLog::log($dbg, CLog::DEBUG);

        }

        // return envelope
        return $envelope;
    }

    /**
     * [decodeJWT description]
     * @param  [type] $response [description]
     * @param  array  $key      [description]
     * @return [type]           [description]
     */
    public function decodeJWT($jwt, array $key = array()) {

        $validationKey = (!empty($key))
            ? $this->getKey($key['token'], $key['type'])
            : $validationKey = $this->getKey('api', 'public');

        return JWT::decode($jwt, $validationKey, ['RS256']);
    }

    /**
     * [prepareResponsePayload description]
     * @param  [type] $resource   [description]
     * @param  [type] $method     [description]
     * @param  [type] $resPayload [description]
     * @return array
     */
    protected function prepareResponsePayload($resource, $method, $resPayload) {

        // load stuff
        $conf = static::get_conf();

        // extract data (does some validation)
        $resPayload = $this->decodeJWT($resPayload);

        // debug output
        if ($conf->debug) {
            $dbg = "RESPONSE PAYLOAD\n";
            $dbg.= "---------------\n";
            $dbg.= print_r($resPayload, 1);
            $dbg.= "\n\n";
            if ($conf->printDebug) echo $dbg;
            else CLog::log($dbg, CLog::DEBUG);
        }

        // extract payload (does some validation)
        if (empty($resPayload->payload))
            throw new Exception('Returned Object is Invalid');
        else $payload = $resPayload->payload;

        // return
        return $payload;
    }

    /**
     * [validateResponse description]
     * @param  [type] $resource   [description]
     * @param  [type] $method     [description]
     * @param  [type] $resPayload [description]
     * @param  [type] $txId       [description]
     * @return $this
     */
    protected function validateResponse($resource, $method, $resPayload, $txId) {

        // JWT decode - validates on extraction throws SignatureInvalidException
        $resPayload = $this->decodeJWT($resPayload);

        return $this;
    }
}
