<?php

namespace Usuario\Controller;



use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Usuario\Model\Cometarios;          // <-- Add this import
use Usuario\Form\UsuarioForm;       // <-- Add this import
use Usuario\Model\CometariosTable; 
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

class ComentariosController extends AbstractActionController
{
  protected $comentariosTable;
  public $dbAdapter;
    public function indexAction()
    {
   
        return new ViewModel(array(
            'comentarios' => $this->getComentariosTable()->come(),
        ));
    }
    
    public function getComentariosTable() {
        if (!$this->comentariosTable) {
            $s = $this->getServiceLocator();
            $this->comentariosTable = $s->get('Usuario\Model\ComentariosTable');
        }
        return $this->comentariosTable;
    }

}
