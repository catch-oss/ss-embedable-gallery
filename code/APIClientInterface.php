<?php

interface APIClientInterface {

    /**
     * this is used to identify the API
     */
    static function APIID();

    /**
     * this is used to identify the resources provided by the API
     */
    static function resources();

    /**
     * [validate_resource_method description]
     * @return [type] [description]
     */
    static function validate_resource_method($resource, $method);
}
