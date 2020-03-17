<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use \Page;
use CatchDesign\EmbedableGallery\SiteTree\AlbumPage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\LiteralField;
use \PageController;

/**
 * @todo - this page needs to present an option to choose a subclass before it presents the actual edit form
 */
class MediaPage extends Page
{
    private static $table_name = 'MediaPage';

    private static $belongs_many_many = array(
        'Albums' => AlbumPage::class
    );

    public function getCMSFields()
    {

        // make sure a media page isn't saved as a media page
        if ($this->ClassName == MediaPage::class) {

            // clean slate
            $fields = new FieldList;
            $fields->push(new TabSet('Root'));

            // Generate Type Selector
            $classes = ClassInfo::subclassesFor(MediaPage::class);
            $list = array();
            foreach ($classes as $class) {
                $list[$class] = $class == MediaPage::class ? 'Please Select a Media Type' : $class;
            }
            $fields->addFieldToTab('Root.Main', new DropdownField('ClassName', 'Type', $list));

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
        else {
            $fields = parent::getCMSFields();
        }

        return $fields;
    }
}

class MediaPage_Controller extends PageController
{
}
