<?php

namespace CatchDesign\EmbedableGallery\SiteTree;

use Page;
use SiteTree;
use DataObject;
use DB;
use Page_Controller;
use CatchDesign\EmbedableGallery\SiteTree\AlbumsHolder;
use CatchDesign\EmbedableGallery\SiteTree\ImagesHolder;
use CatchDesign\EmbedableGallery\SiteTree\VideosHolder;


class MediaHomePage extends Page {

    private static $can_be_root = true;

    private static $allowed_children = array(
        AlbumsHolder::class,
        ImagesHolder::class,
        VideosHolder::class
    );

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
            $this->createDefaultPages();

        }

        // call it on the parent
        parent::requireDefaultRecords();

    }

    public function createDefaultPages() {
        $home = $this->CreateDefaultPage(MediaHomePage::class, 'Media', 3, null, false, 'media', true);
        $this->CreateDefaultPage(AlbumsHolder::class, 'Albums', 1, $home)
             ->CreateDefaultPage(ImagesHolder::class, 'Images', 2, $home)
             ->CreateDefaultPage(VideosHolder::class, 'Videos', 1, $home);
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
