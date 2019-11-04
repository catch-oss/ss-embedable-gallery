<?php

use Firebase\JWT\JWT;

/**
 * @Todo - off load some of this to the JWT Helper
 */
abstract class APIClient extends SS_Object implements APIClientInterface {

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
        $conf = (array) Config::inst()->get(get_called_class(), 'conf');
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
        return APIUtils::array_merge_recursive_distinct($array1, $array2);
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
     * make a request to a location with the provided payload
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
    abstract protected function prepareResponsePayload($resource, $method, $resPayload);

    /**
     * [validateResponse description]
     * @param  [type] $resource   [description]
     * @param  [type] $method     [description]
     * @param  [type] $resPayload [description]
     * @param  [type] $txId       [description]
     * @return $this
     */
    abstract protected function validateResponse($resource, $method, $resPayload, $txId);

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
            $txId       = APIUtils::gen_uuid();
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
                    'code'      => $e->getCode() ?: 500,
                    'message'   => $e->getMessage()
                ]
            ];

            // log the error
            CLog::log(static::APIID() . ' API Exception: ' . $e->getMessage(), CLog::DEBUG);
        }

        // return
        return $resPayload;

    }
}
