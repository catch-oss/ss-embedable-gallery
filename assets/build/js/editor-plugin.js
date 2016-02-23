(function() {
    tinymce.create('tinymce.plugins.album_embed', {

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
            ed.addButton ('album_embed', {
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

    tinymce.PluginManager.add('album_embed', tinymce.plugins.album_embed);
})();
