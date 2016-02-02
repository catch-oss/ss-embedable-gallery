<?php
class MediaPage extends Page {

    private static $belongs_many_many = array(
        'Albums' => 'AlbumPage'
    );

    public function onBeforeWrite() {

        // parent
        parent::onBeforeWrite();

        // Make sure the required records are present
        singleton('MediaHomePage')->requireDefaultRecords();
    }
}

class MediaPage_Controller extends Page_Controller{
}
