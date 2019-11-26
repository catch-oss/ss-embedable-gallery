<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use Page;
use GridField;
use GridFieldConfig_RelationEditor;
use GridFieldSortableRows;
use VersionedGridFieldDetailForm;
use DataObject;
use Page_Controller;
use CatchDesign\EmbedableGallery\SiteTree\MediaPage;
use CatchDesign\EmbedableGallery\SiteTree\AlbumsHolder;
use CatchDesign\EmbedableGallery\SiteTree\MediaHomePage;


class AlbumPage extends Page {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $many_many = array(
        'Media' => MediaPage::class
    );

    private static $many_many_extraFields = array(
        'Media' => array(
            'SortOrder' => 'Int',
            'InvSortOrder' => 'Int'
        ),
    );

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        // Add the gridfield
        $fields->addFieldToTab(
            'Root.Media',
            new GridField(
                'Media',
                'Media',
                $this->Media(),
                GridFieldConfig_RelationEditor::create()
                    ->addComponent(new GridFieldSortableRows('SortOrder'))
                    ->removeComponentsByType('GridFieldDetailForm')
                    ->addComponent(new VersionedGridFieldDetailForm)
            )
        );

        return $fields;
    }

    public function handleParents() {

        // find the media home page, if it doesn't exist - create it
        if (!$page = DataObject::get_one(AlbumsHolder::class)) {

            // make sure the default records are present
            singleton(MediaHomePage::class)->createDefaultPages();

            // media home - created in default record and as a fall back in parent::onBeforeWrite()
            $page = DataObject::get_one(AlbumsHolder::class);

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

class AlbumPage_Controller extends Page_Controller {
}
