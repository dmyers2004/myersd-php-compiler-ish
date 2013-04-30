#!/usr/bin/env php
<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set('apc.stat',0);

error_reporting(E_ALL ^ E_NOTICE);

$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];

$dir = dirname(__FILE__).'/';
$filename = basename(__FILE__);

date_default_timezone_set('America/New_York');

$show_caches = true;

/* compile this */
$apc_compile_dir = '/Applications/MAMP/htdocs/basicmvc-template';

/* desktop */
$apc_compile_file = '/Users/myersd/Desktop/';

echo 'APC Directory Compiler '.gmdate('Y-m-d H:i:s').chr(10);
echo 'Compile '.$apc_compile_dir.chr(10);

echo (apc_clear_cache() ? 'Cache Cleaned' : 'Cache Not Cleaned').chr(10);

if ($show_caches) {
	var_dump(apc_cache_info());
}

echo 'Compile'.chr(10);
echo (apc_compile_dir($apc_compile_dir, true) ? 'Cache Created' : 'Cache Not Created').chr(10);

if ($show_caches) {
	var_dump(apc_cache_info());
}

$bytes = apc_bin_dumpfile(null,null,$apc_compile_file.basename($apc_compile_dir).'.apc'); 

echo ($bytes) ? $bytes.'b' : 'Error';

function apc_compile_dir($root, $recursively = true){
	$compiled = true;

	if ($recursively) {
		foreach(glob($root.'/*', GLOB_ONLYDIR) as $dir) {
			$compiled = $compiled && apc_compile_dir($dir, $recursively);
		}
	}

	foreach(glob($root.'/*.php') as $file) {
		$compiled = $compiled && apc_compile_file($file);
	}

	return  $compiled;
}