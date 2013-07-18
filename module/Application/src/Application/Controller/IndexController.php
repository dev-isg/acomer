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
// use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Application\Form\Formularios;
use Application\Form\Solicita;
use Application\Form\Contactenos;
// use Application\Model\Entity\Procesa;

use Application\Model\Entity\Album;
use Zend\Json\Json;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
// use Zend\I18n\Filter\Alnum;
//use Zend\View\Helper\HeadTitle; 
// use Platos\Model\Platos;
// use Platos\Model\PlatosTable;
// use Classes\Solr;
use Zend\View\Helper\HeadTitle;
class IndexController extends AbstractActionController
{
    protected $configTable;
    public $dbAdapter;

    public function indexAction()
    { 
       
        $view = new ViewModel();
       
//        $this->layout('layout/layout-portada2');
        
        $listades=$this->getConfigTable()->cantComentxPlato(1,'0,3',1);
        $listadeseg=$this->getConfigTable()->cantComentxPlato(1,'3,3',1);
        $listaval=$this->getConfigTable()->cantComentxPlato(2,3,3);
        $listault=$this->getConfigTable()->cantComentxPlato(2,3,2);
//        var_dump($listaval->toArray());exit;
        $this->layout()->clase = 'Home';
        $view->setVariables(array('lista' => $listades,'listaseg'=>$listadeseg,'listaval'=>$listaval,'listault'=>$listault,'clase'=>'Home'));
         return $view;
    
         
         
         
//       
    
    }
    
     public function jsondestaAction()
    { 
         $listades=$this->getConfigTable()->cantComentxPlato(1,'0,3',1);
         $valor =Json::encode($listades);
         echo $valor;
         exit();

    }
    
         public function joincomenatariosAction()
    { 
             
         $id  = $this->params()->fromQuery('id');
         $lista=$this->getConfigTable()->cantComentxPlato(2,3,2);
         $valor =Json::encode($lista);
         echo $valor;
         exit();

    }
    
    
            public function getConfigTable()
    {
        if (!$this->configTable) {
            $sm = $this->getServiceLocator();
            $this->configTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->configTable;
    }
    
    public function josAction()
    {  
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from(array('f' => 'ta_ubigeo')) 
            ->where(array('f.ch_provincia'=>'lima'));
               
             
             $selectString = $sql->getSqlStringForSqlObject($select);
          // echo $selectString;exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
    }
   public function detalleubicacionAction()
    { 
          $view = new ViewModel();   
          $request = $this->getRequest();
          $this->layout()->clase = 'buscar-distrito';
          if ($request->isGet()) {
           $datos =$this->request->getQuery(); 
           $plato= $datos['q'];    
           $filter   = new \Zend\I18n\Filter\Alnum(true);
           $texto = $filter->filter($plato);  
           $distrito = $datos['distrito'];  

              if($texto == '')    
              {$this->redirect()->toUrl('/');}
              if($texto == '')    
              {$this->redirect()->toUrl('/');}
           if($distrito != 'todos los distritos')
           {
                       $limite = 100;    
                        $resultados = false;
                        $palabraBuscar = isset($texto) ? $texto : false ;
                          $fd = array (  
                            'fq'=> 'en_estado:activo AND restaurant_estado:activo AND distrito:'.$distrito,
                              'sort'=>'en_destaque desc ',
                              ); 

			$solar = \Classes\Solr::getInstance()->getSolr();
                       if ($palabraBuscar)
                        { 
                            $solar = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {
                            $resultados = $solar->search($palabraBuscar, 0, $limite,$fd );                             
                          }
                          catch (Exception $e)
                          {                         
                                echo("<div>ingrese algun valor</div>"); 
                           }                             
                        }
                        $limit = 3;             
                        $palabraBuscar = isset($texto) ? $texto : false ;
                        $query = "($palabraBuscar) AND (en_destaque:si)";
                        $fq = array (  
                                   'sort'=>'random_' . uniqid() .' asc',
                            'fq'=> 'en_estado:activo AND restaurant_estado:activo AND distrito:'.$distrito,
                            'wt'=>'json');                                              
                        $results = false;
                        
                        if ($query)
                        { 
                         $solr = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $query = stripslashes($query);
                          }
                          try
                          {
                            $results = $solr->search($query, 0, $limit, $fq  );
                            
                          }
                          catch (Exception $e)
                          {
                                    echo("<div>ingrese algun valor</div>");         
                          }
                        }      
                 }       
                 else 
                 {
                    $limite = 100;    
                        $resultados = false;
                        $palabraBuscar = isset($texto) ? $texto : false ;
                          $fd = array (  
                            'fq'=>'en_estado:activo AND restaurant_estado:activo');

                        if ($palabraBuscar)
                        { 
                         $solar = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {
                            $resultados = $solar->search($palabraBuscar, 0, $limite,$fd );
                          }
                          catch (Exception $e)
                          {
                             
                          $this->redirect()->toUrl('/application');
                          }
                        }          
                        $limit = 3;             
                        $palabraBuscar = isset($texto) ? $texto : false ;
                        $query = "($palabraBuscar)";
                        $fq = array (  
                                   'sort'=>'random_' . uniqid() .' asc',
                            'fq'=>'en_estado:activo AND restaurant_estado:activo  AND en_destaque:si');                                           
                        $results = false;
                        if ($query)
                        { 
   
                            $solr = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $query = stripslashes($query);
                          }
                          try
                          {
                            $results = $solr->search($query, 0, $limit, $fq  );

                          }
                          catch (Exception $e)
                          {
                          
                                   $this->redirect()->toUrl('/application');       
                          }
                         }                
                 }                                   
         }
        $form = new Formularios(); 
        $comidas =  $this->joinAction()->toArray();
        $com = array();
        foreach($comidas as $y){
             $com[$y['va_distrito']] = $y['va_distrito'];
        }
        setcookie('distrito', $datos['distrito']);
        setcookie('q',$texto);
        $form->get('distrito')->setValue($distrito);
        $form->get('q')->setValue($texto);
         $form->get('distrito')->setValueOptions($com);
         $form->get('submit')->setValue('Buscar'); 
//         $total = (int) $resultados->response->numFound;
         $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
         $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
         $paginator->setItemCountPerPage(10);
         
            $total=$paginator->getTotalItemCount();
            $first=$paginator->getPages()->firstItemNumber;
            $last=$paginator->getPages()->lastItemNumber;
            if($total==1){
                $mostrar='Mostrando '.$first.' de '.$total.' resultado';
            }else{
                $mostrar='Mostrando '.$first.'-'.$last.' de '.$total.' resultados';

            }
           
          $listades=$this->getConfigTable()->cantComentxPlato(1,'0,3',1);
         $view->setVariables( array('total' => $total,'distrito'=>$distrito,'plato'=>$texto,'lista' => $listades,'destacados'=>$results->response->docs,'general'=>$paginator,'form' => $form,'mostrar'=>$mostrar,'nombre'=>$texto));//,'error'=>$error
       return $view;
      }

     public function verAction()             
        {  
         
        $view = new ViewModel();
//       $this->layout('layout/layout-portada');
       $this->layout()->clase = 'buscar';
        $filtered = $this->params()->fromQuery('q');
              $filter   = new \Zend\I18n\Filter\Alnum(true);
                  $texto = $filter->filter($filtered);
                   $limite = 100;    
                        $resultados = false;
                        $palabraBuscar = isset($texto) ? $texto : false ;
                          $fd = array (  
                            'fq'=>'en_estado:activo AND restaurant_estado:activo',
                              );
                          if($palabraBuscar=='')
                          {$this->redirect()->toUrl('/');  }
                        if ($palabraBuscar)
                        { 
                         $solar = \Classes\Solr::getInstance()->getSolr();
                         if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {  $resultados = $solar->search($palabraBuscar, 0, $limite,$fd );
                          
                          }
                          catch (Exception $e)
                          {        
                         $this->redirect()->toUrl('/');
                          }
                        }
                        $limit = 3;             
                        $palabraBuscar = isset($texto) ? $texto : false ;
                        $query = "($palabraBuscar)";
                        $fq = array (  
                                   'sort'=>'random_' . uniqid() .' asc',
                            'fq'=>'en_estado:activo AND restaurant_estado:activo AND en_destaque:si');                                           
                        $results = false;
                        if ($query)
                        { 
   
                        $solr = \Classes\Solr::getInstance()->getSolr();
                        if (get_magic_quotes_gpc() == 1)
                          {
                            $query = stripslashes($query);
                          }
                          try
                          {  $results = $solr->search($query, 0, $limit, $fq  ); }
                          catch (Exception $e)
                          { $this->redirect()->toUrl('/');      }
                         }
        
        $form = new Formularios();
        $listades=$this->getConfigTable()->cantComentxPlato(1,'0,3',1);
        $comidas =  $this->joinAction()->toArray();   
        $com = array();
        foreach($comidas as $y){
            $com[$y['va_distrito']] = $y['va_distrito'];
        }
    
         setcookie('q',$texto);
           setcookie('distrito',$comidas[41]['va_distrito']);
        $form->get('distrito')->setValue($comidas[41]['va_distrito']);
        $form->get('distrito')->setValueOptions($com);
        $form->get('q')->setValue($texto);
        $form->get('submit')->setValue('Buscar');

        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);
    
   $total=$paginator->getTotalItemCount();
   $first=$paginator->getPages()->firstItemNumber;
   $last=$paginator->getPages()->lastItemNumber;
   if($total==1){
       $mostrar='Mostrando '.$first.' de '.$total.' resultado';
   }else{
       $mostrar='Mostrando '.$first.'-'.$last.' de '.$total.' resultados';
   
   }
         $view->setVariables( array('total' => $total,'lista' => $listades,'destacados'=>$results->response->docs,'general'=>$paginator,'form' => $form,'nombre'=>$texto,'mostrar'=>$mostrar));
        return $view;
    }
    
    public function jsonmapasaAction()    { 
        $distrito=  $this->params()->fromQuery('distrito');
        $view  = new viewModel();
        $view->setTerminal(true);
        $texto = $this->params()->fromQuery('q');
        setcookie('distrito',$distrito);
        setcookie('q',$texto);
        $filter   = new \Zend\I18n\Filter\Alnum(true);
        $plato = $filter->filter($texto);
     
        
             if($distrito != 'todos los distritos')
           {

                        $resultados = false;
                        $palabraBuscar = isset($plato) ? $plato : false ;
                        $list = 1000;
                          $fd = array (  
                            'fq'=> 'en_estado:activo AND restaurant_estado:activo AND distrito:'.$distrito,
                              'sort'=>'en_destaque desc',
                              'fl'=>'id,latitud,longitud,tx_descripcion,va_imagen,restaurante_estado,restaurante,name,plato_tipo,distrito',
                              'wt'=>'json');      
                        if ($palabraBuscar)
                        { 
                          $solar = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {
                            $resultados = $solar->search($palabraBuscar, 0,$list, $fd );
                          }
                          catch (Exception $e)
                          {
                          
                                 die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
          
                          }
                          if($resultados == '')
                              
                          {
                             echo 'error en busqueda' ;exit;
                          }
                          else  {echo $resultados->getRawResponse(); 
                    exit;}                        
                        }
                        
                        
                        } 
                        
                 
                        else {
   $limite = 1000;    
                        $resultados = false;
                        $palabraBuscar = isset($plato) ? $plato : false ;
                          $fd = array (  
                            'fq'=>'en_estado:activo AND restaurant_estado:activo');
                
                        if ($palabraBuscar)
                        { 
                            $solar = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {
                            $resultados = $solar->search($palabraBuscar, 0, $limite,$fd );
                          //var_dump($resultados);exit;

                          }
                          catch (Exception $e)
                          {
                             
                         $this->redirect()->toUrl('/');
                          }
                        }
          
                        $limit = 3;             
                        $palabraBuscar = isset($plato) ? $plato : false ;
                        $query = "($palabraBuscar) AND (en_destaque:si)";
                        $fq = array (  
                                   'sort'=>'random_' . uniqid() .' asc',
                            'fq'=>'en_estado:activo AND restaurant_estado:activo');                                           
                        $results = false;
                        if ($query)
                        { 
                            $solr = \Classes\Solr::getInstance()->getSolr();
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $query = stripslashes($query);
                          }
                          try
                          {
                            $results = $solr->search($query, 0, $limit, $fq  );

                          }
                          catch (Exception $e)
                          {
                          
                                 $this->redirect()->toUrl('/');      
                          }
                         }
                        }
    
                     
                         echo $resultados->getRawResponse(); 
                    exit;
                        
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
    
    
        public function joinAction()
    {  
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
       $select = $sql->select();
        $select->from('ta_distrito')    
       ->order('va_distrito asc DESC'); 
           $selectString = $sql->getSqlStringForSqlObject($select);
            //var_dump($selectString);exit;
          $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            //var_dump($results);exit;
            return $results;
            
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
    
    public function nosotrosAction(){
        $view = new ViewModel();
//        $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Nosotros';
//        $view->setVariables(array());
//         return $view;
        
    }
        public function solicitaAction(){
                    $view = new ViewModel();
//        $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Solicita';
        $form=new Solicita("form");
        $request=$this->getRequest();
        if($request->isPost()){
        $nombre =htmlspecialchars ($this->params()->fromPost('nombre_complet', 0));
        $email = htmlspecialchars ($this->params()->fromPost('email',0));
        $plato = htmlspecialchars ($this->params()->fromPost('nombre_plato',0));
        $descripcion = htmlspecialchars ($this->params()->fromPost('descripcion',0));
        $nombre_restaurant = htmlspecialchars ($this->params()->fromPost('nombre_restaurant',0));
        $telefono = htmlspecialchars ($this->params()->fromPost('telefono',0));
     
        $bodyHtml='<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                     Nombre <strong style="color:#133088; font-weight: bold;">'.utf8_decode($nombre).'</strong><br />
                                                     Email <strong style="color:#133088; font-weight: bold;">'.utf8_decode($email).'</strong><br />
                                                     Plato <strong style="color:#133088; font-weight: bold;">'.utf8_decode($plato).'</strong><br />
                                                     Descripcion <strong style="color:#133088; font-weight: bold;">'.utf8_decode($descripcion).'</strong><br />
                                                     Restaurante <strong style="color:#133088; font-weight: bold;">'.utf8_decode($nombre_restaurant).'</strong><br />
                                                     Telefono <strong style="color:#133088; font-weight: bold;">'.utf8_decode($telefono).'</strong><br />
                                              
                                                     </div>
                                               </body>
                                               </html>';
    
        $message = new Message();
        $message->addTo('informes@innovationssystems.com', $nombre)
        ->setFrom('no-reply@listadelsabor.pe)', 'listadelsabor.com')
        ->setSubject('Moderacion de comentario de listadelsabor.com')
        ->setBody($bodyHtml);
        $transport = new SendmailTransport();
  
//        
//        $options   = new \Zend\Mail\Transport\SmtpOptions(array(
//            'name' => 'localhost',
//            'host' => '127.0.0.1',
//            'port' => 25,
//        ));
//        
//        $transport->setOptions($options);

        $transport->send($message);
        $this->redirect()->toUrl('/solicita');
        }

        $view->setVariables(array('form' => $form));
         return $view;
        
    }
    
    public function contactenosAction(){
        
        $view = new ViewModel();

        $this->layout()->clase = 'Solicita';
        $form=new Contactenos("form");
        $request=$this->getRequest();
//        var_dump($request->isPost());
        if($request->isPost()){
         
//               var_dump($request->isPost());exit;
        $nombre = htmlspecialchars ($this->params()->fromPost('nombre', 0));
        $email = htmlspecialchars ($this->params()->fromPost('email',0));
        $asunto = htmlspecialchars ($this->params()->fromPost('asunto',0));
        $mensaje = htmlspecialchars ($this->params()->fromPost('mensaje',0));

        $bodyHtml='<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                     Nombre <strong style="color:#133088; font-weight: bold;">'.utf8_decode($nombre).'</strong><br />
                                                     Email <strong style="color:#133088; font-weight: bold;">'.utf8_decode($email).'</strong><br />
                                                     Asunto <strong style="color:#133088; font-weight: bold;">'.utf8_decode($asunto).'</strong><br />
                                                     Mensaje <strong style="color:#133088; font-weight: bold;">'.utf8_decode($mensaje).'</strong><br />
                                              
                                                     </div>
                                               </body>
                                               </html>';
        
        $message = new Message();
        $message->addTo('innovations.systems.group@gmail.com', $nombre)
        ->setFrom('no-reply@listadelsabor.pe)', 'listadelsabor.com')
        ->setSubject('Moderacion de comentario de listadelsabor.com')
        ->setBody($bodyHtml);
        $transport = new SendmailTransport();
        $transport->send($message);
        $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/contactenos');
//        $this->redirect()->toUrl('/contactenos');///application/index
        }
        
        $view->setVariables(array('form' => $form));
         return $view;
        
        
    }
        public function terminosAction(){
                $view = new ViewModel();
//        $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Terminos';
    }
    
    
    
   
}
