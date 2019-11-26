<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use Page;
use DataList;
use GridField;
use GridFieldConfig_RelationEditor;
use ChildPageGridFieldDetailForm;
use Page_Controller;
use CatchDesign\EmbedableGallery\SiteTree\VideoPage;


class VideosHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        VideoPage::class
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        VideoPage::class
    );

    public function Videos() {
        return DataList::create(VideoPage::class)->filter(array('ParentID' => $this->ID));
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Videos',
            GridField::create(
                'Children',
                'Videos',
                $this->Videos(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType('GridFieldDetailForm')
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;

    }
}

class VideosHolder_Controller extends Page_Controller {
}
