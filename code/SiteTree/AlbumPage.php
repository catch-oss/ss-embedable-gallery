<?php
class AlbumPage extends Page {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $many_many = array(
        'Media' => 'MediaPage'
    );
}

class AlbumPage_Controller extends Page_Controller {
}
