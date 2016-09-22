<?php

use Firebase\JWT\JWT;

/**
 * @Todo - off load some of this to the JWT Helper
 */
abstract class APIClient extends Object {

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
	 * this is used to identify the API
	 */
	abstract public static function APIID();

	/**
	 * this is used to identify the API
	 */
	abstract public static function resources();

    /**
     * [validate_resource_method description]
     * @return [type] [description]
     */
    abstract protected static function validate_resource_method($resource, $method);

    /**
     *  @param  array|object $conf An associative array containing the configuration - see self::$conf for an example
     *  @return void
     */
    public static function set_conf($conf) {
        $conf = (array) $conf;
        static::$conf = static::array_merge_recursive_distinct(static::$conf, $conf);
    }

    /**
     *  @return stdClass
     */
    public static function get_conf() {
        return (object) static::array_merge_recursive_distinct(static::$defaults, static::$conf);
    }

    /**
     * @return void
     */
    protected static function set_conf_from_yaml() {
        $conf = (array) Config::inst()->get(__CLASS__, 'conf');
        if (!empty($conf))
            static::$conf = static::array_merge_recursive_distinct(static::$conf, $conf);
    }

    /**
     *  @return void
     */
    protected function configure() {

        // configure from YAML if available
        static::set_conf_from_yaml();
    }

    /**
     *  @param  array $array1 The first array
     *  @param  array $array2 The second array
     *  @return array the merged array
     */
    public static function array_merge_recursive_distinct(array $array1, array $array2) {
        return JWTHelper::array_merge_recursive_distinct($array1, $array2);
    }


    // -------------
    // Instantiation
    // -------------


    /**
     * [inst description]
     * @return [type] [description]
     */
    public static function inst() {
        if (!static::$inst) static::$inst = new static;
        return static::$inst;
    }

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->configure();
    }

    // -----------------
    // per instance conf
    // -----------------

    public function setDebug($flag) {
        static::$conf['debug'] = $flag;
        return $this;
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
        return JWTHelper::inst()->getKey($token, $type);
    }

    // --------
    // API CAll
    // --------


    /**
     * [endpoint description]
     * @param  [type] $resource [description]
     * @param  [type] $method   [description]
     * @return string           the url to the endpoint we need to hit
     */
    abstract protected function endpoint($resource, $method);

    /**
     * make a request to a location with the provided payload via cURL
     * @param  [type] $url     [description]
     * @param  [type] $payload [description]
     * @return [type]          [description]
     */
    abstract protected function request($url, $payload);

    /**
     * [parseResponse description]
     * @param  [type] $payload [description]
     * @return [type]          [description]
     */
    abstract protected function parseResponse($payload);

    /**
     * [preparePayload description]
     * @param  [type] $resource [description]
     * @param  [type] $method   [description]
     * @param  array  $payload  [description]
     * @param  [type] $txId     [description]
     * @return [type]           [description]
     */
    abstract protected function prepareRequestPayload($resource, $method, array $payload, $txId);

    /**
     * [prepareResponsePayload description]
     * @param  [type] $resource   [description]
     * @param  [type] $method     [description]
     * @param  [type] $resPayload [description]
     * @return array
     */
    abstract protected function prepareResponsePayload($resource, $method, $jwt);

    /**
     * [validateResponse description]
     * @param  [type] $resource   [description]
     * @param  [type] $method     [description]
     * @param  [type] $resPayload [description]
     * @param  [type] $txId       [description]
     * @return $this
     */
    abstract protected function validateResponse($resource, $method, $jwt, $txId);

    /**
     * [call description]
     * @param  [type] $endpoint one of static::$endpoints
     * @param  [type] $payload  sanitised json object without wrapper
     * @param  [type] $method   [description]
     * @return array
     * @throws Exception
     */
    public function call(array $conf) {

        // parse input
        $resource = $conf['resource'];
        $method = empty($conf['method']) ? 'index' : $conf['method'];
        $reqPayload = $conf['payload'];

        // catch the errors
        try {

            // validate that call before we make
            if (!static::validate_resource_method($resource, $method))
                throw new Exception('Invalid Resource / Method');

            // prepare call
            $ep         = $this->endpoint($resource, $method);
            $txId       = Utils::gen_uuid();
            $reqPayload = $this->prepareRequestPayload($resource, $method, $reqPayload, $txId);

            // make call
            $response = $this->request($ep, $reqPayload);

            // extract data response
            $data = $this->parseResponse($response);

            // get return payload
            $resPayload = $this->validateResponse($resource, $method, $data, $txId)
                               ->prepareResponsePayload($resource, $method, $data);

        } catch (Exception $e) {

            // generate the payload
            $resPayload = (object) [
                'status'    => 'error',
                'info'      => [
                    'code'      => 500,
                    'message'   => $e->getMessage()
                ]
            ];

            // log the error
            SS_Log::log(static::APIID() . ' API Exception: ' . $e->getMessage(), SS_Log::DEBUG);
        }

        // return
        return $resPayload;

    }
}
