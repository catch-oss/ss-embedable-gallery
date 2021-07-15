(function($) {

    'use strict';

    tinymce.create('tinymce.plugins.albumEmbed', {

        init : function(ed, url) {

            // Register commands
            ed.addCommand('mceInsertGalleryEmbed', function() {
                ed.windowManager.open({
                    title: 'Album Embed',
                    url: '/sseg-album-admin',
                    width: 900,
                    height: 600
                });
            });

            // add the button
            ed.addButton ('albumEmbed', {
                'title' : 'Gallery Embed',
                'image' : url + '/../img/icon.png',
                'cmd': 'mceInsertGalleryEmbed',
            });

            // replace the markup with the short code on save
            // ed.onSaveContent.add(function(ed, o) {
            ed.on('SaveContent', function(o) {
                var $content = $(o.content);
                $content.find('.album-embed').each(function() {
                    var $el = $(this);
                    var shortCode = $el.attr('data-shortcode').replace(/'/g, '"');
                    $el.replaceWith(shortCode);
                });

                // get the content string
                var content = $('<div />').append($content).html();

                // make sure we don't have a bung p tag
                if (content.replace(/^\s+|\s+$/g, '') == '<p>&nbsp;</p>') content = '';

                // set the content;
                o.content = content;
            });

            // replace the short code with markup on load
            // ed.onSetContent.add(function(ed) {

            ed.on('SetContent', function() {
                // parse the content
                var re = /\[album_embed,id="([^"]+)"\]/gi,
                    m = ed.getContent().match(re);

                if (m) {

                    // handle m
                    var mCount = m.length,
                        rCount = 0,
                        rMap = {},
                        i;

                    // find all the matched
                    for (i=0; i < m.length; i++) {

                        // extract the match data
                        var mCur = m[i],
                            m2 = /id="([^"]+)"/.exec(mCur),
                            id = m2[1];

                        // get the fully parsed piece of html
                        $.get('/sseg-album-admin/htmlfragment/' + id, function(mCur, id, data) {

                            // increment the request counter
                            rCount++;

                            // generate the token
                            var ii,
                                token = '[album_embed,id="' + id + '"]';

                            // generate the replacement html
                            data =  '<div class="album-embed" data-shortcode="' + token.replace(/"/g, '\'') + '">' +
                                        data +
                                    '</div>';

                            // store the replacement data
                            rMap[mCur] = data;

                            // do the replacement once we get back all of the requests
                            if (rCount == mCount) {
                                var cont = ed.getContent();
                                for (ii=0; ii < m.length; ii++) {
                                    var key = m[ii];
                                    cont = cont.replace(key, rMap[key]);
                                }
                                ed.setContent(cont);
                            }

                        }.bind(null, mCur, id));
                    }
                }
            });
        },

        getInfo : function() {
            return {
                longname  : 'Album Embed',
                author    : 'azt3k',
                authorurl : 'http://www.catch.co.nz',
                infourl   : 'http://gl.catch.co.nz/catch/ss-embedable-gallery',
                version   : '0.1'
            };
        }
    });

    tinymce.PluginManager.add('albumEmbed', tinymce.plugins.albumEmbed);
})(jQuery);
