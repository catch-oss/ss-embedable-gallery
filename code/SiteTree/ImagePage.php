<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use CatchDesign\EmbedableGallery\SiteTree\ImagesHolder;
use CatchDesign\EmbedableGallery\SiteTree\MediaHomePage;
use SilverStripe\ORM\DataObject;

/**
 * @todo make this more portable - i.e. either require some other module like abc-silverstripe-social or explicitly define has one images etc
 */
class ImagePage extends MediaPage {
    private static $table_name = 'ImagePage';

    private static $can_be_root = false;
    private static $allowed_children = 'none';
    public function handleParents() {

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one(ImagesHolder::class)) {

            // make sure the default records are present
            singleton(MediaHomePage::class)->createDefaultPages();

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $page = DataObject::get_one(ImagesHolder::class);

        }

        // set parent
        $this->ParentID = $page->ID;
    }

    public function validate() {
        $this->handleParents();
        return parent::validate();
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->handleParents();
    }
}

class ImagePage_Controller extends MediaPage_Controller {
}
