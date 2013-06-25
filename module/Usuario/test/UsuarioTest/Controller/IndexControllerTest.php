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


//public function testAddActionRedirectsAfterValidPost()
//{
//    $albumTableMock = $this->getMockBuilder('Usuario\Model\UsuarioTable')
//                            ->disableOriginalConstructor()
//                            ->getMock();
//
//    $albumTableMock->expects($this->once())
//                    ->method('guardarUsuario')
//                    ->will($this->returnValue(null));
//
//    $serviceManager = $this->getApplicationServiceLocator();
//    $serviceManager->setAllowOverride(true);
//    $serviceManager->setService('Usuario\Model\UsuarioTable', $albumTableMock);
//
//    $postData = array('nombre' => 'Led Zeppelin III', 'direccion' => 'Led Zeppelin');
//    $this->dispatch('/usuario/agregarusuario', 'POST', $postData);
//    $this->assertResponseStatusCode(302);
//
//    $this->assertRedirectTo('/usuario');
//}


}
