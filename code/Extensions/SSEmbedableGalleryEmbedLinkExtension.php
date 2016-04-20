<?php

class SSEmbedableGalleryEmbedLinkExtension extends DataExtension {

    private static $db = array(
        'EmbedLink'     => 'Varchar(255)',
    );

    public function updateCMSFields(FieldList $fields) {
        parent::updateCMSFields($fields);
        $fields->addFieldToTab('Root.Media', new TextField('EmbedLink', 'Embed Link'));
    }

    public function is_ie() {
        return preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']);
    }

    protected function embedData($id = null, $autoplay = true) {

        // init reciever
        $r = [
            'href' => null,
            'type' => null
        ];

        // generate embed code if there's a link
        if ($link = $this->owner->EmbedLink) {

            if (preg_match('/(youtube\.com|youtu\.be)\/(v\/|u\/|embed\/|watch\?v=)?([^#\&\?]*).*/', $link, $matches)) {
                $href = 'http://www.youtube.com/embed/' . $matches[3] .
                        '?autohide=2&fs=0&rel=0&enablejsapi=1&modestbranding=1&showinfo=0' .
                        ($autoplay ? '&autoplay=1' : '');
                $type = 'iframe';
            }
            else if (preg_match('/vimeo.com\/(video\/)?(\d+)\/?(.*)/', $link, $matches)) {
                $href = 'http://player.vimeo.com/video/' . $matches[2] .
                        '?hd=1&api=1&show_title=1&show_byline=1&badge=0&show_portrait=0&color=&fullscreen=1' .
                        ($id ? '&player_id=' . $id : '') . ($autoplay ? '&autoplay=1' : '');
                $type = 'iframe';
            }
            else if (preg_match('/vimeo.com\/channels\/(.+)\/(\d+)\/?/', $link, $matches)) {
                $href = 'http://player.vimeo.com/video/' . $matches[2] .
                        '?hd=1&api=1&show_title=1&show_byline=1&badge=0&show_portrait=0&color=&fullscreen=1' .
                        ($id ? '&player_id=' . $id : '') . ($autoplay ? '&autoplay=1' : '');
                $type = 'iframe';
            }
            else if (preg_match('/metacafe.com\/watch\/(\d+)\/?(.*)/', $link, $matches)) {
                $href = 'http://www.metacafe.com/fplayer/' . $matches[1] .
                        '/.swf' . ($autoplay ? '?playerVars=autoPlay=yes' : '');
                $type = 'swf';
            }
            else if (preg_match('/dailymotion.com\/video\/(.*)\/?(.*)/', $link, $matches)) {
                $href = 'http://www.dailymotion.com/swf/video/' . $matches[1] .
                        '?additionalInfos=0' . ($autoplay ? '&autoStart=1' : '');
                $type = 'swf';
            }
            else if (preg_match('/twitvid\.com\/([a-zA-Z0-9_\-\?\=]+)/', $link, $matches)) {
                $href = 'http://www.twitvid.com/embed.php?guid=' . $matches[1] .
                        ($autoplay ? '&autoplay=1' : '&autoplay=0');
                $type = 'iframe';
            }
            else if (preg_match('/twitpic\.com\/(?!(?:place|photos|events)\/)([a-zA-Z0-9\?\=\-]+)/', $link, $matches)) {
                $href = 'http://twitpic.com/show/full/' . $matches[1];
                $type = 'image';
            }
            else if (preg_match('/(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?/', $link, $matches)) {
                $href = 'http://' . $matches[1] . '/p/' . $matches[2] . '/media/?size=l';
                $type = 'image';
            }
            else if (preg_match('/maps\.google\.com\/(\?ll=|maps\/?\?q=)(.*)/', $link, $matches)) {
                $href = 'http://maps.google.com/' . $matches[1] . '' . $matches[2] .
                        '&output=' . ((strpos($matches[2], 'layer=c')) ? 'svembed' : 'embed');
                $type = 'iframe';
            }

            // return
            if (!empty($href)) {
                return [
                    'href' => $href,
                    'type' => $type
                ];
            }
        }

        return $r;

    }

    public function EmbedLink($id = null, $autoplay = true) {

        // get data
        extract($this->embedData($id, $autoplay));

        // return
        if ($href) return $href;
        return null;

    }

    public function EmbedCode($w = 400, $h = 300, $lang = 'und', $id = null, $autoplay = true) {

        // generate embed code if there's a link
        if ($link = $this->owner->EmbedLink) {

            extract($this->embedData($id, $autoplay));

            if (empty($type)) {
                return false;
            } else {

                switch ($type) {

                    case 'iframe':
                        return '
                            <iframe width="' . $w . '"
                                    height="' . $h . '"
                                    data-src="' . $href . '"
                                    src=""
                                    frameborder="0"
                                    hspace="0"
                                    webkitallowfullscreen
                                    mozallowfullscreen
                                    allowfullscreen
                                    ' . ($id ? ' id="' . $id . '" ' : '') .
                                    ($this->is_ie() ? ' allowtransparency="true"' : '') . '></iframe>
                        ';
                        break;

                    case 'image':
                        return '<img ' . ($id ? ' id="' . $id . '" ' : '') . 'src="' . $href . '" alt="" />';
                        break;

                    case 'swf':
                        return '
                            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                                    width="100%"
                                    height="100%"
                                    ' . ($id ? ' id="' . $id . '" ' : '') . '>
                                <param name="wmode" value="transparent" />
                                <param name="allowfullscreen" value="true" />
                                <param name="allowscriptaccess" value="always" />
                                <param name="movie" value="' . $href . '" />
                                <embed src="' . $href . '"
                                       type="application/x-shockwave-flash"
                                       allowfullscreen="true"
                                       allowscriptaccess="always"
                                       width="100%"
                                       height="100%"
                                       wmode="transparent"></embed>
                            </object>
                        ';
                        break;

                }
            }

        } else {
            return false;
        }
    }
}
