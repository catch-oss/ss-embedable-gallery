<?php

use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\View\Requirements;
use CatchDesign\EmbedableGallery\Extensions\SSEmedableGalleryPageExtension;



$module = ModuleLoader::inst()->getManifest()->getModule('catchdesign/embedable-gallery');

// Define path constant
$path = str_replace('\\', '/', __DIR__);
$path_fragments = explode('/', $path);
$dir_name = $path_fragments[count($path_fragments) - 1];
define('SS_EMBEDABLE_GALLERY_DIR', $dir_name);
define('SS_EMBEDABLE_GALLERY_PATH', __DIR__);
$cmsConfig = HTMLEditorConfig::get('cms');

// page Extension
\Page::add_extension(SSEmedableGalleryPageExtension::class);


// Requirements::css('silverstripe-example-module/styles/admin.css');
// + Requirements::css('example-user/silverstripe-example-module: styles/admin.css');


// require
Requirements::javascript( $module->getResource('assets/build/js/lib.js') );

// add the embed functionality
ShortcodeParser::get('default')->register(
	'album_embed',
	['SSEmedableGalleryPageExtension', 'AlbumEmbedParser']
);
$cmsConfig->enablePlugins(array(
	'albumEmbed' => $module->getResource('assets/build/js/editor-plugin.js')
));
$cmsConfig->addButtonsToLine(2, 'albumEmbed');

$styleSheets = $cmsConfig->getOption('content_css');
$cmsConfig->setContentCSS([
    $module->getResource('assets/build/css/main.css')
]);

// HtmlEditorConfig::get('cms')->setOption(
// 	'content_css',
// 	($styleSheets ?: '/themes/' . SSViewer::current_theme() . '/css/editor.css') . ',' .
// 	SS_EMBEDABLE_GALLERY_DIR . '/assets/build/css/main.css'
// );
