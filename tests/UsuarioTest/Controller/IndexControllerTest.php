<?php

namespace UsuarioTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    public function setUp()
    {
        $this->setApplicationConfig(
                //'C:/xampp/htdocs/acomer/config/application.config.php'
            include '/xampp/htdocs/acomer/config/application.config.php'//'/var/www/acomer/config/application.config.php'
        );
        parent::setUp();
    }
    
    public function testIndexActionCanBeAccessed()
{
            $albumTableMock = $this->getMockBuilder('Usuario\Model\UsuarioTable')
                            ->disableOriginalConstructor()
                            ->getMock();

    $albumTableMock->expects($this->once())
                    ->method('fetchAll')
                    ->will($this->returnValue(array()));

    $serviceManager = $this->getApplicationServiceLocator();
    $serviceManager->setAllowOverride(true);
    $serviceManager->setService('Usuario\Model\UsuarioTable', $albumTableMock);
    
    $this->dispatch('/usuario');
    $this->assertResponseStatusCode(200);

    $this->assertModuleName('Usuario');
    $this->assertControllerName('Usuario\Controller\Index');
    $this->assertControllerClass('IndexController');
    $this->assertMatchedRouteName('usuario');
}


public function testAddActionRedirectsAfterValidPost()
{
    $usuarioTableMock = $this->getMockBuilder('Usuario\Model\UsuarioTable')
                            ->disableOriginalConstructor()
                            ->getMock();
    
    $usuarioTableMock->expects($this->once())
                    ->method('estadoUsuario')
                    ->will($this->returnValue(null));

    $serviceManager = $this->getApplicationServiceLocator();

    $serviceManager->setAllowOverride(true);
    $serviceManager->setService('Usuario\Model\UsuarioTable', $usuarioTableMock);
//           'va_nombre'     => $usuario->va_nombre,
//           'va_apellidos'  => $usuario->va_apellidos,
//           'va_email'      => $usuario->va_email,
//           'va_contrasenia'=> $usuario->va_contrasenia,
//           'Ta_rol_in_id'  => $usuario->Ta_rol_in_id,  
//     
//    $postData = array( 'va_nombre'=> 'yosmel' ,'va_apellidos'=> 'hello' ,
//        'va_email'=>'yos@yopmail.com','va_contrasenia'=> '123456',
//        'Ta_rol_in_id'=> '1');

        $postData = array( 'id'=> 1 ,'estado'=> 'activo');
        
    $this->dispatch('/usuario/index/cambiaestado','GET',$postData);
    

    $this->assertResponseStatusCode(302);

    $this->assertRedirectTo('/usuario');
}

//"in_id"=>135, "va_nombre"=> "yosmel" ,"va_apellidos"=> "hello" ,
//        "va_email"=>"yos@yopmail.com","va_contrasenia"=> "123456", "va_contrasenia2"=>123456,
//        "Ta_rol_in_id"=> 1, "submit"=> "INSERTAR"
}
