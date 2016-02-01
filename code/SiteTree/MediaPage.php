<?php
class MediaPage extends Page {

    private static $belongs_many_many = array(
        'Albums' => 'AlbumPage'
    );

    public function onBeforeWrite() {

        // parent
        parent::onBeforeWrite();

        // find the media home page, if it doesn't exist - create it
        if (!$home = DataObject::get_one('MediaHomePage')) {

            // create
            $home = MediaHomePage::create()->update([
                'Title' => 'Media',
                'ShowInMenus' => false,
            ]);

            // write to all the places
            $home->write();
            $home->doRestoreToStage();
            $home->doPublish();
        }
    }
}

class MediaPage_Controller extends Page_Controller{
}
