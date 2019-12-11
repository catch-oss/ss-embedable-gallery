<?php

namespace CatchDesign\EmbedableGallery\Extensions;

use SilverStripe\View\Requirements;
use SilverStripe\Core\Extension;

class SSEmbedableGalleryLeftAndMainExtension extends Extension {

	public function onAfterInit() {
		Requirements::javascript(SS_EMBEDABLE_GALLERY_DIR . '/assets/build/js/lib.js');
		Requirements::javascript(SS_EMBEDABLE_GALLERY_DIR . '/assets/build/js/wysiwyg.js');
	}
}