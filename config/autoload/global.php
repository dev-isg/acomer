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


return array(
    // ...AGREGASTE PARA LA CONEXION GLOBAL
        'db' => array(
        'driver'         => 'Pdo',

            'username' => 'kevin',
        'password' => '123456',
        'dsn'            => 'mysql:dbname=bd_acomer;host=192.168.1.35',

        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
     'solr' => array (
                               'host' => '192.168.1.38',
                               'port' => '8983',
                               'folder' => '/solr' 
               ),
               'host' => array (
                               'static' => '',
                               'images' => 'http://192.168.1.38:8080/imagenes'
               ),
               'upload' => array(
                       'images' => APPLICATION_PATH . '/public/imagenes'
               ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'module_layouts' => array(
//      'Application' =>  array('layout/layout-portada'),
        'Application' => array(
          'index' => 'layout/layout-portada',
          'terminos'=>'layout/layout-portada',
          'nosotros'=>'layout/layout-portada',
          'solicita'=>'layout/layout-portada',
          'ver'=>'layout/layout-portada',
        ),
      'Local' => array(
          'index' => 'layout/layout-administrador',

        ),
      'Platos' => array(
          'index' => 'layout/layout-administrador',
          'verplatos'=>'layout/layout-portada'
        ),
      'Usuario' => array(
          'index' => 'layout/layout-portada',
          'comentarios'=>'layout/layout-administrador'
          
        ),
       'Restaurante' => array(
          'index' => 'layout/layout-administrador',
//          'comentarios'=>'layout/layout-administrador'
          
        ),

     ),

);
