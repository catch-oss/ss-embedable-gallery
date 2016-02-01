<?php
class VideosHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        'VideoPage'
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        'VideoPage'
    );

    public function Videos() {
        return DataList::create('VideoPage')->filter(array('ParentID' => $this->ID));
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
