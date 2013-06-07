<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Usuario\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;

class IndexController extends AbstractActionController
{
  protected $usuarioTable;
    public function indexAction()
    {
        //return array();
        //retorna la vista nueva forma oo
        //$this->view->data='hola mundo';
        return new ViewModel(array(
            'usuarios' => $this->getUsuarioTable()->fetchAll(),
            'data'=>'Hola'    
        ));
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }

    public function listarAction(){
        //echo 'holla mundddo';exit;
        $request = $this->getRequest();
         if ($request->isPost()) {
       //$datos=$request->post()->toArray();
       $datos=$this->params()->fromPost('texto');
      // $tipo=$this->params()->fromPost('listado');

       //$this->redirect()->toUrl('http://zf2.isg.com:81/usuario/index');

      //$val= $this->getUsuarioTable()->buscarUsuario($datos,$tipo);

      var_dump($datos);exit;
          // var_dump($datos);exit;
        //print_r($_REQUEST);
    }
    //$texto=5;
        //$texto = $form->getValue('texto');
        //$this->params()->fromPost('texto'); 
        return array('texto'=>$texto);
      //echo  $this->_request->getParam('texto');exit;
    }

     public function getUsuarioTable()
    {
        if (!$this->usuarioTable) {
            $sm = $this->getServiceLocator();
            $this->usuarioTable = $sm->get('Usuario\Model\UsuarioTable');
        }
        return $this->usuarioTable;
    }




}
