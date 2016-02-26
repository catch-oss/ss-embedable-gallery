<?php
class ImagesHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        'ImagePage'
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        'ImagePage'
    );

    public function Images() {
        return DataList::create('ImagePage')->filter(array('ParentID' => $this->ID));
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
