(function($) {

    'use strict';

    tinymce.create('tinymce.plugins.albumEmbed', {

        init : function(ed, url) {

            // Register commands
            ed.addCommand('mceInsertGalleryEmbed', function() {
                ed.windowManager.open({
                    title: 'Gallery Embed',
                    url: '/sseg-album-admin'
                });
            });

            // add the button
            ed.addButton ('album_embed', {
                'title' : 'Gallery Embed',
                'image' : url + '/../img/icon.png',
                'cmd': 'mceInsertGalleryEmbed',
            });

            // replace the markup with the short code on save
            ed.onSaveContent.add(function(ed, o) {
                var $content = $(o.content);
                $content.find('.album-embed').each(function() {
                    var $el = $(this);
                    var shortCode = $el.attr('data-shortcode').replace(/'/g, '"');
                    $el.replaceWith(shortCode);
                });
                o.content = $('<div />').append($content).html();
            });

            // replace the short code with markup on load
            ed.onSetContent.add(function(ed) {

                // parse the content
                var re = /\[album_embed,id="([^"]+)"\]/gi,
                    m = ed.getContent().match(re),
                    i;

                if (m) {

                    // find all the matched
                    for (i=0; i < m.length; i++) {

                        // extract the match data
                        var mCur = m[i],
                            m2 = /id="([^"]+)"/.exec(mCur),
                            id = m2[1];

                        // get the fully parsed piece of html
                        $.get('/sseg-album-admin/htmlfragment/' + id, function(data) {

                            // ensure we have a common anscestor or it's all bad:
                            data = '<div>' + data + '</div>';

                            // generate the token / html
                            var token = '[album_embed,id="' + id + '"]';
                            var $html = $(data).attr('data-shortcode', token.replace(/"/g, '\''))
                                               .addClass('album-embed');

                            // replace
                            ed.setContent(ed.getContent().replace(mCur, $('<div />').append($html).html()));
                        });
                    }
                }
            });
        },

        getInfo : function() {
            return {
                longname  : 'Gallery Embed',
                author    : 'Me',
                authorurl : 'http://www.catch.co.nz',
                infourl   : 'http://gl.catch.co.nz/catch/ss-embedable-gallery',
                version   : '0.1'
            };
        }
    });

    tinymce.PluginManager.add('albumEmbed', tinymce.plugins.albumEmbed);
})(jQuery);
