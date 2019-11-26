<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use Page;
use DataList;
use GridField;
use GridFieldConfig_RelationEditor;
use ChildPageGridFieldDetailForm;
use Page_Controller;
use CatchDesign\EmbedableGallery\SiteTree\AlbumPage;


class AlbumsHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        AlbumPage::class
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        AlbumPage::class
    );

    public function Albums() {
        return DataList::create(AlbumPage::class)->filter(array('ParentID' => $this->ID));
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Albums',
            GridField::create(
                'Children',
                'Albums',
                $this->Albums(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType('GridFieldDetailForm')
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;

    }
}

class AlbumsHolder_Controller extends Page_Controller {
}
