<?php
class MediaPage extends Page {

    private static $belongs_many_many = array(
        'Albums' => 'AlbumPage'
    );
}

class MediaPage_Controller extends Page_Controller{
}
