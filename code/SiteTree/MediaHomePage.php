<?php
class MediaHomePage extends Page {

    private static $can_be_root = true;

    private static $allowed_children = array(
        'AlbumsHolder',
        'ImagesHolder',
        'VideosHolder'
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

    /**
     * Add default records to database.
     *
     * This function is called whenever the database is built, after the
     * database tables have all been created. Overload this to add default
     * records when the database is built, but make sure you call
     * parent::requireDefaultRecords().
     */
    public function requireDefaultRecords() {

        if (SiteTree::config()->create_default_pages) {

            // create the other home pages
            $home = $this->CreateDefaultPage('MediaHomePage', 'Media', 3, null, false, 'media', true);
            $this->CreateDefaultPage('AlbumsHolder', 'Albums', 1, $home)
                 ->CreateDefaultPage('ImagesHolder', 'Images', 2, $home)
                 ->CreateDefaultPage('VideosHolder', 'Videos', 1, $home);

        }

        // call it on the parent
        parent::requireDefaultRecords();

    }

    /**
     * Helper Method for creating default records
     * @param  string $type                [description]
     * @param  string $title               [description]
     * @param  string $sort                [description]
     * @param  string $parent              [description]
     * @param  bool   $showInMenus         [description]
     * @param  string $path                [description]
     * @param  bool   $returnCreatedRecord [description]
     * @return SiteTree                    [description]
     */
    protected function createDefaultPage(
        $type,
        $title,
        $sort,
        $parent = null,
        $showInMenus = true,
        $path = null,
        $returnCreatedRecord = false
    ) {

        // make the page
        if (!$page = DataObject::get_one($type)) {
            $page = new $type;
            $page->Title = $title;
            $page->ShowInMenus = $showInMenus;
            $page->Sort = $sort;
            if ($path) $page->URLSegment = $path;
            if ($parent) $page->ParentID = $parent->ID;
            $page->write();
            $page->doRestoreToStage();
            $page->doPublish();
            $page->flushCache();
            DB::alteration_message($title . ' (' . $type . ') created', 'created');
        }

        // make chainable
        return $returnCreatedRecord ? $page : $this;
    }


}

class MediaHomePage_Controller extends Page_Controller {
}
