<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use Page;
use DataList;
use GridField;
use GridFieldConfig_RelationEditor;
use ChildPageGridFieldDetailForm;
use Page_Controller;
use CatchDesign\EmbedableGallery\SiteTree\ImagePage;


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
                    ->removeComponentsByType('GridFieldDetailForm')
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;

    }
}

class ImagesHolder_Controller extends Page_Controller {
}
