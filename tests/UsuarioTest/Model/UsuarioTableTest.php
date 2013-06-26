<?php
namespace UsuarioTest\Model;

use Usuario\Model\UsuarioTable;
use Usuario\Model\Usuario;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class UsuarioTableTest extends PHPUnit_Framework_TestCase
{
    /*
     * para el metodo getUsuario
     */
    public function testgetUsuarioByItsId()
{
    $usuario = new Usuario();
    $usuario->exchangeArray(array('in_id'  =>132,
                                'va_nombre'=>'kevin',
                                'va_apellidos'=>'castillo',
                                'va_email'=>'kev@yopmail.com',
                                'va_contrasenia'=>'123456',
                                'en_estado'=>'activo',
                                'Ta_rol_in_id' =>2
                        ));
    
    $resultSet = new ResultSet();
    $resultSet->setArrayObjectPrototype(new Usuario());
    $resultSet->initialize(array($usuario));

    $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
    $mockTableGateway->expects($this->once())
                     ->method('select')
                     ->with(array('in_id' => 132))
                     ->will($this->returnValue($resultSet));

    $albumTable = new UsuarioTable($mockTableGateway);

    $this->assertSame($usuario, $albumTable->getUsuario(132));
}

    public function testFetchAllReturnsAllAlbums()
    {
        $resultSet = new ResultSet(); 
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                                           array('select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSet));

        $usuarioTable = new UsuarioTable($mockTableGateway);

        $this->assertSame($resultSet, $usuarioTable->fetch());
    }
    
    
        public function testFetchAllReturnsAllAlbums2()
    {
        $resultSet = new ResultSet();

        $mockSelect = $this->getMock('Zend\Db\Sql\Select', array('from', 'join','where'), array(), '', false);
               

        
          $mockSelect->expects($this->once())
            ->method('where')
           ->with(array('f.Ta_rol_in_id=b.in_id'))// ->with('ta_rol', 'ta_usuario.Ta_rol_in_id=ta_rol.in_id', array())
            ->will($this->returnValue($mockSelect));
        
        $mockSelect->expects($this->once())
            ->method('join')
           ->with(array('b' => 'ta_rol'), 'f.Ta_rol_in_id=b.in_id', array('va_nombre_rol'))// ->with('ta_rol', 'ta_usuario.Ta_rol_in_id=ta_rol.in_id', array())
            ->will($this->returnValue($mockSelect));
        
         $mockSelect->expects($this->once())
            ->method('from')
            ->with(array('f' => 'ta_usuario'))
            ->will($this->returnValue($mockSelect));
 
        $mockSql = $this->getMock('Zend\Db\Sql\Sql', array('select'), array(), '', false);
        $mockSql->expects($this->once())
            ->method('select')
            ->with()
            ->will($this->returnValue($mockSelect));
 
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                array('getAdapter', 'select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                ->method('getAdapter')
                ->with()
                ->will($this->returnValue($mockSql));
        
         $mockSelect->expects($this->once())
                ->method('select')
                ->with($mockSelect)
                ->will($this->returnValue($resultSet));
 
//        $mockTableGateway = $this->getMock('Zend\Db\Adapter\AdapterInterface',
//                array('getAdapter'), array(), '', false);
//        $mockTableGateway->expects($this->once())
//                ->with()
//                ->will($this->returnValue($mockTableGateway));
 

        $usuarioTable = new UsuarioTable($mockTableGateway);
        $this->assertNotSame($resultSet, $usuarioTable->fetchAll());
    }
    
    /*
     * metodo de prueba si funcionaaaaa
     */
        public function testFetchAllReturnsAllAlbums3()
    {                               
        $resultSet = new ResultSet();
        $mockSelect = $this->getMock('Zend\Db\Sql\Select', array('columns', 'join'), array(), '', false);
        $mockSelect->expects($this->once())
            ->method('columns')
            ->with(array('in_id', 'va_nombre', 'va_email',
              'va_contraseña','en_estado'))
            ->will($this->returnValue($mockSelect));
        $mockSelect->expects($this->once())
            ->method('join')
            ->with('ta_rol', 'ta_rol.in_id = ta_usuario.Ta_rol_in_id', array(), 'left')
            ->will($this->returnValue($mockSelect));
 
        $mockSql = $this->getMock('Zend\Db\Sql\Sql', array('select'), array(), '', false);
        $mockSql->expects($this->once())
            ->method('select')
            ->with()
            ->will($this->returnValue($mockSelect));
 
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                array('getSql', 'select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                ->method('getSql')
                ->with()
                ->will($this->returnValue($mockSql));
 
        $mockTableGateway->expects($this->once())
                ->method('select')
                ->with($mockSelect)
                ->will($this->returnValue($resultSet));
 
        $albumTable = new UsuarioTable($mockTableGateway);
        $this->assertSame($resultSet, $albumTable->fetchAll2());
    }
    
    
    
    
}
