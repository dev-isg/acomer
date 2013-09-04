<?php
namespace Platos\Form;

use Zend\Form\Form;
//use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\AdapterInterface;
//use Platos\Controller\IndexController;
//use Zend\Db\Adapter\Adapter;
//use Zend\ServiceManager\ServiceLocatorAwareInterface,
//    Zend\ServiceManager\ServiceLocatorInterface;

//use Zend\Form\Form;
//use Zend\Db\Adapter\AdapterInterface;


class PlatosForm extends Form
{
    protected $dbAdapter;
    protected $idplato;
     public function __construct(AdapterInterface $dbAdapter,$id)//$name = null,
    {
              // we want to ignore the name passed
        $this->setDbAdapter($dbAdapter);
        $this->setId($id);
        parent::__construct('platos');
        $this->setAttribute('method', 'post');
        $this->setAttribute('endtype', 'multipart/form-data');
        
        
       $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
           'attributes' => array(               
                'id'   => 'in_id',         
            ),
        ));
              
        $this->add(array(
            'name' => 'Ta_usuario_in_id',
            'type' => 'Hidden',
           'attributes' => array(               
                'id'   => 'Ta_usuario_in_id',         
            ),
        ));
              
              
        $this->add(array(
            'name' => 'Ta_puntaje_in_id',
            'type' => 'Hidden',
           'attributes' => array(               
                'id'   => 'Ta_puntaje_in_id',         
            ),
        ));
        
                $this->add(array(
            'name' => 'en_estado',
            'type' => 'Hidden',
           'attributes' => array(               
                'id'   => 'en_estado',         
            ),
        ));
       
  
        $this->add(array(
            'name' => 'va_imagen',
            'type' => 'File',
              'attributes' => array(               
                'class' => '',
                'id'   => 'va_imagen',
                'placeholder'=>'Ingrese su página Web'
            ),
            'options' => array(
                'label' => 'Agregar Imagen : ',
            ),
        ));
        
        
          $this->add(array(
            'name' => 'tx_descripcion',
            'type' => 'Textarea',
            'attributes' => array(               
                'class' => 'span11',
                'id'   => 'tx_descripcion',
                'placeholder'=>'Ingrese descripción',
                'colls'=>40,
                'rows'=>4
            ),
            'options' => array(
                'label' => 'Descripción',
            ),
        ));
           
          
         $this->add(array(
            'name' => 'va_nombre',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Nombre del Plato',          
            ),
            'attributes' => array(               
                'class' => 'span11',
                'id'   => 'va_nombre',
                'placeholder'=>'Ingrese nombre del Plato'
            ),
        ));  
          
         
          $this->add(array(
            'name' => 'va_precio',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'de_precio',
                'placeholder'=>'Ingrese el precio'
            ),
            'options' => array(
                'label' => 'Precio',
            ),
        ));
          
        $this->add(array(
            'name' => 'va_otros',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_otros'
            ),
            'options' => array(
                'label' => 'ingrese nuevo tipo de plato',
            ),
        ));
          
          //el problema NO DESCOMENTAR

        $this->add(array(
            'name' => 'va_promocion',
            'type' => 'MultiCheckbox',
             'attributes' => array(               
                'class' => 'checkbox inline',
                'id'   => 'va_mistura'
            ),
            'options' => array(
                     'value_options' => $this->promocion()
             )
        ));
          
               
        $this->add(array(
            'name' =>'Ta_tipo_plato_in_id',// 'ta_tipo_plato',
            'type' => 'Select',  
            
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'Ta_tipo_plato_in_id'//'ta_tipo_plato'
            ),
           'options' => array('label' => 'Tipo de Plato : ',
                     'value_options' => 
               $this->tipoPlato(),
               //array(
//                   '0' => 'selecccione :',
//                   '1'=>'arroz con papa',
              //),
//               'empty_option'  => '--- Seleccionar ---'
             )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Guardar',
                'class' => 'btn btn-success',
                'id' => 'submitbutton',
            ),
        ));

    }
    
    
   public function tipoPlato()
        {   
           
           $idpla=$this->getId();
//     var_dump($idpla);
       $this->dbAdapter =$this->getDbAdapter();//getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from('ta_tipo_plato')
            ->join(array('ttc'=>'ta_tipo_comida'), 'ttc.in_id = ta_tipo_plato.ta_tipo_comida_in_id', array(), 'left')
            ->join(array('tr'=>'ta_restaurante'), 'tr.ta_tipo_comida_in_id = ttc.in_id', array(), 'left')
            ->join(array('tl'=>'ta_local'), 'tr.in_id = tl.ta_restaurante_in_id', array(), 'left')                 
            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_local_in_id = tl.in_id', array(), 'left')
            ->join(array('tpl'=>'ta_plato'), 'tpl.in_id = pl.ta_plato_in_id', array(), 'left')
            ->where(array('tl.in_id'=>$idpla));//->where(array('tr.in_id'=>$idpla));
 
            $selectString = $sql->getSqlStringForSqlObject($select);
//            var_dump($selectString);exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $tiplatos=$results->toArray();
            
//        $auxtipo = array('0'=>'otros');
 
        foreach($tiplatos as $tipo){
            $auxtipo[$tipo['in_id']] = $tipo['va_nombre'];      
        }
//            $auxtipo['0']='otros';
//            $auxres=  array_merge(array(0=>'Otros'),$auxtipo);
//        var_dump($auxtipo);Exit;
            return $auxtipo;//$auxres;//        
  }
     
   public function promocion($id=null){
       $this->dbAdapter =$this->getDbAdapter();
       $adapter = $this->dbAdapter;
        
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_tag')->order('ta_tag.va_nombre asc');
       if($id!=null){
            $selecttot ->where(array('ta_tag.in_id='=>$id))->order('ta_tag.va_nombre DESC');       
       }
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $promocion=$results->toArray();
        $promo = array();
        foreach($promocion as $arrpro){
            $promo[$arrpro['in_id']] = $arrpro['va_nombre'];
        }
        
        return $promo;
    }
     
         public function setDbAdapter(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }
    
    public function setId($id){
        $this->idplato=$id;
        return $this;
    }
    
        public function getId()
    {
        return $this->idplato;
    }
}
