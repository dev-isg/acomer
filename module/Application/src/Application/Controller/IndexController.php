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
use Application\Form\Registro;
use Application\Form\Registroplato;
use Application\Form\Contactenos;
use SanAuth\Controller\AuthController; 
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Application\Model\Entity\Album;
use Zend\Mail\Message;
use Zend\Http\Request;
//use SanAuth\Controller\AuthController; 
use Usuario\Model\ClientesTable;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
// use Zend\I18n\Filter\Alnum;
// use Zend\View\Helper\HeadTitle;
// use Platos\Model\Platos;
// use Platos\Model\PlatosTable;
// use Classes\Solr;
use Zend\View\Helper\HeadTitle;

class IndexController extends AbstractActionController
{

    protected $configTable;
    protected $restauranteTable;
    protected $clientesTable;
     protected $authservice;
    public $dbAdapter;
public function __construct()
	{
		$this->_options = new \Zend\Config\Config ( include APPLICATION_PATH . '/config/autoload/global.php' );
	}
    public function indexAction()
    {
        $view = new ViewModel();
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read();  
       if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; 
        if(session_status() === PHP_SESSION_ACTIVE){$this->layout()->face = $facebook['name'];}
        else{
        if($facebook['id_facebook']){ 
        $id_face=$this->getClientesTable()->usuarioface($facebook['id_facebook']); 
                         if(count($id_face)>0)
                         {if($id_face[0]['id_facebook']=='')  
                          { $this->getClientesTable()->idfacebook($id_face[0]['in_id'],$facebook['id_facebook'],$facebook['logoutUrl']);
                          $this->layout()->face = $facebook['name']; }     
                         else{$this->getClientesTable()->idfacebook2($id_face[0]['in_id'],$facebook['logoutUrl']);
                         $this->layout()->face = $facebook['name']; }    }
                         else{$this->getClientesTable()->insertarusuariofacebbok($facebook['name'],$facebook['email'],$facebook['id_facebook'],$facebook['logoutUrl']);  
                         $this->layout()->face = $facebook['name'];  }
                         }  }
        }
        $comidas = $this->joinAction()->toArray();
        $this->layout()->comidas = $comidas;
        $mistura=$this->getConfigTable()->platoslistadelsabor();
        $page2 = (int) $this->params()->fromQuery('page', 1);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($mistura));
        $paginator->setCurrentPageNumber($page2);
         $paginator->setItemCountPerPage(9); 
         $cante=count($mistura->toArray());
         if(ceil($cante/9) <$page2){
             $view->setTerminal(true);
            $view->setTemplate('layout/layout-error');}
         $url = $_SERVER['REQUEST_URI'];
        if ($url != '/') {
            if ($url) {
                $page = strpos($url, 'page=');
                if ($page) {
                    $urlf = substr($url, 0, $page - 1);
                } else {
                    $urlf = $url;
                }
                $urlf = $urlf . '?';
            } else {
                $urlf = $urlf . '?';
            }
        } else {
            $auxurl = strpos($url, '/');
            $urlf = substr($url, 0, $auxurl);
            $urlf = $urlf . '?';
        }
        $this->layout()->clase = 'Home';
        $menus = $this->menu();
        $banner = $this->banner();
        $view->setVariables(array(
            'promociones'=>$paginator,
            'clase' => 'Home',
              'urlac' => $urlf,
            'menus'=>$menus,
            'banner'=>$banner,
            'session'=>$session,
            'user' => $user,
            'loginUrl'  =>$loginUrl, 
            'w'=>$ddd
        ));
        return $view;
    }
    
    
    public function menu()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from('ta_menu')
                ->where(array('en_estado'=>'activo'))
                ->order('in_orden ASC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }
     public function banner()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from('ta_banner')
      ->where(array('en_estado'=>'activo'))
                ->order('in_orden ASC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
         return $results->toArray();
    }
        public function distritosperu()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from('ta_ubigeo')
                ->columns(array('ch_distrito'=>'ch_distrito','ch_provincia'=>'ch_provincia','ch_departamento'=>'ch_departamento'))
                ->join('ta_local', 'ta_ubigeo.in_id = ta_local.ta_ubigeo_in_id ', array(), 'left')
            ->where(array('ta_local.ta_ubigeo_in_id!=?'=>null))->group('ta_ubigeo.ch_distrito');    

        $selectString = $sql->getSqlStringForSqlObject($select);
      //  var_dump($selectString);exit;
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
         return $results->toArray();
    }
   
    public function jsondestaAction()
    {
        $listades = $this->getConfigTable()->cantComentxPlato(1, '0,3', 1);
        $valor = Json::encode($listades);
        echo $valor;
        exit();
    }

    public function joincomenatariosAction()
    {
        $id = $this->params()->fromQuery('id');
        $lista = $this->getConfigTable()->cantComentxPlato(2, 3, 2);
        $valor = Json::encode($lista);
        echo $valor;
        exit();
    }

    public function getConfigTable()
    {
        if (! $this->configTable) {
            $sm = $this->getServiceLocator();
            $this->configTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->configTable;
    }

    public function josAction()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from(array(
            'f' => 'ta_ubigeo'
        ))
            ->where(array(
            'f.ch_provincia' => 'lima'
        ));
        
        $selectString = $sql->getSqlStringForSqlObject($select);
        // echo $selectString;exit;
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }
    // FUNCION PARA TABLET Y PC
    
       public function consultasAction($limit,$plato_tipo=null, $platid=null)       
    {
           if($plato_tipo==null or $platid==null )
           {$texto  = 'en_destaque:si'; 
           $palabraBuscar = isset($texto) ? $texto : false;
               $query = "($palabraBuscar) AND (en_destaque:si)";
                $fq = array(
                    'sort' => 'random_' . uniqid() . ' asc, puntuacion desc',
                    'fq' => 'en_estado:activo  AND restaurant_estado:activo ',
                    'wt' => 'json'
                );}
           else{$texto  = 'plato_tipo:'.$plato_tipo; 
           $palabraBuscar = isset($texto) ? $texto : false;
               $query = "($palabraBuscar) AND (en_destaque:si)";
                $fq = array(
                    'sort' => 'random_' . uniqid() . ' asc, puntuacion desc',
                    'fq' => 'en_estado:activo  AND restaurant_estado:activo AND -id:'.$platid  ,
                    'wt' => 'json'
                );}    
                $resultados = false;
                if ($query) {
                    $solr = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $query = stripslashes($query);}
                    try { $resultados = $solr->search($query, 0, $limit, $fq);
                //   var_dump(count($resultados->response->docs));exit;
                    } catch (Exception $e) {
                  echo ("<div>ingrese algun valor</div>"); }} 
                  
             return $resultados->response->docs;     
    }
    public function detalleubicacionAction()
    {
        $view = new ViewModel();
        $request = $this->getRequest();
         $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read();
      if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; 
        if(session_status() === PHP_SESSION_ACTIVE){$this->layout()->face = $facebook['name'];}
        else{
        if($facebook['id_facebook']){ 
        $id_face=$this->getClientesTable()->usuarioface($facebook['id_facebook']); 
                         if(count($id_face)>0)
                         {if($id_face[0]['id_facebook']=='')  
                          { $this->getClientesTable()->idfacebook($id_face[0]['in_id'],$facebook['id_facebook'],$facebook['logoutUrl']);
                          $this->layout()->face = $facebook['name']; }     
                         else{$this->getClientesTable()->idfacebook2($id_face[0]['in_id'],$facebook['logoutUrl']);
                         $this->layout()->face = $facebook['name']; }    }
                         else{$this->getClientesTable()->insertarusuariofacebbok($facebook['name'],$facebook['email'],$facebook['id_facebook'],$facebook['logoutUrl']);  
                         $this->layout()->face = $facebook['name'];  }
                         }  }
        }
        $this->layout()->clase = 'buscar-distrito';
        if ($request->isGet()) {
            $datos = $this->request->getQuery();
            $plato = $datos['q'];      
            $paginas = $datos['page']; 
            $distrit = $datos['distrito'];
            $valorubigeo =  explode(',', $distrit);
            $distrito = $valorubigeo[0];
            $valor = explode(" ",$plato);
            if($valor[0]=='restaurante:')
            {$buscar = $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];
            $texto = $valor[0].'"'.$buscar.'"'; 
            $ruta = $this->_options->data->busqueda .'/busqueda.txt';
            $fp = fopen($ruta,"a");
            fwrite($fp, "$buscar , $distrito" . PHP_EOL);
            fclose($fp);}
            elseif($valor[0]=='tag:')     
            {$buscar = $valor[1].' '.$valor[2].' '.$valor[3];
            $texto = $valor[0].'"'.$buscar.'"'; 
            }
            elseif($valor[0]=='name:')     
            {$buscar = $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];
            $texto = $valor[0].'"'.$buscar.'"'; 
            }
            else{ $filter = new \Zend\I18n\Filter\Alnum(true);
            $texto = $filter->filter($plato);
            $ruta = $this->_options->data->busqueda .'/busqueda.txt';
            $fp = fopen($ruta,"a");
            fwrite($fp, "$texto , $distrito" . PHP_EOL);
            fclose($fp);
            }
             
            if ($texto == '') {
                $this->redirect()->toUrl('/');
            }
            if (strtoupper($distrito)and strtoupper($distrito)!='LIMA') {
                $limite = 10;
                $distrits = '"'.$distrito.'"';
                if($paginas=='')
                 {$start = 0;}
                  else{$start=($paginas-1)*10;}
                $resultados = false;
                $buscarsolar= '(('.$texto.') AND en_destaque:si)^100 OR ('.$texto.')';
                $palabraBuscar = isset($buscarsolar) ? $buscarsolar : false;
                $fd = array(
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND distrito:' .$distrits,
                );
                
                $solar = \Classes\Solr::getInstance()->getSolr();
                if ($palabraBuscar) {
                    $solar = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $palabraBuscar = stripslashes($palabraBuscar);
                    }
                    try {
                        $resultados = $solar->search($palabraBuscar,$start, $limite, $fd);
                    } catch (Exception $e) {
                        echo ("<div>ingrese algun valor</div>");
                    }
                }

          $limit_distritos = 9999;
                $query_distritos = "-($palabraBuscar)";
                $fq_distritos = array(
                    'sort' => 'random_' . uniqid() . ' asc',
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND -distrito:' .$distrito,
                    'wt' => 'json',
                    'fl'=>'distrito'
                );
                $results_distritos = false;
                if ($query_distritos) {
                    $solr = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $query_platos = stripslashes($query_distritos);
                    }
                    try {
                        $results_distritos = $solr->search($query_distritos, 0,$limit_distritos, $fq_distritos);
                    } catch (Exception $e) {
                        echo ("<div>ingrese algun valor</div>");
                    }
                }
                
            } elseif (strtoupper($distrito)=='LIMA') {
                $limite = 10;
                if($paginas=='')
                 {$start = 0;}
               else{$start=($paginas-1)*10;}
                $resultados = false;
                $buscarsolar= '(('.$texto.') AND en_destaque:si)^100 OR ('.$texto.')';
                $palabraBuscar = isset($buscarsolar) ? $buscarsolar : false;
                $fd = array(
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND  departamento:' .$distrito,
                );
                
                $solar = \Classes\Solr::getInstance()->getSolr();
                if ($palabraBuscar) {
                    $solar = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $palabraBuscar = stripslashes($palabraBuscar);
                    }
                    try {
                        $resultados = $solar->search($palabraBuscar,$start, $limite, $fd);
                    } catch (Exception $e) {
                        echo ("<div>ingrese algun valor</div>");
                    }
                }
          $limit_distritos = 9999;
                $query_distritos = "-($palabraBuscar)";
                $fq_distritos = array(
                    'sort' => 'random_' . uniqid() . ' asc',
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND -distrito:' .$distrito,
                    'wt' => 'json',
                    'fl'=>'distrito'
                );
                $results_distritos = false;
                if ($query_distritos) {
                    $solr = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $query_platos = stripslashes($query_distritos);
                    }
                    try {
                        $results_distritos = $solr->search($query_distritos, 0,$limit_distritos, $fq_distritos);
                    } catch (Exception $e) {
                        echo ("<div>ingrese algun valor</div>");
                    }
                }
                
            }else{
                $limite = 10;
                if($paginas=='')
                 {$start = 0;}
                  else{$start=($paginas-1)*10;}
                $resultados = false;
                $buscarsolar= '(('.$texto.') AND en_destaque:si)^100 OR ('.$texto.')';
                $palabraBuscar = isset($buscarsolar) ? $buscarsolar : false;
                $fd = array(
                    'fq' => 'en_estado:activo AND restaurant_estado:activo',
                );
                
                if ($palabraBuscar) {
                    $solar = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $palabraBuscar = stripslashes($palabraBuscar);
                    }
                    try {
                        $resultados = $solar->search($palabraBuscar, $start, $limite, $fd);
                      
                    } catch (Exception $e) {
                        
                        $this->redirect()->toUrl('/application');
                    }
                }
               else{$results =$resulta->response->docs;}
       }
                $limit_platos = 9999;
                $query_platos = "-($palabraBuscar)";
                $fq_platos = array(
                    'sort' => 'random_' . uniqid() . ' asc',
                    'fq' => 'en_estado:activo AND restaurant_estado:activo',
                    'wt' => 'json',
                    'fl'=>'name'
                );
                $results_platos = false;
                if ($query_platos) {
                    $solr = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $query_platos = stripslashes($query_platos);
                    }
                    try {
                        $results_platos = $solr->search($query_platos, 0, $limit_platos, $fq_platos);
                    } catch (Exception $e) {
                        echo ("<div>ingrese algun valor</div>");
                    }
                }
                ///////////////////////////////////////fin/////////////////////////////////////////////////////////
                //////////////////////////////////////////random de 5 DISTRITOS distinc////////////////////////////////////
              
                ///////////////////////////////////////fin/////////////////////////////////////////////////////////
                
        }
        $form = new Formularios();
         if($valor[0]=='restaurante:')
         { $form->get('q')->setValue($plato);
         $valores = $buscar;} 
         elseif($valor[0]=='tag:')
         { $form->get('q')->setValue($plato);
         $valores = $buscar;}
         elseif($valor[0]=='name:')
         { $form->get('q')->setValue($plato);
         $valores = $buscar;}
         else{ setcookie('q', $texto);
         $form->get('q')->setValue($texto);
         $valores =$texto;}
        setcookie('distrito', $distrit);
        $form->get('distrito')->setValue($distrit);
        $form->get('submit')->setValue('Buscar'); 
        $paginato = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
        $paginato->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginato->setItemCountPerPage($limite);
        
       
            if ($resultados)
                  {
                    $total = (int) $resultados->response->numFound;
                    $end = count($resultados->response->docs)+$start;
                    $inicio = $start+1;    
                  }  
        if ($total <= 10) {
           //  $mostrar = 'Mostrando ' . $inicio . ' - ' . $end . ' de ' . $total . ' resultados';
             $mostrar =  $total . ' resultados';
        } else {
          //  $mostrar = 'Mostrando ' . $inicio . ' - ' . $end . ' de ' . $total . ' resultados';
            $mostrar =  $total . ' resultados';
        }
         if($_GET['callback'])
       { 
        $view = new ViewModel();
        header('Content-type: application/x-javascript');
        header("Status: 200");
          echo "jsonpCallback(".$resultados->getRawResponse().")";
                exit();
                $view->setTerminal(true);
                return $view;
       }
        $arrpl=array();
        $arrest=array();
        if (count($resultados->response->docs) < 5 && count($resultados->response->docs) > 0) {
            $contc = 0;
            $contrc=0;
            foreach ($resultados->response->docs as $plat) {
                    if(!in_array($plat->name,$arrpl)){
                        $arrpl[] = $plat->name;
//                        $cont++;
                    }
                    if(!in_array($plat->distrito,$arrest)){
                        $arrest[] = $plat->distrito;
                    }
            }
            if (count($arrpl) < 5) {
                $maxcantidad = 5 - count($arrpl);
                foreach ($results_platos->response->docs as $plat2) {
                    if ($maxcantidad > $contc) {
                        if(!in_array($plat2->name,$arrpl)){
                        $arrpl[] = $plat2->name;
                        $contc++;
                        }
                    }
                }
            }
            if(count($arrest) < 5){
                $maxcantidadr = 5 - count($arrest);
                 foreach ($results_distritos->response->docs as $rest2) {
                     if ($maxcantidadr > $contrc) {
                        if(!in_array($rest2->distrito,$arrest)){
                            $arrest[] = $rest2->distrito;
                            $contrc++;
                         }
                    }
                }           
            }
        } elseif (count($resultados->response->docs) < 5 && count($resultados->response->docs) == 0) {
            $cont = 0;
            $contr=0;
            foreach ($results_platos->response->docs as $plat) {
                if($cont<5){
                    if(!in_array($plat->name,$arrpl)){
                        $arrpl[] = $plat->name;
                        $cont++;
                    }
                }
                
            }
            foreach ($results_distritos->response->docs as $rest) {
                 if($contr<5){
                    if(!in_array($rest->distrito,$arrest)){
                        $arrest[] = $rest->distrito; 
                        $contr++;
                    }
                }         
            }
        } else {
            $cont = 0;
            $contr=0;
            foreach ($resultados->response->docs as $plat) {
                if ($cont < 5) {
                    if(!in_array($plat->name,$arrpl)){
                        $arrpl[] = $plat->name;
                        $cont++;
                    }   
                }
                if($contr<5){
                    if(!in_array($plat->distrito,$arrest)){
                        $arrest[] = $plat->distrito;
                        $contr++;
                    }
                }
            }
        }
        
        $busquedatitle=$valores.':'.implode(",",$arrpl).':'.implode(",",$arrest).' │ ';
        
         $this->layout()->description=$busquedatitle;
        
        $listatot = $this->getConfigTable()->cantComentxPlato();
        $listatot = $listatot->toArray();
        
        foreach ($listatot as $key => $value) {
            if ($key < 3) {
                $listades[] = $listatot[$key];
            } else {
                $listadeseg[] = $listatot[$key];
            }
        }
        $menus = $this->menu();
        $view->setVariables(array(
            'total' => $total,
            'distrito' => $valorubigeo[0],
            'plato' => $valores,
            'lista' => $listades,
            'destacados' => $results,
            'general' => $paginato,
            'form' => $form,
            'mostrar' => $mostrar,
            'nombre' => $texto,
            'busquedatitle'=>$busquedatitle,
            'total' =>$total,
            'start'=> $start,
            'end' =>$end,
            'plat'=>$plato,
            'session'=>$session,
            'menus'=>$menus,
            'masdestacados'=>$consultafinal,
            'session'=>$session
        ));
        return $view;
    }
    
    // FUNCION SOLO PARA MOVILES ,AQUI A QUE PARTIR EL VALOR DEL fromQuery PARA
 
    public function verAction()
    {
        $view = new ViewModel();
        
        $this->layout()->clase = 'buscar';
        $filtered = $this->params()->fromQuery('q');
        $paginas = $this->params()->fromQuery('page');
        $valor = explode(" ", $filtered);
        //var_dump($valor);exit;
         if($valor[0]=='restaurante:')
        {$buscar =  $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];     
        $texto = $valor[0].'"'.$buscar.'"';
        $distrito = '';
        $ruta = $this->_options->data->busqueda .'/busqueda_movil.txt';
        $fp = fopen($ruta,"a");
        fwrite($fp, "$buscar , $distrito" . PHP_EOL);
        fclose($fp); }   
        elseif($valor[0]=='tag:')     
        {$buscar = $valor[1].' '.$valor[2].' '.$valor[3];
        $texto = $valor[0].'"'.$buscar.'"';}
            elseif($valor[0]=='name:')
            {$buscar = $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];
          $texto = $valor[0].'"'.$buscar.'"'; }
        else{ $filtered = strtoupper($filtered);
        $filter = new \Zend\I18n\Filter\Alnum(true);
        $text = trim($filter->filter($filtered));
        $text = preg_replace('/\s\s+/', ' ', $text);
        $busqueda = explode(" EN ", $text);
        if($this->getRestauranteTable()->ubigeototal2($busqueda[1])>0){
            $distrito=$busqueda[1]; 
        }

        
        $texto = $busqueda[0];
        $ruta = $this->_options->data->busqueda .'/busqueda_movil.txt';
        $fp = fopen($ruta,"a");
        fwrite($fp, "$texto , $distrito" . PHP_EOL);
        fclose($fp);
        }
        if($distrito=='LIMA' or $distrito=='lima')
       {$limite = 10;
                if($paginas=='')
                 {$start = 0;}
               else{$start=($paginas-1)*10;}
        $resultados = false;
      $buscarsolar= '(('.$texto.') AND en_destaque:si)^100 OR ('.$texto.')';
       $palabraBuscar = isset($buscarsolar) ? $buscarsolar : false;
        $fd = array(
            'fq' => 'en_estado:activo AND restaurant_estado:activo AND  departamento:' .$distrito,
        );
        if ($palabraBuscar == '') {
            $this->redirect()->toUrl('/');
        }
        
        
        if ($palabraBuscar) {
            $solar = \Classes\Solr::getInstance()->getSolr();
            if (get_magic_quotes_gpc() == 1) {
                $palabraBuscar = stripslashes($palabraBuscar);
            }
            try {
                $resultados = $solar->search($palabraBuscar,$start, $limite, $fd);
            } catch (Exception $e) {
                $this->redirect()->toUrl('/');
            }
        }

       }
        else
        {
         $limite = 10;
                if($paginas=='')
                 {$start = 0;}
                  else{$start=($paginas-1)*10;}
        $resultados = false;
       $buscarsolar= '(('.$texto.') AND en_destaque:si)^100 OR ('.$texto.')';
       $palabraBuscar = isset($buscarsolar) ? $buscarsolar : false;
        $distrito = ($distrito) ? ' AND distrito:' . $distrito : '';
        $fd = array(
            'fq' => 'en_estado:activo AND restaurant_estado:activo' . $distrito
        );
        if ($palabraBuscar == '') {
            $this->redirect()->toUrl('/');
        }
        
        
        if ($palabraBuscar) {
            $solar = \Classes\Solr::getInstance()->getSolr();
            if (get_magic_quotes_gpc() == 1) {
                $palabraBuscar = stripslashes($palabraBuscar);
            }
            try {
                $resultados = $solar->search($palabraBuscar,$start, $limite, $fd);
            } catch (Exception $e) {
                $this->redirect()->toUrl('/');
            }
        }

        
    }     
                  
                  
                  
        $form = new Formularios();
      
        if($valor[0]=='restaurante:')
        {
        $form->get('q')->setValue($filtered);
        $valores = $buscar;
        }
         elseif($valor[0]=='tag:')
        {
        $form->get('q')->setValue($filtered);
        $valores = $buscar;
        }
        elseif($valor[0]=='name:')
        {
        $form->get('q')->setValue($filtered);
        $valores = $buscar;
        }
        else {setcookie('q', $text);
        $form->get('q')->setValue($text);
        $valores = $texto;
        }
        $form->get('submit')->setValue('Buscar');
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
        $paginator->setCurrentPageNumber((int) $this->params()
        ->fromQuery('page', 1));
       $paginator->setItemCountPerPage($limite);
       
       if($_GET['callback'])
       {
           $view = new ViewModel();
                header('Content-type: application/x-javascript');
                header("Status: 200");
               echo "jsonpCallback(".$resultados->getRawResponse().")";

                 exit();
                $view->setTerminal(true);
            return $view;
       }
       
            if ($resultados)
                  {
                    $total = (int) $resultados->response->numFound;
                    $end = count($resultados->response->docs)+$start;
                    $inicio = $start+1;    
                  }  
        if ($total <= 10) {
             //$mostrar = 'Mostrando ' . $inicio . ' - ' . $end . ' de ' . $total . ' resultados';
             $mostrar = $total . ' resultados';
        } else {
           // $mostrar = 'Mostrando ' . $inicio . ' - ' . $end . ' de ' . $total . ' resultados';
            $mostrar = $total . ' resultados';
        }
        $listatot = $this->getConfigTable()->cantComentxPlato();
        $listatot = $listatot->toArray();
        
        foreach ($listatot as $key => $value) {
            if ($key < 3) {
                $listades[] = $listatot[$key];
            } else {
                $listadeseg[] = $listatot[$key];
            }
        }
        $view->setVariables(array(
            'total' => $total,
            'lista' => $listades,
            'destacados' => $results,
            'general' => $paginator,
            'form' => $form,
            'nombre' => $text,
            'masplatosdestacados'=>$consultafinal,
              'plato' => $valores,
            'mostrar' => $mostrar,
            'plat'=>$filtered
             
        ));
        return $view;
    }

    public function unoAction()
    {
        $view = new viewModel();
        $view->setTerminal(true);
         $filtered = $this->params()->fromQuery('q');
        $valor =explode(" ",$filtered);
        if($valor[0]=='restaurante:')
            { $buscar = $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];
            $texto = $valor[0].'"'.$buscar.'"'; }  
            elseif($valor[0]=='tag:')
            { $buscar = $valor[1].' '.$valor[2].' '.$valor[3];
            $texto = $valor[0].'"'.$buscar.'"'; }
            elseif($valor[0]=='name:')
            { $buscar = $valor[1].' '.$valor[2].' '.$valor[3];
            $texto = $valor[0].'"'.$buscar.'"'; }
            else{
                setcookie('q', $texto);
                $filtered = strtoupper($filtered);
                $filter = new \Zend\I18n\Filter\Alnum(true);
                $text = trim($filter->filter($filtered));
                $text = preg_replace('/\s\s+/', ' ', $text);
                $busqueda = explode(" EN ", $text);
            if($this->getRestauranteTable()->ubigeototal2($busqueda[1])>0){
                $distrito=$busqueda[1];
            }
            $texto = $busqueda[0];}
            
            
            if($distrito=='LIMA' or $distrito=='lima')
            {  $limite = 10;
            $resultados = false;
            $palabraBuscar = isset($texto) ? $texto : false;
           // $distrito = ($distrito) ? ' AND distrito:' . $distrito : '';
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo AND  departamento:' .$distrito,
            );
            if ($palabraBuscar == '') {
                $this->redirect()->toUrl('/');
            }
            if ($palabraBuscar) {
                $solar = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $palabraBuscar = stripslashes($palabraBuscar);
                }
                try {
                    $resultados = $solar->search($palabraBuscar, 0, $limite, $fd);
                } catch (Exception $e) {
                    $this->redirect()->toUrl('/');
                }
            }
        
            $limit = 3;
            $palabraBuscar = isset($texto) ? $texto : false;
            $query = "($palabraBuscar)";
            $fq = array(
                'sort' => 'random_' . uniqid() . ' asc',
                'fq' => 'en_estado:activo AND restaurant_estado:activo AND en_destaque:si AND  departamento:' .$distrito,
            );
            $results = false;
            if ($query) {
        
                $solr = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $query = stripslashes($query);
                }
                try {
                    $results = $solr->search($query, 0, $limit, $fq);
                } catch (Exception $e) {
                    $this->redirect()->toUrl('/');
                }
            }}
            else
            {
            $limite = 10;
            $resultados = false;
            $palabraBuscar = isset($texto) ? $texto : false;
            $distrito = ($distrito) ? ' AND distrito:' . $distrito : '';
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo' . $distrito
            );
            if ($palabraBuscar == '') {
                $this->redirect()->toUrl('/');
            }
            if ($palabraBuscar) {
                $solar = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $palabraBuscar = stripslashes($palabraBuscar);
                }
                try {
                    $resultados = $solar->search($palabraBuscar, 0, $limite, $fd);
                } catch (Exception $e) {
                    $this->redirect()->toUrl('/');
                }
            }
        
            $limit = 3;
            $palabraBuscar = isset($texto) ? $texto : false;
            $query = "($palabraBuscar)";
            $fq = array(
                'sort' => 'random_' . uniqid() . ' asc',
                'fq' => 'en_estado:activo AND restaurant_estado:activo AND en_destaque:si' . $distrito
            );
            $results = false;
            if ($query) {
        
                $solr = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $query = stripslashes($query);
                }
                try {
                    $results = $solr->search($query, 0, $limit, $fq);
                } catch (Exception $e) {
                    $this->redirect()->toUrl('/');
                }
            }
    }
            $form = new Formularios();
            $listades = $this->getConfigTable()->cantComentxPlato(1, '0,3', 1);
            
            $form->get('q')->setValue($texto);
            $form->get('submit')->setValue('Buscar');
        
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
            $paginator->setCurrentPageNumber((int) $this->params()
                ->fromQuery('page', 1));
            $paginator->setItemCountPerPage(10);
        
              if ($resultados)
                  {
                    $total = (int) $resultados->response->numFound;
                    $end = count($resultados->response->docs)+$start;
                    $inicio = $start+1;    
                  }  
        if ($total <= 10) {
             $mostrar = 'Mostrando ' . $inicio . ' - ' . $end . ' de ' . $total . ' resultados';
        } else {
            $mostrar = 'Mostrando ' . $inicio . ' - ' . $end . ' de ' . $total . ' resultados';
        }
        $datos= $resultados->getRawResponse();// echo Json::encode($datos);
        echo $datos;
        exit();
    }

  
    public function jsonmapasaAction()
    {
        $distrit = $this->params()->fromQuery('distrito');
        $view = new viewModel();
        $view->setTerminal(true);
        $valor1 =explode(',',$distrit);
        $distrito = $valor1[0];
        $texto = $this->params()->fromQuery('q');
        setcookie('distrito', $distrito);
        $valor =explode(" ",$texto);
        if($valor[0]=='restaurante:')
            { $buscar = $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];
            $plato = $valor[0].'"'.$buscar.'"'; }  
         elseif($valor[0]=='tag:')
            { $buscar = $valor[1].' '.$valor[2].' '.$valor[3];
         $plato = $valor[0].'"'.$buscar.'"'; }
             elseif($valor[0]=='name:')
            {$buscar = $valor[1].' '.$valor[2].' '.$valor[3].' '.$valor[4];
          $plato = $valor[0].'"'.$buscar.'"'; }
            else{$filter = new \Zend\I18n\Filter\Alnum(true);
            $plato = $filter->filter($texto);
            setcookie('q', $texto);}
        
        if($distrito and $distrito!='LIMA'){
            $distrit = '"'.$distrito.'"';
            $resultados = false;
            $palabraBuscar = isset($plato) ? $plato : false;
            $list = 1000;
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo AND distrito:' . strtoupper($distrito),
                'fl' => 'id,latitud,longitud,tx_descripcion,va_imagen,restaurante_estado,restaurante,name,plato_tipo,distrito',
                'wt' => 'json'
            );
            if ($palabraBuscar) {
                $solar = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $palabraBuscar = stripslashes($palabraBuscar);
                }
                try {
                    $resultados = $solar->search($palabraBuscar, 0, $list, $fd);
                } catch (Exception $e) {
                    
                    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
                }
            }
        } 

        elseif($distrito=='LIMA'){ 
            $limite = 1000;
            $resultados = false;
            $palabraBuscar = isset($plato) ? $plato : false;
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo AND departamento:' . strtoupper($distrito),
                'fl' => 'id,latitud,longitud,tx_descripcion,va_imagen,restaurante_estado,restaurante,name,plato_tipo,distrito',
                'wt' => 'json'
            );
            
            if ($palabraBuscar) {
                $solar = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $palabraBuscar = stripslashes($palabraBuscar);
                }
                try {
                    $resultados = $solar->search($palabraBuscar, 0, $limite, $fd);
                    // var_dump($resultados);exit;
                } catch (Exception $e) {
                    
                    $this->redirect()->toUrl('/');
                }
            }
        }else{$limite = 1000;
       // $distrito= '"LIMA"';
            $resultados = false;
            $palabraBuscar = isset($plato) ? $plato : false;
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo ',
                'fl' => 'id,latitud,longitud,tx_descripcion,va_imagen,restaurante_estado,restaurante,name,plato_tipo,distrito',
                'wt' => 'json'
            );
            
            if ($palabraBuscar) {
                $solar = \Classes\Solr::getInstance()->getSolr();
                if (get_magic_quotes_gpc() == 1) {
                    $palabraBuscar = stripslashes($palabraBuscar);
                }
                try {
                    $resultados = $solar->search($palabraBuscar, 0, $limite, $fd);
                    // var_dump($resultados);exit;
                } catch (Exception $e) {
                    
                    $this->redirect()->toUrl('/');
                }
              }
           }

       echo $resultados->getRawResponse();
        exit();
    }

    public function rolesAction()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $u = new Album($adapter);
        $s = $u->rolAll($adapter);
        $array = array(
            'hola' => 'desde sql',
            'yea' => $u->rolAll($adapter)
        );
        return new ViewModel($array);
    }

    public function joinAction()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('ta_distrito');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }
    
  
        
    public function joinPlatoAction()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('ta_plato');
        $selectString = $sql->getSqlStringForSqlObject($select);
        // var_dump($selectString);exit;
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        // var_dump($results);exit;
        return $results;
    }

    public function addAction()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $id = (int) $this->params()->fromRoute('in_id', 0);
        // var_dump($id);exit;
        $u = new Album($adapter);
        $array = array(
            'artist' => 'sandra',
            'title' => 'ss'
        );
        $u->deleteAlbum($id);
        
        return new ViewModel($array);
    }

    public function delAction()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $id = (int) $this->params()->fromRoute('in_id', 0);
        $u = new Album($adapter);
        $u->deleteAlbum($id);
        $valores = array(
            'url' => $this->getRequest()->getBaseUrl(),
            'in_id' => $id
        );
        return new ViewModel($valores);
        
        return $this->redirect()->toUrl($this->getRequest()
            ->getBaseUrl() . '/application/index/index');
    }

    public function actualizarusuarioAction()
    {
        /*
         * $id = (int) $this->params()->fromRoute('in_id', 0); if (!$id) { return $this->redirect() ->toUrl($this->getRequest() ->getBaseUrl().'/application/index/actualizarusuario'); } try { $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); $adapter = $this->dbAdapter; $id = (int) $this->params()->fromRoute('in_id', 0); $u = new Album($adapter); $u->obtenerUsuario($id); } catch (\Exception $ex) { return $this->redirect() ->toUrl($this->getRequest() ->getBaseUrl().'/application/index/index'); }
         */
        if ($this->getRequest()->isPost()) {
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $id = (int) $this->params()->fromRoute('in_id', 0);
            $u = new Album($adapter);
            $data = $this->request->getPost();
            $u->updateAlbum($id, $data);
            return $this->redirect()->toUrl($this->getRequest()
                ->getBaseUrl() . '/application/index/actualizarusuario/1');
        } else {
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $id = (int) $this->params()->fromRoute('in_id', 0);
            $u = new Album($adapter);
            $datos = $u->obtenerUsuario($id);
            $form = new Formularios("form");
            $dao = array(
                'nombre' => $datos['va_nombre'],
                'apellido' => $datos['va_apellidos'],
                'pass' => $datos['va_contrasenia'],
                'email' => $datos['va_email'],
                'rol' => $datos['Ta_rol_in_id']
            );
            // var_dump($dao);exit;
            // var_dump($values);exit;
            // $form->populate($values);
            // $va=$form->bind($datos);
            // $form->setAttribute($values);
            $valores = array(
                "titulo" => "Actualizar Usuario",
                "form" => $form,
                'url' => $this->getRequest()->getBaseUrl(),
                'in_id' => $id,
                'ye' => $dao
            );
            return new ViewModel($valores);
        }
    }

    public function agregarusuarioAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $u = new Album($adapter);
            $data = $this->request->getPost();
            $u->addAlbum($data);
            return $this->redirect()->toUrl($this->getRequest()
                ->getBaseUrl() . '/application/index/agregarusuario/1');
        } else {
            $form = new Formularios("form");
            $id = (int) $this->params()->fromRoute('in_id', 0);
            $valores = array(
                "titulo" => "Registro de Usuario",
                "form" => $form,
                'url' => $this->getRequest()->getBaseUrl(),
                'in_id' => $id
            );
            return new ViewModel($valores);
        }
    }

    public function nosotrosAction()
    {
        $view = new ViewModel();
        $comidas = $this->joinAction()->toArray();
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read(); 
        if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; 
        if($facebook['id_facebook']){
        $id_face=$this->getClientesTable()->usuarioface($facebook['id_facebook']); 
                         if(count($id_face)>0)
                         {if($id_face[0]['id_facebook']=='')  
                          { $this->getClientesTable()->idfacebook($id_face[0]['in_id'],$facebook['id_facebook'],$facebook['logoutUrl']);
                          $this->layout()->face = $facebook['name']; }     
                         else{$this->getClientesTable()->idfacebook2($id_face[0]['in_id'],$facebook['logoutUrl']);
                         $this->layout()->face = $facebook['name']; }    }
                         else{$this->getClientesTable()->insertarusuariofacebbok($facebook['name'],$facebook['email'],$facebook['id_facebook'],$facebook['logoutUrl']);  
                         $this->layout()->face = $facebook['name']; 
      } }  }
        $this->layout()->comidas = $comidas;
        // $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Nosotros';
        // $view->setVariables(array());
        // return $view;
    }

    public function solicitaAction()
    {
        $view = new ViewModel();
        $comidas = $this->joinAction()->toArray();
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read(); 
         if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; }
        $this->layout()->comidas = $comidas;
        $this->layout()->clase = 'Solicita';
        $form = new Solicita("form");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $datos = array();
            $datos['nombre_complet'] = htmlspecialchars($this->params()->fromPost('nombre_complet', 0));
            $datos['email'] = htmlspecialchars($this->params()->fromPost('email', 0));
            $datos['nombre_plato'] = htmlspecialchars($this->params()->fromPost('nombre_plato', 0));
            $datos['descripcion'] = htmlspecialchars($this->params()->fromPost('descripcion', 0));
            $datos['nombre_restaurant'] = htmlspecialchars($this->params()->fromPost('nombre_restaurant', 0));
            $datos['telefono'] = htmlspecialchars($this->params()->fromPost('telefono', 0));
            // var_dump($datos);exit;
            $form->setData($datos);
            if ($form->isValid()) {
                $bodyHtml = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                     Nombre <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['nombre_complet']) . '</strong><br />
                                                     Email <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['email']) . '</strong><br />
                                                     Plato <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['nombre_plato']) . '</strong><br />
                                                     Descripcion <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['descripcion']) . '</strong><br />
                                                     Restaurante <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['nombre_restaurant']) . '</strong><br />
                                                     Telefono <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['telefono']) . '</strong><br />
                                              
                                                     </div>
                                               </body>
                                               </html>';
                
                $message = new Message();
                $config = $this->getServiceLocator()->get('Config');
                $message->addTo($config['mail']['transport']['options']['connection_config']['username'], $nombre)
                    ->setFrom($config['mail']['transport']['options']['connection_config']['username'], 'listadelsabor.com')
                    ->setSubject('Solicitar platos de listadelsabor.com');
                // ->setBody($bodyHtml);
                $bodyPart = new \Zend\Mime\Message();
                $bodyMessage = new \Zend\Mime\Part($bodyHtml);
                $bodyMessage->type = 'text/html';
                $bodyPart->setParts(array(
                    $bodyMessage
                ));
                $message->setBody($bodyPart);
                $message->setEncoding('UTF-8');
                
                $transport = $this->getServiceLocator()->get('mail.transport'); // new SendmailTransport();//$this->getServiceLocator('mail.transport')
                $transport->send($message);
                $this->flashMessenger()->addMessage('Su mensaje ha sido enviado...');
                $this->redirect()->toUrl('/solicita');
            }
        }
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $mensajes = $flashMessenger->getMessages();
        }
        $view->setVariables(array(
            'form' => $form,
            'mensaje' => $mensajes
        ));
        return $view;
    }

    
    public function comidas()
    {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from('ta_tipo_comida')
                ->order('va_nombre_tipo ASC');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
            
     }
      public function ingresardatosAction()
    {
        $view = new ViewModel();
       $this->layout()->clase = 'Solicita';
        $form = new Registro("form");
      $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read(); 
         if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; 
        if($facebook['id_facebook']){
        $id_face=$this->getClientesTable()->usuarioface($facebook['id_facebook']); 
                         if(count($id_face)>0)
                         {if($id_face[0]['id_facebook']=='')  
                          { $this->getClientesTable()->idfacebook($id_face[0]['in_id'],$facebook['id_facebook'],$facebook['logoutUrl']);
                          $this->layout()->face = $facebook['name']; }     
                         else{$this->getClientesTable()->idfacebook2($id_face[0]['in_id'],$facebook['logoutUrl']);
                         $this->layout()->face = $facebook['name']; }    }
                         else{$this->getClientesTable()->insertarusuariofacebbok($facebook['name'],$facebook['email'],$facebook['id_facebook'],$facebook['logoutUrl']);  
                         $this->layout()->face = $facebook['name']; 
      } }  }
        $comidas =  $this->comidas()->toArray();
        $com = array();
        foreach($comidas as $y){
            $com[$y['in_id']] = $y['va_nombre_tipo'];}
        $form->get('Ta_tipo_comida_in_id')->setValueOptions($com);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $datos =$this->request->getPost();
           // var_dump($datos->cantidad_platos);exit;
            $File = $this->params()->fromFiles('va_imagen');
            $form->setData($datos);
            if ($form->isValid()) {
                $valor  = uniqid();
                $info =  pathinfo($File['name']);
                 require './vendor/Classes/Filter/Alnum.php';
                 if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg'){  
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($datos->va_nombre_contacto);
                  $name = $filtered.'-'.$imf2;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);                  
                       $copia = $this->_options->upload->images . '/registro/restaurante/' . $name;       
                       imagejpeg($viejaimagen,$copia);
                  }
               $idrestaurante = $this->getConfigTable()->guardarregistro($datos,$name);
               $id =$idrestaurante;
               $this->flashMessenger()->addMessage('El restaurante ha sido registrado correctamente...');
                $this->redirect()->toUrl('/solicita?id='.$id.'&plato='.$datos->cantidad_platos);
                
            }
        }
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $mensajes = $flashMessenger->getMessages();
        }
        $formu = new Registroplato();
        $view->setVariables(array(
            'form' => $form,
             'formu' => $formu,
            'mensaje' => $mensajes
        ));
      return $view;
    }
    public function ingresarplatosAction()
    {
        $view = new ViewModel();
        $this->layout()->clase = 'Solicita';
        $request = $this->getRequest();
        if ($request->isPost()) {
           $datos =$this->request->getPost();
                $returnArray=array();
        foreach ($datos as $result) {
            $returnArray[] = $result;}
         

            
          $con = count($datos)/4;
          for($i=1;$i<=$con;$i++){
               $File = $this->params()->fromFiles('va_imagen'.$i);
                $info =  pathinfo($File['name']);
                // require './vendor/Classes/Filter/Alnum.php';
                 if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  { $imf2 =  $info['extension']; 
                  $name='plato'.$i.'-'.$datos->Ta_registro_in_id1.'.'.$imf2;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);                  
                       $copia = $this->_options->upload->images . '/registro/plato/' . $name;       
                       imagejpeg($viejaimagen,$copia);
                 }   
          }  
                     
               $this->getConfigTable()->guardarplatoregistro($datos);        
            $this->redirect()->toUrl('/solicita?m=1');
        }

        return $view;
    }


    public function contactenosAction()
    {
        $view = new ViewModel();
        $comidas = $this->joinAction()->toArray();
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read(); 
         if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; }
        $this->layout()->comidas = $comidas;
        $this->layout()->clase = 'Solicita';
        $form = new Contactenos("form");
        $request = $this->getRequest();
        if ($request->isPost()) {         
            $datos = array();
            $datos['nombre'] = htmlspecialchars($this->params()->fromPost('nombre', 0));
            $datos['email'] = htmlspecialchars($this->params()->fromPost('email', 0));
            $datos['asunto'] = htmlspecialchars($this->params()->fromPost('asunto', 0));
            $datos['mensaje'] = htmlspecialchars($this->params()->fromPost('mensaje', 0));
            // $form->setInputFilter(new \Application\Form\ContactenosFiltro());
            $form->setData($datos);
            if ($form->isValid()) {
                
                $bodyHtml = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                     Nombre <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['nombre']) . '</strong><br />
                                                     Email <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['email']) . '</strong><br />
                                                     Asunto <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['asunto']) . '</strong><br />
                                                     Mensaje <strong style="color:#133088; font-weight: bold;">' . utf8_decode($datos['mensaje']) . '</strong><br />
                                              
                                                     </div>
                                               </body>
                                               </html>';
                
                $message = new Message();
                $config = $this->getServiceLocator()->get('Config');
                
                $message->addTo($config['mail']['transport']['options']['connection_config']['username'], $datos['nombre'])
                    ->setFrom($config['mail']['transport']['options']['connection_config']['username'], 'listadelsabor.com')
                    ->setSubject('Contactos de ListaDelSabor.com');
                // ->setBody($bodyHtml);
                $bodyPart = new \Zend\Mime\Message();
                $bodyMessage = new \Zend\Mime\Part($bodyHtml);
                $bodyMessage->type = 'text/html';
                $bodyPart->setParts(array(
                    $bodyMessage
                ));
                $message->setBody($bodyPart);
                $message->setEncoding('UTF-8');
                
                $transport = $this->getServiceLocator()->get('mail.transport'); // new SendmailTransport();
                $transport->send($message);
                $this->flashMessenger()->addMessage('Su mensaje ha sido enviado...');
                $this->redirect()->toUrl($this->getRequest()
                    ->getBaseUrl() . '/contactenos');
                // $this->redirect()->toUrl('/contactenos');///application/index
            }
        }
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $mensajes = $flashMessenger->getMessages();
        }
        $view->setVariables(array(
            'form' => $form,
            'mensaje' => $mensajes
        ));
        return $view;
    }

    public function terminosAction()
    {
        $view = new ViewModel();
        $comidas = $this->joinAction()->toArray();
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read(); 
       if (!isset($session)) {
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; 
        if($facebook['id_facebook']){
        $id_face=$this->getClientesTable()->usuarioface($facebook['id_facebook']); 
                         if(count($id_face)>0)
                         {if($id_face[0]['id_facebook']=='')  
                          { $this->getClientesTable()->idfacebook($id_face[0]['in_id'],$facebook['id_facebook'],$facebook['logoutUrl']);
                          $this->layout()->face = $facebook['name']; }     
                         else{$this->getClientesTable()->idfacebook2($id_face[0]['in_id'],$facebook['logoutUrl']);
                         $this->layout()->face = $facebook['name']; }    }
                         else{$this->getClientesTable()->insertarusuariofacebbok($facebook['name'],$facebook['email'],$facebook['id_facebook'],$facebook['logoutUrl']);  
                         $this->layout()->face = $facebook['name']; 
      } }  }
        $this->layout()->comidas = $comidas;
        // $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Terminos';}
      public function getClientesTable() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
        }
        return $this->clientesTable;
    }
     public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }

        return $this->authservice;
    }
      public function getRestauranteTable() {
        if (!$this->restauranteTable) {
            $sm = $this->getServiceLocator();
            $this->restauranteTable = $sm->get('Restaurante\Model\RestauranteTable');
        }
        return $this->restauranteTable;
    }
    
    
}
