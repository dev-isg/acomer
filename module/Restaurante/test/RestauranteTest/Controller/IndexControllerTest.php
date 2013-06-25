<?php

namespace RestauranteTest\Controller;

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
            $albumTableMock = $this->getMockBuilder('Restaurante\Model\RestauranteTable')
                            ->disableOriginalConstructor()
                            ->getMock();

    $albumTableMock->expects($this->once())
                    ->method('fetchAll')
                    ->will($this->returnValue(array()));

    $serviceManager = $this->getApplicationServiceLocator();
    $serviceManager->setAllowOverride(true);
    $serviceManager->setService('Restaurante\Model\RestauranteTable', $albumTableMock);
    
    $this->dispatch('/restaurante');
    $this->assertResponseStatusCode(200);

    $this->assertModuleName('Restaurante');
    $this->assertControllerName('Restaurante\Controller\Index');
    $this->assertControllerClass('IndexController');
    $this->assertMatchedRouteName('restaurante');
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
