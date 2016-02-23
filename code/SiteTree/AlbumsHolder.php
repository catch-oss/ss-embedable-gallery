<?php
class AlbumsHolder extends Page {

    private static $can_be_root = false;

    private static $allowed_children = array(
        'AlbumPage'
    );

    private static $extensions = array(
        'ExcludeChildren',
    );

    private static $excluded_children = array(
        'AlbumPage'
    );

    public function Albums() {
        return DataList::create('AlbumPage')->filter(array('ParentID' => $this->ID));
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
