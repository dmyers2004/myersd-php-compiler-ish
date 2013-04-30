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

define('APC_CLEAR_CACHE', true);
define('APC_COMPILE_RECURSIVELY', true);
define('APC_COMPILE_FILE','/Users/myersd/Desktop/');

define('APC_COMPILE_DIR','/Applications/MAMP/htdocs/expressionengine');

echo 'APC Directory Compiler '.gmdate('Y-m-d H:i:s').chr(10);

if (APC_CLEAR_CACHE){
	echo (apc_clear_cache() ? 'Cache Cleaned' : 'Cache Not Cleaned').chr(10);
	//var_dump(apc_cache_info());
}

echo 'Compile'.chr(10);
echo (apc_compile_dir(APC_COMPILE_DIR, APC_COMPILE_RECURSIVELY) ? 'Cache Created' : 'Cache Not Created').chr(10);

//var_dump(apc_cache_info());

echo apc_bin_dumpfile(null,null,APC_COMPILE_FILE.basename(APC_COMPILE_DIR).'.apc'); 

function apc_compile_dir($root, $recursively = true){
	$compiled = true;

	switch($recursively){
		case true:
			foreach(glob($root.'/*', GLOB_ONLYDIR) as $dir) {
				$compiled = $compiled && apc_compile_dir($dir, $recursively);
			}
		case false:
			foreach(glob($root.'/*.php') as $file) {
				$compiled = $compiled && apc_compile_file($file);
			}
		break;
	}

	return  $compiled;
}