(function($) {

	'use strict';

	$(function() {

		// auto complete
		$('#q').on('keyup', function(e) {

			// get the element
			var $el = $(this);

			// make a get request to the json endpoint
			$.get('/sseg-album-admin/albums?q=' + encodeURI($el.val()), function(data) {

				// set som local vars
				var $wrap = $('#options'), i;

				// purge the previous content
				$wrap.html('');

				// loop through the data and build the options
				for (i = 0; i < data.length; i++) {
					var item = data[i];
					$wrap.append(
						$(
							'<li>' +
								'<a data-id="' + item.value + '">' + item.text + '</a>' +
							'</li>'
						)
						.on('click', function() {
							var $link = $(this).find('a');
							$('#AlbumID').val($link.attr('data-id'));
							$('#q').val($link.text());
							$wrap.html('');
						})
					);
				}
			});
		});

		// inject the short code
		$('form').on('submit', function(e) {

			// dont submit
			e.preventDefault();

			// generate the token
			var token = '[album_embed,id="' + $('#AlbumID').val() + '"]';
			var replacement = '<div data-shortcode="' + token.replace(/"/g, '\'') + '">' + token + '</div>';

			alert(replacement);

			// insert the content
			tinyMCEPopup.execCommand('mceInsertContent', false, replacement);

			// Refocus in window
			if (tinyMCEPopup.isWindow) window.focus();

			// close the window etc
			tinyMCEPopup.editor.focus();
			tinyMCEPopup.close();
		});
	});
})(jQuery);
