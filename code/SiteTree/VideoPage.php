<?php
class VideoPage extends MediaPage {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    public function onBeforeWrite() {

        // parent
        parent::onBeforeWrite();

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one('VideosHolder')) {

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $home = DataObject::get_one('MediaHomePage');

            // create
            $page = VideosHolder::create()->update([
                'Title' => 'Videos',
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

class VideoPage_Controller extends MediaPage_Controller {
}
