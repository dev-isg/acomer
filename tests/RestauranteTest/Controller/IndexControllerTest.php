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
    
//    public function testIndexActionCanBeAccessed()
//{
//            $localTableMock = $this->getMockBuilder('Restaurante\Model\RestauranteTable')
//                            ->disableOriginalConstructor()
//                            ->getMock();
//
//    $localTableMock->expects($this->once())
//                    ->method('listar')
//                    ->will($this->returnValue(array()));
//
//    $serviceManager = $this->getApplicationServiceLocator();
//    $serviceManager->setAllowOverride(true);
//    $serviceManager->setService('Local\Model\LocalTable', $localTableMock);
//    
//    $this->dispatch('/local');
//    $this->assertResponseStatusCode(200);
//
//    $this->assertModuleName('Local');
//    $this->assertControllerName('Local\Controller\Index');
//    $this->assertControllerClass('IndexController');
//    $this->assertMatchedRouteName('local');
//}


public function testAddActionRedirectsAfterValidPost()
{
    $restauranteTableMock = $this->getMockBuilder('Restaurante\Model\RestauranteTable')
                            ->disableOriginalConstructor()
                            ->getMock();

    $restauranteTableMock->expects($this->once())
                    ->method('guardarRestaurante')
                    ->will($this->returnValue(null));

    $serviceManager = $this->getApplicationServiceLocator();
    $serviceManager->setAllowOverride(true);
    $serviceManager->setService('Restaurante\Model\RestauranteTable', $restauranteTableMock);

    $postData = array('nombre' => 'Led Zeppelin III', 'direccion' => 'Led Zeppelin');
    $this->dispatch('/restaurante/agregarrestaurante', 'POST', $postData);
    $this->assertResponseStatusCode(302);

    $this->assertRedirectTo('/restaurante');
}


}
