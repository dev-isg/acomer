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
        $filtrar = $this->params()->fromPost('submit'); 
        $datos = $this->params()->fromPost('texto');
        $estado = $this->params()->fromPost('estado');
         if (isset($filtrar)) {
            $comentarios = $this->getComentariosTable()->buscarComentario($datos,$estado);
        }
        else {
            $comentarios = $this->getComentariosTable()->fetchAll();
        }
        return array(
          'comentarios' => $comentarios
        );
    }
    
    public function getComentariosTable() {
        if (!$this->comentariosTable) {
            $s = $this->getServiceLocator();
            $this->comentariosTable = $s->get('Usuario\Model\ComentariosTable');
        }
        return $this->comentariosTable;
    }
    
    public function cambiaestadoAction() {
              $id = $this->params()->fromQuery('id');
              $estado = $this->params()->fromQuery('estado');
              $this->getComentariosTable()->estadoComentario((int) $id, $estado);
              $this->redirect()->toUrl('/usuario/comentarios/index');
    }
    
     public function eliminarcomentarioAction() {
        $id = $this->params()->fromPost('id');
        $this->getUsuarioTable()->deleteComentario((int) $id);
        $this->redirect()->toUrl('/usuario/comentarios/index');
    }

}
