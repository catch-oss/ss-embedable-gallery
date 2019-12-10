<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use \Page;

use Azt3k\SS\Forms\ChildPageGridFieldDetailForm;
use CatchDesign\EmbedableGallery\SiteTree\ImagePage;
use SilverStripe\ORM\DataList;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField;
use \PageController;

class ImagesHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        ImagePage::class
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        ImagePage::class
    );

    public function Images() {
        return DataList::create(ImagePage::class)->filter(array('ParentID' => $this->ID));
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Images',
            GridField::create(
                'Children',
                'Image Pages',
                $this->Images(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType(GridFieldDetailForm::class)
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;

    }
}

class ImagesHolder_Controller extends PageController {
}
