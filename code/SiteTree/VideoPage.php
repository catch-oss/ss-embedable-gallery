<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use CatchDesign\EmbedableGallery\Extensions\SSEmbedableGalleryEmbedLinkExtension;
use CatchDesign\EmbedableGallery\SiteTree\VideosHolder;
use CatchDesign\EmbedableGallery\SiteTree\MediaHomePage;
use SilverStripe\ORM\DataObject;

class VideoPage extends MediaPage
{
    private static $table_name = 'VideoPage';
    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $extensions = array(
        SSEmbedableGalleryEmbedLinkExtension::class
    );

    public function handleParents()
    {

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one(VideosHolder::class)) {

            // make sure the default records are present
            singleton(MediaHomePage::class)->createDefaultPages();

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $page = DataObject::get_one(VideosHolder::class);
        }

        // set parent
        $this->ParentID = $page->ID;
    }

    public function validate()
    {
        $this->handleParents();
        return parent::validate();
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->handleParents();
    }
}

class VideoPage_Controller extends MediaPage_Controller
{
}
