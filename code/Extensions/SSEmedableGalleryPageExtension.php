<?php

namespace CatchDesign\EmbedableGallery\Extensions;

use CatchDesign\EmbedableGallery\SiteTree\AlbumPage;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Upload_Validator;
use SilverStripe\ORM\DataExtension;

/**
 * @todo need reconcile removals in both directions
 * @todo remove PublicationFBUpdateID && PublicationTweetID as they aren't really needed any more - if testing for post just call $this->owner->PublicationTweets()->count()
 */
class SSEmedableGalleryPageExtension extends DataExtension
{
    private static $casting = [
        'AlbumEmbedParser' => 'HTMLText',
    ];

    private static $has_one = [
        'PrimaryImage'      => Image::class,
    ];

    public function updateCMSFields(\SilverStripe\Forms\FieldList $fields)
    {
        // images
        $iValidator = new Upload_Validator();
        $iValidator->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $fields->AddFieldsToTab(
            'Root.Images',
            [
                UploadField::create('PrimaryImage')->setValidator($iValidator),
            ]
        );
    }

    // Short Code parser
    // -----------------

    /**
     * parses out short codes:
     * [album_embed,id="123"].
     *
     * @param [type] $arguments [description]
     * @param [type] $content   [description]
     * @param [type] $parser    [description]
     * @param [type] $tagName   [description]
     */
    public static function AlbumEmbedParser($arguments, $content = null, $parser = null, $tagName)
    {
        $album = AlbumPage::get()->filter(['ID' => $arguments['id']])->first();

        return $album->renderWith('EmbeddedAlbum');
    }
}
