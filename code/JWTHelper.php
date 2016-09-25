<?php

use Firebase\JWT\JWT;

class JWTHelper extends Object {

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
    private static $conf = [];

    /**
     * default config fallbacks
     * @var array
     */
    private static $defaults = [
        'debug'         => false,
        'printDebug'    => false,
        'iss'           => 'Silverstripe Platform',
        'keyDir'        => 'keys',
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

        // apply settings to JSON Web token Library
        JWT::$leeway = 60;
    }

    /**
     *  @param  array $array1 The first array
     *  @param  array $array2 The second array
     *  @return array the merged array
     */
    public static function array_merge_recursive_distinct(array $array1, array $array2) {
        return Utils::array_merge_recursive_distinct($array1, $array2);
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

        // get conf
        $conf = static::get_conf();

        // get key path
        if (!is_dir($path = APP_PATH . '/' . $conf->keyDir))
            if (!is_dir($path = PUBLIC_PATH . '/' . $conf->keyDir))
                throw new Exception('Cannot find key directory ' . $conf->keyDir);

        // normalise token
        $token = preg_replace('/[^A-Za-z0-9\/]+/', '-', strtolower($token));

        // generate full path to key
        $keyPath = $path . '/' . $token . '/' . $type . '.key';

        // validate
        if (!is_file($keyPath))
            throw new Exception('Cannot find key ' . $keyPath);

        // return
        return file_get_contents($keyPath);
    }

    /**
     * Generates a UUIDv4 String
     * @return string
     */
    public static function gen_uuid() {
        return Utils::gen_uuid();
    }


    /**
     * [decodeJWT description]
     * @param  [type] $response [description]
     * @param  array  $key      [description]
     * @return [type]           [description]
     */
    public function decodeJWT($jwt, array $key) {
        $validationKey = $this->getKey($key['token'], $key['type']);
        return JWT::decode($jwt, $validationKey, ['RS256']);
    }
}
