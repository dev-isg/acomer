<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not * sensitive to security.
 * * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    // ...AGREGASTE PARA LA CONEXION GLOBAL
    'db' => array(
        'driver' => 'Pdo',
        'username' => 'kevin',
        'password' => '123456',
        'dsn' => 'mysql:dbname=liston;host=192.168.1.50',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),
    'solr' => array(
        'host' => '192.168.1.45',
        'port' => '8983',
        'folder' => '/solr'
    ),
   
    'host' => array(
        'base' => 'http://local.listadelsabor',
        'static' => 'http://local.listadelsabor',
        'images' => 'http://local.listadelsabor/imagenes',
        'img'=>'http://local.listadelsabor/img',
        'ruta' => 'http://local.listadelsabor',
        'version'=>1,
    ),

    'upload' => array(
        'images' => APPLICATION_PATH . '/public/imagenes'
    ),
    
    'verifica' => array(
        'img' => APPLICATION_PATH . '/public/img'
    ),

    'mail' => array(
        'transport' => array(
            'options' => array(
                'host'              => 'smtp.innovationssystems.com',
                'connection_class'  => 'login',
                'connection_config' => array(
                    'username' => 'listadelsabor@innovationssystems.com',
                    'password' => 'L1st@d3ls@b0r',
                    // 'ssl' => 'tls'
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
        ),
//        'aliases' => array(
//                'translator' => 'MvcTranslator',
//            ),
    ),
    'module_layouts' => array(
//         'Application' => 'layout/layout-portada',
         
        'Application' => array(
            'default'=> 'layout/layout-portada2',
            'index' => 'layout/layout-portada2',
            'terminos' => 'layout/layout-portada2',
            'contactenos' => 'layout/layout-portada2',
            'nosotros' => 'layout/layout-portada2',
            'solicita' => 'layout/layout-portada2',
            'ver' => 'layout/layout-portada2',
            'detalleubicacion' => 'layout/layout-portada2'
        ),
       'Local' => array(
            'index' => 'layout/layout-administrador'
        )
        ,
        'Platos' => array(
            'index' => 'layout/layout-administrador',
            'verplatos' => 'layout/layout-portada2'
        ),
       'Usuario' => array(
            'index' => 'layout/layout-administrador',
            'comentarios' => 'layout/layout-administrador'
        )
        ,
        'Restaurante' => array(
            'default'=>'layout/layout-administrador',
           'index' => 'layout/layout-administrador'
               )
   )
    
)
;
