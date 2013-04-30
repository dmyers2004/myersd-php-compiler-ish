#!/usr/bin/env php
<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');

error_reporting(E_ALL ^ E_NOTICE);

$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];

$dir = dirname(__FILE__).'/';
$filename = basename(__FILE__);

date_default_timezone_set('America/New_York');

// apc_compile_dir.php

function apc_compile_dir($root, $recursively = true){
    $compiled   = true;
    switch($recursively){
        case    true:
            foreach(glob($root.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $dir) {
                $compiled   = $compiled && apc_compile_dir($dir, $recursively);
            }
        case    false:
            foreach(glob($root.DIRECTORY_SEPARATOR.'*.php') as $file) {
								//clean($file);
								//clean2($file);
                $compiled   = $compiled && apc_compile_file($file);
            }
            break;
    }
    return  $compiled;
}

function clean($file) {
	$fileStr = file_get_contents($file);
	$newStr  = '';

	$commentTokens = array(T_COMMENT);

	if (defined('T_DOC_COMMENT')) {
			$commentTokens[] = T_DOC_COMMENT; // PHP 5
	}
	if (defined('T_ML_COMMENT')) {
			$commentTokens[] = T_ML_COMMENT;  // PHP 4
	}

	$tokens = token_get_all($fileStr);

	foreach ($tokens as $token) {    
			if (is_array($token)) {
				if (in_array($token[0], $commentTokens)) {
						continue;
				}
				$token = $token[1];
			}

			if ($token != chr(10)) {
				$newStr .= $token;
			}
	}

	//echo $newStr;
	file_put_contents($file,$newStr);
}

function clean2($file) {
	$source = file_get_contents($file);
	$newSource = '';
	foreach (token_get_all($source) as $i => $token) {
			if (!is_array($token)) {
					$newSource .= $token;
			}

			if ($token[0] == T_WHITESPACE) {
					if (   isset($tokens[$i - 1])      && isset($tokens[$i + 1])
							&& is_array($tokens[$i - 1])   && is_array($tokens[$i + 1])
							&& isLabel($tokens[$i - 1][1]) && isLabel($tokens[$i + 1][1])
					) {
							$newSource .= ' ';
					}
			} else {
					$newSource .= $token[1];
			}
	}
	file_put_contents($file,$newSource);
}

function isLabel($str) {
    return preg_match('~^[a-zA-Z0-9_\x7f-\xff]+$~', $str);
}

echo chr(10);

if (function_exists('apc_compile_file')){

    define('APC_CLEAR_CACHE',           true);
    define('APC_COMPILE_RECURSIVELY',   true);
    define('APC_COMPILE_DIR','/Applications/MAMP/htdocs/expressionengine');

    echo 'APC Directory Compiler '.gmdate('Y-m-d H:i:s').chr(10);
    echo chr(10).'-------------------------'.chr(10);

    if (APC_CLEAR_CACHE){
      echo    (apc_clear_cache() ? 'Cache Cleaned' : 'Cache Not Cleaned').chr(10);
      var_dump(apc_cache_info());
      echo    chr(10).'-------------------------'.chr(10);
    }

    echo 'Runtime Errors'.chr(10);
    echo (apc_compile_dir(APC_COMPILE_DIR, APC_COMPILE_RECURSIVELY) ? 'Cache Created' : 'Cache Not Created').chr(10);
    echo chr(10).'-------------------------'.chr(10);
    
		$apcinfo = new APCIterator('file', NULL, APC_ITER_FILENAME);
		$files = array();
		foreach ($apcinfo as $val) {
			$files[] = $val['filename'];
		}
    
    var_dump(apc_cache_info());
    
    echo apc_bin_dumpfile(null,null,"/Users/myersd/Desktop/file.data"); 

} else {
	echo 'APC is not present, nothing to do.'.chr(10);
}
