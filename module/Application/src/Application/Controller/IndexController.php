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
use Application\Model\Usuario;
use Application\Model\Entity\Album;

use Platos\Model\Platos;
use Platos\Model\PlatosTable; 

class IndexController extends AbstractActionController
{
    protected $configTable;
    public $dbAdapter;
    public function indexAction()
    { 
       
        $view = new ViewModel();
        $this->layout('layout/layout-portada');
        $listarecomendacion=$this->getConfigTable()->cantComentxPlato();
        
        $view->setVariables(array('lista' => $listarecomendacion));
         return $view;
//                return new ViewModel(array(
//            'lista' => $listarecomendacion
//        ));
                
//       
    
    }
    
    public function getConfigTable()
{
    if (!$this->configTable) {
        $sm = $this->getServiceLocator();
        $this->configTable = $sm->get('Platos\Model\PlatosTable'); // <-- HERE!
    }
    return $this->configTable;
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
         $view = new ViewModel();
        $this->layout('layout/layout-dos');
        return $view;
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
        $u = new Album($adapter);
        $u->deleteAlbum($id);
         $valores=array
            ( 
                'url'=>$this->getRequest()->getBaseUrl(),
                'in_id'=>$id );
            return new ViewModel($valores);

    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/index/index');
       
    }
    
    public function actualizarusuarioAction()
    { 
        /* $id = (int) $this->params()->fromRoute('in_id', 0);
        if (!$id) {
            return $this->redirect()
           ->toUrl($this->getRequest()
           ->getBaseUrl().'/application/index/actualizarusuario');
        }
        try {
            $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $u = new Album($adapter);
             $u->obtenerUsuario($id); 
        }
        catch (\Exception $ex) {
            return $this->redirect()
           ->toUrl($this->getRequest()
           ->getBaseUrl().'/application/index/index');
        }
             */

         if($this->getRequest()->isPost())
        {
             $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $u = new Album($adapter);
             $data = $this->request->getPost();
             $u->updateAlbum($id,$data);
             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/index/actualizarusuario/1');
        }
        else
        {    
             $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $u = new Album($adapter);
             $datos=$u->obtenerUsuario($id); 
             $form=new Formularios("form");
               $dao = array  ('nombre'=>$datos['va_nombre'],
              'apellido'=>$datos['va_apellidos'],
              'pass'=>$datos['va_contrasenia'],
              'email'=>$datos['va_email'],
              'rol'=>$datos['Ta_rol_in_id']);
          //var_dump($dao);exit;
              //var_dump($values);exit;
              // $form->populate($values);
            // $va=$form->bind($datos);           
            // $form->setAttribute($values);
             $valores=array
            ( "titulo"=>"Actualizar Usuario",
                "form"=>$form,
                'url'=>$this->getRequest()->getBaseUrl(),
                'in_id'=>$id,
                 'ye' => $dao );
            return new ViewModel($valores);
        }
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
    
     public function agregarusuarioAction()
    { 
         if($this->getRequest()->isPost())
        {
             $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $u = new Album($adapter);
             $data = $this->request->getPost();
             $u->addAlbum($data);
             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/index/agregarusuario/1');
        }
        else
        {
             $form=new Formularios("form");
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $valores=array
            ( "titulo"=>"Registro de Usuario",
                "form"=>$form,
                'url'=>$this->getRequest()->getBaseUrl(),
                'in_id'=>$id );
            return new ViewModel($valores);
        }
    }
    
    
    

}
