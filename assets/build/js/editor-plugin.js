(function() {
    tinymce.create('tinymce.plugins.gallery_embed', {

        init : function(ed, url) {

            var self = this;

            // Register commands
            ed.addCommand('mceInsertGalleryEmbed', function() {
                ed.windowManager.open({
                    title: 'Gallery Embed',
                    url: '/sseg-album-admin'
                });
            });

            // add the button
            ed.addButton ('gallery_embed', {
                'title' : 'Gallery Embed',
                'image' : url + '/../img/icon.png',
                'cmd': 'mceInsertGalleryEmbed',
            });
        },

        getInfo : function() {
            return {
                longname  : 'Gallery Embed',
                author    : 'Me',
                authorurl : 'http://www.catch.co.nz',
                infourl   : 'http://gl.catch.co.nz/catch/ss-embedable-gallery',
                version   : "0.1"
            };
        }
    });

    tinymce.PluginManager.add('gallery_embed', tinymce.plugins.gallery_embed);
})();
