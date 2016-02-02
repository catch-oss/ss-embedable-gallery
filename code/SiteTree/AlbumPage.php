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

        // make sure the default records are present
        singleton('MediaHomePage')->requireDefaultRecords();

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one('AlbumsHolder')) {

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $home = DataObject::get_one('MediaHomePage');

            // create
            $page = AlbumsHolder::create()->update([
                'Title' => 'Images',
                'ParentID' => $home->ID
            ]);

            // write to all the places
            $page->write();
            $page->doRestoreToStage();
            $page->doPublish();
        }

        // set parent
        $this->ParentID = $page->ID;
    }
}

class AlbumPage_Controller extends Page_Controller {
}
