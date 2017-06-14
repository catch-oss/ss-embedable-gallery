<?php

interface APIClientInterface {

    /**
     * this is used to identify the API
     */
    public static function APIID();

    /**
     * this is used to identify the resources provided by the API
     */
    public static function resources();

    /**
     * [validate_resource_method description]
     * @return [type] [description]
     */
    public static function validate_resource_method($resource, $method);
}
