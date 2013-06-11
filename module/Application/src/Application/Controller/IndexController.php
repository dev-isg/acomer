<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Application\Form\Formularios;
use Application\Model\Entity\Procesa;
use Application\Model\Entity\Album;


class IndexController extends AbstractActionController
{
    
    public $dbAdapter;
    public function indexAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $u = new Album($adapter);
        $array = array('hola'=>'LISTADO DE USUARIOS',
                        'yea'=>$u->fetchAll());
       return new ViewModel($array);
    }
    
    public function rolesAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $u = new Album($adapter);
        $s=$u->rolAll($adapter);
        $array = array('hola'=>'desde sql',
                        'yea'=>$u->rolAll($adapter)); 
       return new ViewModel($array);
    }
     public function verAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        
        $id =(int)$this->params()->fromRoute('in_id',0); 
       
        $u = new Album($adapter);
        $array = array('hola'=>'desde verrr',
                        'yea'=>$u->getAlbum($id,$adapter));
       return new ViewModel($array);
    }
    public function addAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $id =(int)$this->params()->fromRoute('in_id',0);
        //var_dump($id);exit;
        $u = new Album($adapter);
        $array = array('artist'=>'sandra' , 
                        'title'=>'ss');
        $u->deleteAlbum($id);

       return new ViewModel($array);
    }
    public function delAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $id =(int)$this->params()->fromRoute('in_id',0);
        //var_dump($id);exit;
        $u = new Album($adapter);
        $u->deleteAlbum($id);

       //return new ViewModel($array);
    }
    public function recibeAction()
    {
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $data = $this->request->getPost();
        $u = new Album($adapter);
        $array = array('va_nombre'=>$data['nombre'],
                    'va_apellidos'=>$data['apellido'],
                        'va_email'=>$data['email'],
                  'va_contrasenia'=>$data['pass'],
                    'Ta_rol_in_id'=>$data['rol']);
      $u->addAlbum($array);
        
        //return new ViewModel(array('mal'=>$array));
    }

    public function joinAction()
    {  
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from(array('f' => 'ta_usuario')) 
            ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id = b.in_id')
           ->where(array('nombre = "peru"','id = 3'));
            $selectString = $sql->getSqlStringForSqlObject($select);
            //echo $selectString;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return new ViewModel(array('hola'=>'desde sql','yea'=>$results));
    }
    
     public function forAction()
    { 
        $form=new Formularios("form");
        return new ViewModel(array("titulo"=>"Formularios en ZF2","form"=>$form,'url'=>$this->getRequest()->getBaseUrl()));
  
    }
}
