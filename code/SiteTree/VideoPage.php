<?php
class VideoPage extends MediaPage {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $extensions = array(
        'SSEmbedableGalleryLeftAndMainExtension'
    );

    public function handleParents() {

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one('VideosHolder')) {

            // make sure the default records are present
            singleton('MediaHomePage')->createDefaultPages();

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $page = DataObject::get_one('VideosHolder');

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

class VideoPage_Controller extends MediaPage_Controller {
}
