<?php

namespace CatchDesign\SSAPInterface;


use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Config\Configurable;

class APIUtils {

    use Extensible;
    use Injectable;
    use Configurable;
    /**
     * [check_ip description]
     * @param  [type] $ip [description]
     * @return [type]     [description]
     */
    public static function check_ip($ip) {
        if (!empty($ip) && ip2long($ip) != -1 && ip2long($ip) != false) {
            $private_ips = array(
               array('0.0.0.0','2.255.255.255'),
               array('10.0.0.0','10.255.255.255'),
               array('127.0.0.0','127.255.255.255'),
               array('169.254.0.0','169.254.255.255'),
               array('172.16.0.0','172.31.255.255'),
               array('192.0.2.0','192.0.2.255'),
               array('192.168.0.0','192.168.255.255'),
               array('255.255.255.0','255.255.255.255')
            );

            foreach ($private_ips as $r) {
                $min = ip2long($r[0]);
                $max = ip2long($r[1]);
                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * [determine_ip description]
     * @return [type] [description]
     */
    public static function determine_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']) &&
            static::check_ip($_SERVER['HTTP_CLIENT_IP'])
        ) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip) {
                if (static::check_ip(trim($ip))) {
                    return $ip;
                }
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) &&
            static::check_ip($_SERVER['HTTP_X_FORWARDED'])
        ) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) &&
            static::check_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])
        ) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']) &&
            static::check_ip($_SERVER['HTTP_FORWARDED_FOR'])
        ) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        elseif (!empty($_SERVER['HTTP_FORWARDED']) &&
            static::check_ip($_SERVER['HTTP_FORWARDED'])
        ) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }


    /**
     *  @param  array $array1 The first array
     *  @param  array $array2 The second array
     *  @return array the merged array
     */
    public static function array_merge_recursive_distinct(array $array1, array $array2) {

        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Generates a UUIDv4 String
     * @return string
     */
    public static function gen_uuid() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * matches an ip against an ip range in CIDR format
     * @param  String   $ip     the ip to test
     * @param  String   $cidr   the range we are testing against
     * @return Boolean          did the IP match?
     */
    public static function cidr_match($ip, $cidr) {

        // convert CIDR expression in a subnet and a mask
        list($subnet, $mask) = explode('/', $cidr);

        // do the comparison and return the result
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet);
    }
}
