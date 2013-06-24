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
        $puntaje = $this->params()->fromPost('puntaje');
         if (isset($filtrar)) {
            $comentarios = $this->getComentariosTable()->buscarComentario($datos,$estado,$puntaje);
        }
        else {
            $comentarios = $this->getComentariosTable()->fetchAll();
        }
        return array(
          'comentarios' => $comentarios,
            'puntaje' =>$this-> puntaje()
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
    
    public function comentariocorreoAction() {
              $id = $this->params()->fromQuery('id');
              $estado = $this->params()->fromQuery('estado');
              $this->getComentariosTable()->estadoComentario((int) $id, $estado);
              $this->redirect()->toUrl('/usuario/comentarios/index');
    }
    
     public function eliminarcomentarioAction() {
        $id = $this->params()->fromPost('id');
        $this->getComentariosTable()->deleteComentario((int) $id);
        $this->redirect()->toUrl('/usuario/comentarios/index');
    }

      public function puntaje()
    {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from('ta_puntaje');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
            
     }
        public function comentariosexcelAction(){
          if (empty($_GET["estado"])and empty($_GET["puntaje"])and empty($_GET["texto"]) )
                {
               $comentarios = $this->getComentariosTable()->fetchAll();
                }
          else {
                $datos=$_GET["texto"];
                $estado = $_GET["estado"];
                $puntaje =$_GET["puntaje"];
                $comentarios = $this->getComentariosTable()->buscarComentario($datos,$estado,$puntaje);  
                }
       return array(
          'comentarios' => $comentarios,
           'puntaje' =>$this-> puntaje()
        );
    }
}
