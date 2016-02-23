(function($) {
	$(function() {

		// auto complete
		$('#q').on('keyup', function(e) {
			$el = $(this);
			$.get('/sseg-album-admin/albums?q=' + encodeURI($el.val()), function(data) {
				var $wrap = $('#options');
				$wrap.html('');
				data.forEach(function(i) {
					$wrap.append(
						$(
							'<li>' +
								'<a data-id="' + i.value + '">' + i.text + '</a>' +
							'</li>'
						)
						.on('click', function() {
							$('#AlbumID').val($(this).attr('data-id'));
							$wrap.html('');
						})
					);
				});
			});
		});

		// inject the short code
		$('form').on('submit', function(e) {

			// dont submit
			e.preventDefault();

			// generate the token
			var token = '[social_embed,id="' + $('#AlbumID').val() + '"]';

			// insert the content
			tinyMCEPopup.execCommand('mceInsertContent', false, token);

			// Refocus in window
			if (tinyMCEPopup.isWindow) window.focus();

			// close the window etc
			tinyMCEPopup.editor.focus();
			tinyMCEPopup.close();
		});
	});
})(jQuery);
