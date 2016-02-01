<?php
class ImagePage extends MediaPage {

    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $db = array(
        'ZeusHash' => 'Varchar(255)'
    );

    private static $has_one = array(
        'Games' => 'GamesPage'
    );

    private static $belongs_many_many = array(
        'News'      => 'NewsArticlePage',
        'Athletes'  => 'AthletePage',
    );

    public static function gen_zeus_hash(stdClass $data, AthletePage $athete) {
        return sha1(
            $athete->ZeusID .
            $data->FileName .
            $data->GamesID
        );
    }

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

class ImagePage_Controller extends MediaPage_Controller {
}
