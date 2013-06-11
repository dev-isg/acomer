<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\Usuario;
use Application\Model\Entity\Procesa;

class FormularioController extends AbstractActionController
{
    public function indexAction()
    {
        $form=new Usuario("form");
        
        return new ViewModel(array("titulo"=>"Formularios en Ingreso de Usuarios","form"=>$form,'url'=>$this->getRequest()->getBaseUrl()));
    }
    public function formularioAction()
    {
        $form=new Formularios("form");
        $form->get("lenguaje")->setValueOptions(array('0'=>'Ingl�s','1'=>'Espa�ol'));
        $form->get("genero")->setValueOptions(array('f'=>'Femenino','m'=>'Masculino','n'=>'no definido'));
        $form->get("oculto")->setAttribute("value","87");
        $form->get("preferencias")->setValueOptions(array('m'=>'M�sica','d'=>'Deporte','o'=>'Ocio'));
        return new ViewModel(array("titulo"=>"Formularios en ZF2","form"=>$form,'url'=>$this->getRequest()->getBaseUrl()));
    }
    public function recibeAction()
    {
        $data = $this->request->getPost();
        $procesa=new Procesa($data);
        $datos=$procesa->getData();
        return new ViewModel(array('datos'=>$datos));
    }

}
