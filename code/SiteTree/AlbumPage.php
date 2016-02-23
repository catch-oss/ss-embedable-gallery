<?php
class AlbumPage extends Page {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $many_many = array(
        'Media' => 'MediaPage'
    );

    public function onBeforeWrite() {

        // parent
        parent::onBeforeWrite();

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one('AlbumsHolder')) {

            // make sure the default records are present
            singleton('MediaHomePage')->createDefaultPages();

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $page = DataObject::get_one('AlbumsHolder');

        }

        // set parent
        $this->ParentID = $page->ID;
    }
}

class AlbumPage_Controller extends Page_Controller {
}
