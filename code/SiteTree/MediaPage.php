<?php
class MediaPage extends Page {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $db = array(
    );

    private static $has_many = array(
    );

    public function onBeforeWrite() {

        // parent
        parent::onBeforeWrite();

        // find the media home page, if it doesn't exist - create it
        if (!$parent = DataObject::get_one('MediaHomePage')) {

            // create
            $parent = MediaHomePage::create()->update([
                'Title' => 'Media',
                'ShowInMenus' => false,
            ]);

            // write to all the places
            $parent->write();
            $parent->doRestoreToStage();
            $parent->doPublish();
        }

        // make sure this ends up in the right place
        $this->ParentID = $parent->ID;
    }
}

class MediaPage_Controller extends Page_Controller {

    private static $allowed_actions = array ();

    public function init() {
        parent::init();
    }
}
