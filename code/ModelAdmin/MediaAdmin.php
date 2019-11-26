<?php

namespace CatchDesign\EmbedableGallery\ModelAdmin;

use ModelAdmin;
use CatchDesign\EmbedableGallery\SiteTree\ImagePage;
use CatchDesign\EmbedableGallery\SiteTree\VideoPage;
use CatchDesign\EmbedableGallery\SiteTree\AlbumPage;



class MediaPageAdmin extends ModelAdmin {

    /**
     * [$managed_models description]
     * @var array
     */
    private static $managed_models = array(
        ImagePage::class,
        VideoPage::class,
        AlbumPage::class
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
