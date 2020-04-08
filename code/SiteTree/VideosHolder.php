<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use \Page;
use Azt3k\SS\Forms\ChildPageGridFieldDetailForm;
use CatchDesign\EmbedableGallery\SiteTree\VideoPage;
use SilverStripe\ORM\DataList;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField;
use \PageController;

class VideosHolder extends Page
{
    private static $table_name = 'VideosHolder';
    private static $can_be_root = false;

    private static $allowed_children = array(
        VideoPage::class
    );

    private static $hide_from_cms_tree = array(
        VideoPage::class
    );

    public function Videos()
    {
        return DataList::create(VideoPage::class)->filter(array('ParentID' => $this->ID));
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Videos',
            GridField::create(
                'Children',
                'Videos',
                $this->Videos(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType(GridFieldDetailForm::class)
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;
    }
}

class VideosHolder_Controller extends PageController
{
}
