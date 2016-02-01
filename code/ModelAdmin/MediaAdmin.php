<?php

class MediaPageAdmin extends ModelAdmin {

    /**
     * [$managed_models description]
     * @var array
     */
    private static $managed_models = array(
        'ImagePage',
        'VideoPage',
        'AlbumPage'
    );

    /**
     * [$url_segment description]
     * @var string
     */
    private static $url_segment = 'media-pages';

    /**
     * [$menu_title description]
     * @var string
     */
    private static $menu_title = 'Media';
}
