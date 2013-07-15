<?php

error_reporting(E_ALL);
ini_set('session.cookie_httponly', 1);
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
    
// Setup autoloading
require 'init_autoloader.php';
// if (false) {
// 	$frontendOpts = array (
// 			'lifetime' => 60 * 30, //30 minutos
// 			'debug_header' => true,
// 			'regexps' => array (
// 					'^/$' => array (
// 							'cache' => true 
// 					),
// 					'^/es/$' => array (
// 							'cache' => true 
// 					)
// 			) 
// 	);
	
// 	$backendOpts = array (
// 			'cache_dir' => APPLICATION_PATH . '/data/cache',
// 			'hashed_directory_level' => 2 
// 	);
// // 	$cache = \Zend\Cache::factory ( 'Page', 'File', $frontendOpts, $backendOpts );
// 	$cache = \Zend\Cache\StorageFactory::adapterFactory('Page');
// 	$cacheName = substr ( str_replace ( '.', '_', $_SERVER ['SCRIPT_NAME'] ), 1 ) 
// 							. '_' . $lang 
// 							. '_' . md5 ( $_SERVER ['QUERY_STRING'] );
// 	$cache->start ( $cacheName );
// }

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
