<?php
/**
 * @todo - this page needs to present an option to choose a subclass before it presents the actual edit form
 */
class MediaPage extends Page {

    private static $belongs_many_many = array(
        'Albums' => 'AlbumPage'
    );

    function getCMSFields() {

        // make sure a media page isn't saved as a media page
        if ($this->ClassName == 'MediaPage') {

            // clean slate
            $fields = new FieldList;
            $fields->push(new TabSet('Root'));

            // Generate Type Selector
            $classes = ClassInfo::subclassesFor('MediaPage');
            $list = array();
            foreach ($classes as $class) {
                $list[$class] = $class == 'MediaPage' ? 'Please Select a Media Type' : $class;
            }
            $fields->addFieldToTab('Root.Main', new DropdownField('ClassName','Type', $list));

            // Display the notice if they haven't selected a type
            $fields->addFieldToTab(
                'Root.Main',
                new LiteralField(
                    'Notice',
                    '<p>
                        This is an empty media page,
                        you need to select a type and save to reveal other formatting options.
                    </p>'
                )
            );
        }

        // get the normal fields
        else $fields = parent::getCMSFields();

        return $fields;
    }
}

class MediaPage_Controller extends Page_Controller{
}
