<?php

// Define path constant
$path = str_replace('\\', '/', __DIR__);
$path_fragments = explode('/', $path);
$dir_name = $path_fragments[count($path_fragments) - 1];
define('SS_EMBEDABLE_GALLERY_DIR', $dir_name);
define('SS_EMBEDABLE_GALLERY_PATH', __DIR__);

// require
Requirements::javascript($dir_name . '/assets/build/js/lib.js');
