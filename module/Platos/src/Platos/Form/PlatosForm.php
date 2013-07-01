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
     public function __construct(AdapterInterface $dbAdapter,$name = null)
    {
              // we want to ignore the name passed
        $this->setDbAdapter($dbAdapter);
        
        parent::__construct('platos222');
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
                'placeholder'=>'Ingrese descripcion',
                'colls'=>40,
                'rows'=>4
            ),
            'options' => array(
                'label' => 'Descripcion',
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
          
          //el problema NO DESCOMENTAR

//        $this->add(array(
//            'name' => 'en_destaque',
//            'type' => 'MultiCheckbox',
//           // 'label' => 'Modalidad de Pago?',
//             'attributes' => array(               
//                'class' => 'checkbox inline',
//                'id'   => 'en_destaque',
//                 'placeholder'=>'Ingrese su destaque'
//            ),
//            'options' => array(
//                     
//                     'value_options' => array(
//                         '0'=>'hola'
//                     ),
//             )
//        ));
          
               
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
            ->where(array('tr.in_id'=>3));
   
            $selectString = $sql->getSqlStringForSqlObject($select);
//            var_dump($selectString);exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $tiplatos=$results->toArray();
            
        $auxtipo = array();
        
        foreach($tiplatos as $tipo){
            $auxtipo[$tipo['in_id']] = $tipo['va_nombre'];      
        }
            $auxtipo['0']='otros';
//        $result= array_merge($auxtipo[0],$auxtipo);
            return $auxtipo;
            
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
}
