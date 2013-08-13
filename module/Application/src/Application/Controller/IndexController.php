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
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Application\Model\Entity\Album;
use Zend\Mail\Message;
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

    public $dbAdapter;
public function __construct()
	{
		$this->_options = new \Zend\Config\Config ( include APPLICATION_PATH . '/config/autoload/global.php' );
	}
    public function indexAction()
    {
        $view = new ViewModel();
        
        $comidas = $this->joinAction()->toArray();
        $this->layout()->comidas = $comidas;
        $listatot = $this->getConfigTable()->cantComentxPlato(1, null, 1);
        $listatot = $listatot->toArray();
        
        foreach ($listatot as $key => $value) {
            if ($key < 3) {
                $listades[] = $listatot[$key];
            } else {
                $listadeseg[] = $listatot[$key];
            }
        }
        
        $listaval = $this->getConfigTable()->cantComentxPlato(2, 3, 3);
        $listault = $this->getConfigTable()->cantComentxPlato(2, 3, 2);
        $this->layout()->clase = 'Home';
        $view->setVariables(array(
            'lista' => $listades,
            'listaseg' => $listadeseg,
            'listaval' => $listaval,
            'listault' => $listault,
            'clase' => 'Home'
        ));
        return $view;
        
        //
    }

    public function jsondestaAction()
    {
        $listades = $this->getConfigTable()->cantComentxPlato(1, '0,3', 1);
        $valor = Json::encode($listades);
        echo $valor;
        exit();
    }
    // public function equipoAction()
    // {
    //
    // require './vendor/Classes/Mobile_Detect.php';
    // $detect = new \Mobile_Detect;
    // if($detect->isiPad())
    // {echo 'es un ipad';exit;}
    // }
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
    public function detalleubicacionAction()
    {
        $view = new ViewModel();
        
        $request = $this->getRequest();
        $this->layout()->clase = 'buscar-distrito';
        if ($request->isGet()) {
            $datos = $this->request->getQuery();
            $plato = $datos['q'];
            $filter = new \Zend\I18n\Filter\Alnum(true);
            $texto = $filter->filter($plato);
            $distrito = $datos['distrito'];
            $ruta = $this->_options->data->busqueda .'/busqueda.txt';
            $fp = fopen($ruta,"a");
            fwrite($fp, "PLATO BUSCADO: $texto \t DISTRITO: $distrito" . PHP_EOL);
            fclose($fp); 
            if ($texto == '') {
                $this->redirect()->toUrl('/');
            }
            if ($texto == '') {
                $this->redirect()->toUrl('/');
            }
            if ($distrito != 'TODOS LOS DISTRITOS') {
                $limite = 100;
                $resultados = false;
                $palabraBuscar = isset($texto) ? $texto : false;
                $fd = array(
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND distrito:' . $distrito,
//                     'sort' => 'en_destaque desc '
                );
                
                $solar = \Classes\Solr::getInstance()->getSolr();
                if ($palabraBuscar) {
                    $solar = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $palabraBuscar = stripslashes($palabraBuscar);
                    }
                    try {
                        $resultados = $solar->search($palabraBuscar, 0, $limite, $fd);
                    } catch (Exception $e) {
                        echo ("<div>ingrese algun valor</div>");
                    }
                }
                $limit = 3;
                $palabraBuscar = isset($texto) ? $texto : false;
                $query = "($palabraBuscar) AND (en_destaque:si)";
                $fq = array(
                    'sort' => 'random_' . uniqid() . ' asc',
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND distrito:' . $distrito,
                    'wt' => 'json'
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
                        echo ("<div>ingrese algun valor</div>");
                    }
                }
            } else {
                $limite = 100;
                $resultados = false;
                $palabraBuscar = isset($texto) ? $texto : false;
                $fd = array(
                    'fq' => 'en_estado:activo AND restaurant_estado:activo'
                );
                
                if ($palabraBuscar) {
                    $solar = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $palabraBuscar = stripslashes($palabraBuscar);
                    }
                    try {
                        $resultados = $solar->search($palabraBuscar, 0, $limite, $fd);
                    } catch (Exception $e) {
                        
                        $this->redirect()->toUrl('/application');
                    }
                }
                $limit = 3;
                $palabraBuscar = isset($texto) ? $texto : false;
                $query = "($palabraBuscar)";
                $fq = array(
                    'sort' => 'random_' . uniqid() . ' asc',
                    'fq' => 'en_estado:activo AND restaurant_estado:activo  AND en_destaque:si'
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
                        
                        $this->redirect()->toUrl('/application');
                    }
                }
            }
        }
        $form = new Formularios();
        $comidas = $this->joinAction()->toArray();
        $this->layout()->comidas = $comidas;
        $com = array();
        foreach ($comidas as $y) {
            $com[$y['va_distrito']] = $y['va_distrito'];
        }
        setcookie('q', $texto);
        setcookie('distrito', $distrito);
        // var_dump($_COOKIE['distrito']);exit;
        // $form->get('distrito')->setValue($_COOKIE['distrito']);
        $form->get('distrito')->setValue($distrito);
        $form->get('q')->setValue($texto);
        
        $form->get('distrito')->setValueOptions($com);
        $form->get('submit')->setValue('Buscar');
        // $total = (int) $resultados->response->numFound;
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);
        
        $total = $paginator->getTotalItemCount();
        $first = $paginator->getPages()->firstItemNumber;
        $last = $paginator->getPages()->lastItemNumber;
        if ($total == 1) {
            $mostrar = 'Mostrando ' . $first . ' de ' . $total . ' resultado';
        } else {
            $mostrar = 'Mostrando ' . $first . '-' . $last . ' de ' . $total . ' resultados';
        }
        
        $listades = $this->getConfigTable()->cantComentxPlato(1, '0,3', 1);
        $view->setVariables(array(
            'total' => $total,
            'distrito' => $distrito,
            'plato' => $texto,
            'lista' => $listades,
            'destacados' => $results->response->docs,
            'general' => $paginator,
            'form' => $form,
            'mostrar' => $mostrar,
            'nombre' => $texto
        )); // ,'error'=>$error
        return $view;
    }
    
    // FUNCION SOLO PARA MOVILES ,AQUI A QUE PARTIR EL VALOR DEL fromQuery PARA
    public function consultaDistrito($distrito)
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('ta_distrito')->where->like('va_distrito', '%' . $distrito . '%')->count();
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function verAction()
    {
        $view = new ViewModel();
        
        $this->layout()->clase = 'buscar';
        $filtered = $this->params()->fromQuery('q');
        $filtered = strtoupper($filtered);
        $filter = new \Zend\I18n\Filter\Alnum(true);
        
        $text = trim($filter->filter($filtered));
        $text = preg_replace('/\s\s+/', ' ', $text);
        $busqueda = explode(" EN ", $text);
//         $distritos = $this->joinAction()->toArray();
        
        // for($f=0;$f<count($distritos);$f++){
        
        // for($i=0;$i<count($busqueda);$i++){
        // if($busqueda[1]==$distritos[$f]['va_distrito'])//trim($busqueda[$i])
        // {
        // $distrito = $distritos[$f]['va_distrito'];
        
        // }else{
        // $texto = $busqueda[0];
        // }
        // }
        // }
        
        if($this->consultaDistrito($busqueda[1])>0){
            $distrito=$busqueda[1];   
        }
        $texto = $busqueda[0];
        $ruta = $this->_options->data->busqueda .'/busqueda_movil.txt';
        $fp = fopen($ruta,"a");
        fwrite($fp, "PLATO BUSCADO: $texto \t DISTRITO: $distrito" . PHP_EOL);
        fclose($fp);
//         var_dump($texto);
//         var_dump($distrito);Exit;
        $limite = 100;
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
//         var_dump($distrito);exit;
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
//         var_dump($results->response->docs);Exit;
        $form = new Formularios();
        $listades = $this->getConfigTable()->cantComentxPlato(1, '0,3', 1);
        setcookie('q', $text);
        $form->get('q')->setValue($text);
        $form->get('submit')->setValue('Buscar');
        
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);
        
        $total = $paginator->getTotalItemCount();
        $first = $paginator->getPages()->firstItemNumber;
        $last = $paginator->getPages()->lastItemNumber;
        if ($total == 1) {
            $mostrar = 'Mostrando ' . $first . ' de ' . $total . ' resultado';
        } else {
            $mostrar = 'Mostrando ' . $first . '-' . $last . ' de ' . $total . ' resultados';
        }
//    var_dump($results->response->docs);exit;
        $view->setVariables(array(
            'total' => $total,
            'lista' => $listades,
            'destacados' => $results->response->docs,
            'general' => $paginator,
            'form' => $form,
            'nombre' => $text,
            'mostrar' => $mostrar
        ));
        return $view;
    }

    public function unoAction()
    {
        $view = new viewModel();
        $view->setTerminal(true);
        
        $filtered = $this->params()->fromQuery('q');
        $filtered = strtoupper($filtered);
        $filter = new \Zend\I18n\Filter\Alnum(true);
        $text = trim($filter->filter($filtered));
        $text = preg_replace('/\s\s+/', ' ', $text);
        $busqueda = explode(" EN ", $text);
            if($this->consultaDistrito($busqueda[1])>0){
                $distrito=$busqueda[1];
            }
            $texto = $busqueda[0];
            $limite = 100;
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
        
            $form = new Formularios();
            $listades = $this->getConfigTable()->cantComentxPlato(1, '0,3', 1);
            setcookie('q', $texto);
            $form->get('q')->setValue($texto);
            $form->get('submit')->setValue('Buscar');
        
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($resultados->response->docs));
            $paginator->setCurrentPageNumber((int) $this->params()
                ->fromQuery('page', 1));
            $paginator->setItemCountPerPage(10);
        
            $total = $paginator->getTotalItemCount();
            $first = $paginator->getPages()->firstItemNumber;
            $last = $paginator->getPages()->lastItemNumber;
            if ($total == 1) {
                $mostrar = 'Mostrando ' . $first . ' de ' . $total . ' resultado';
            } else {
                $mostrar = 'Mostrando ' . $first . '-' . $last . ' de ' . $total . ' resultados';
            }
        
        $datos= $resultados->getRawResponse();// echo Json::encode($datos);
        echo $datos;
        exit();
    }

    public function jsonmapaAction()
    {
        $distrito = $this->params()->fromQuery('distrito');
        $view = new viewModel();
        $view->setTerminal(true);
        

        
        echo $resultados->getRawResponse();
        exit();
    }

    public function jsonmapasaAction()
    {
        $distrito = $this->params()->fromQuery('distrito');
        $view = new viewModel();
        $view->setTerminal(true);
        $texto = $this->params()->fromQuery('q');
        setcookie('distrito', $distrito);
        setcookie('q', $texto);
        $filter = new \Zend\I18n\Filter\Alnum(true);
        $plato = $filter->filter($texto);
        
        if ($distrito != 'TODOS LOS DISTRITOS') {
            
            $resultados = false;
            $palabraBuscar = isset($plato) ? $plato : false;
            $list = 1000;
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo AND distrito:' . $distrito,
                //'sort' => 'en_destaque desc',
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
//                if ($resultados == '') 
//
//                {
//                    echo 'error en busqueda';
//                    exit();
//                } else {
//                    echo $resultados->getRawResponse();
//                    exit();
//                }
            }
        } 

        else {
            $limite = 1000;
            $resultados = false;
            $palabraBuscar = isset($plato) ? $plato : false;
            $fd = array(
                'fq' => 'en_estado:activo AND restaurant_estado:activo'
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
            
//            $limit = 3;
//            $palabraBuscar = isset($plato) ? $plato : false;
//            $query = "($palabraBuscar) AND (en_destaque:si)";
//            $fq = array(
//                'sort' => 'random_' . uniqid() . ' asc',
//                'fq' => 'en_estado:activo AND restaurant_estado:activo'
//            );
//            $results = false;
//            if ($query) {
//                $solr = \Classes\Solr::getInstance()->getSolr();
//                if (get_magic_quotes_gpc() == 1) {
//                    $query = stripslashes($query);
//                }
//                try {
//                    $results = $solr->search($query, 0, $limit, $fq);
//                } catch (Exception $e) {
//                    
//                    $this->redirect()->toUrl('/');
//                }
//            }
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
        // var_dump($selectString);exit;
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        // var_dump($results);exit;
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
        // $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Nosotros';
        // $view->setVariables(array());
        // return $view;
    }

    public function solicitaAction()
    {
        $view = new ViewModel();
        // $this->layout('layout/layout-portada');
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
                $this->flashMessenger()->addMessage('Su mensaje a sido enviado...');
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

    public function contactenosAction()
    {
        $view = new ViewModel();
        
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
                $this->flashMessenger()->addMessage('Su mensaje a sido enviado...');
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
        // $this->layout('layout/layout-portada');
        $this->layout()->clase = 'Terminos';
    }
}
