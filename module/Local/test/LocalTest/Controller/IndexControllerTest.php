<?php

namespace LocalTest\Controller;

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
            $localTableMock = $this->getMockBuilder('Local\Model\LocalTable')
                            ->disableOriginalConstructor()
                            ->getMock();

    $localTableMock->expects($this->once())
                    ->method('listar')
                    ->will($this->returnValue(array()));

    $serviceManager = $this->getApplicationServiceLocator();
    $serviceManager->setAllowOverride(true);
    $serviceManager->setService('Local\Model\LocalTable', $localTableMock);
    
    $this->dispatch('/local');
    $this->assertResponseStatusCode(200);

    $this->assertModuleName('Local');
    $this->assertControllerName('Local\Controller\Index');
    $this->assertControllerClass('IndexController');
    $this->assertMatchedRouteName('local');
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
