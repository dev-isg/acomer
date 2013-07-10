<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array (
		// ...AGREGASTE PARA LA CONEXION GLOBAL
		'db' => array (
				'driver' => 'Pdo',
				'username' => 'kevin',
				'password' => '123456',
				'dsn' => 'mysql:dbname=bd_acomer;host=192.168.1.35',
				
				'driver_options' => array (
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'' 
				) 
		),
		'service_manager' => array (
				'factories' => array (
						'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory' 
				) 
		),
		'solr' => array (
				'host' => '192.168.1.38',
				'port' => '8983',
				'folder' => '/solr/' 
		),
		'server' => array (
				'static' => '',
				'dynamic' => '' 
		) 
);
