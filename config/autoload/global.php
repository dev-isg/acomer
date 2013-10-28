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
        'dsn' => 'mysql:dbname=listadelsabor3;host=192.168.1.50',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),
    'solr' => array(
        'host' => '192.168.1.36',
        'port' => '8983',
        'folder' => '/solr'
    ),
    'facebook' => array(
        'appId' => '750432834973806',
        'secret' => '2e07ad7ea83185f20da1ca546fee0720'
    ),
'host' => array(
    'base' => 'http://192.168.1.36:8080',
    'static' => 'http://192.168.1.36:8080',
    'images' => 'http://192.168.1.36:8080/imagenes',
    'img'=>'http://192.168.1.36:8080/img',
    'ruta' => 'http://192.168.1.36:8080',
    'version'=>1,
),
// 'host' => array(
//       'base' => 'http://dev.listadelsabor.com',
//        'static' => 'http://dev.listadelsabor.com',
//        'images' => 'http://192.168.1.36:8080/imagenes',
//        'img'=>'http://192.168.1.36:8080/img',
//       'ruta' => 'http://dev.listadelsabor.com',
//        'version'=>1,
//    ),
//    
   
    'upload' => array(
        'images' => APPLICATION_PATH . '/public/imagenes'
    ),
    
    'data' => array(
        'busqueda' => APPLICATION_PATH . '/data/busqueda'
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
