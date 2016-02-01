<?php
class MediaHomePage extends Page {

    private static $allowed_children = array(
        'MediaPage',
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        'MediaPage'
    );

    public function Media() {
        return DataList::create('MediaPage')->filter(array('ParentID' => $this->ID));
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Media',
            GridField::create(
                'Children',
                'Media',
                $this->Media(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType('GridFieldDetailForm')
                    ->addComponent(new ChildPageGridFieldDetailForm)
            )
        );

        return $fields;

    }
}

class MediaHomePage_Controller extends Page_Controller {
}
