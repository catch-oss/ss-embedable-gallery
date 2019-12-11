<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use \Page;

use Azt3k\SS\Forms\ChildPageGridFieldDetailForm;
use CatchDesign\EmbedableGallery\SiteTree\AlbumPage;
use SilverStripe\ORM\DataList;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField;
use PageController;



class AlbumsHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        AlbumPage::class
    );

    private static $extensions = array(
        \micschk\ExcludeChildren::class,
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
                    ->removeComponentsByType(GridFieldDetailForm::class)
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;

    }
}

class AlbumsHolder_Controller extends PageController {
}
