<?php
class VideoPage extends MediaPage {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $belongs_many_many = array(
        'News'      => 'NewsArticlePage',
        'Athletes'  => 'AthletePage',
    );

    public function News() {
        return $this->getManyManyComponents('News')->sort('InvSortOrder');
    }

    public function Athletes() {
        return $this->getManyManyComponents('Athletes')->sort('InvSortOrder');
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        // add the relation editors
        $this->AddLinkedContentFields('News', $fields, true, 'InvSortOrder')
             ->AddBasicRelationEditor('Athletes', $fields, true, 'InvSortOrder');

        return $fields;
    }
}

class VideoPage_Controller extends MediaPage_Controller {
}
