<?php 
//namespace ApplicationController;
namespace UsuarioTest\Controller;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

use ApplicationControllerIndexController;
use ZendHttpRequest;
use ZendHttpResponse;
use ZendMvcMvcEvent;
use ZendMvcRouterRouteMatch;
use PHPUnit_Framework_TestCase;

class ClientesControllerTest extends  PHPUnit_Framework_TestCase{
        protected $traceError = true;
    public function setUp()
    {
        $this->setApplicationConfig(
                //'C:/xampp/htdocs/acomer/config/application.config.php'
            include '/xampp/htdocs/acomer/config/application.config.php'//'/var/www/acomer/config/application.config.php'
        );
        parent::setUp();
    }   
    
    public function testclientesAction(){
        $this->routeMatch->setParam('action','clientes');
        
        $result=$this->controller->dispatch($this->request);
        
        $response=$this->controller->getResponse();
        $this->assertEquals(200,$response->getStatusCode());
        
        $this->assertInstanceOf('Zend\View\ViewModel',$result);
        
        $vars=$result->getVarualbes();
        
        
    }
    
    
    
}


?>
