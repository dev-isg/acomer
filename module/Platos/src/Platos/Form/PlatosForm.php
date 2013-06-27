<?php
namespace Platos\Form;

use Zend\Form\Form;
use Platos\Controller\IndexController;


class PlatosForm extends Form
{
    public function __construct($name = null)
    {
              // we want to ignore the name passed
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
            'name' => 'ta_usuario_in_id',
            'type' => 'Hidden',
           'attributes' => array(               
                'id'   => 'ta_usuario_in_id',         
            ),
        ));
              
              
        $this->add(array(
            'name' => 'ta_puntaje_in_id',
            'type' => 'Hidden',
           'attributes' => array(               
                'id'   => 'ta_puntaje_in_id',         
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
            'name' => 'de_precio',
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
            'name' => 'en_destaque',
            'type' => 'MultiCheckbox',
           // 'label' => 'Modalidad de Pago?',
             'attributes' => array(               
                'class' => 'checkbox inline',
                'id'   => 'en_destaque',
                 'placeholder'=>'Ingrese su destaque'
            ),
            'options' => array(
                     
                     'value_options' => array(
                         '0'=>'hola'
                     ),
             )
        ));
          
               
        $this->add(array(
            'name' => 'Ta_tipo_plato',
            'type' => 'Select',  
            
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'Ta_tipo_plato'
            ),
           'options' => array('label' => 'Tipo de Plato : ',
                     'value_options' => array(
                         
                          '' => 'selecccione :',
              ),
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
}
