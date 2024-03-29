<?php

// Define path constant
$path = str_replace('\\', '/', __DIR__);
$path_fragments = explode('/', $path);
$dir_name = $path_fragments[count($path_fragments) - 1];
define('SS_EMBEDABLE_GALLERY_DIR', $dir_name);
define('SS_EMBEDABLE_GALLERY_PATH', __DIR__);

// page Extension
Page::add_extension('SSEmedableGalleryPageExtension');

// require
Requirements::javascript(SS_EMBEDABLE_GALLERY_DIR . '/assets/build/js/lib.js');

// add the embed functionality
ShortcodeParser::get('default')->register(
	'album_embed',
	['SSEmedableGalleryPageExtension', 'AlbumEmbedParser']
);
HtmlEditorConfig::get('cms')->enablePlugins(array(
	'albumEmbed' => '../../../' . SS_EMBEDABLE_GALLERY_DIR . '/assets/build/js/editor-plugin.js'
));
HtmlEditorConfig::get('cms')->addButtonsToLine(2, 'albumEmbed');

$styleSheets = HtmlEditorConfig::get('cms')->getOption('content_css');
HtmlEditorConfig::get('cms')->setOption(
	'content_css',
	($styleSheets ?: '/themes/' . SSViewer::current_theme() . '/css/editor.css') . ',' .
	SS_EMBEDABLE_GALLERY_DIR . '/assets/build/css/main.css'
);
