<?php
namespace Restaurante\Form;

use Zend\Form\Form;
class MenuForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('menu');
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
            'name' => 'va_nombre',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Nombre de la pestania :',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'va_nombre',
                'placeholder'=>'Ingrese el nombre de la pestania'
            ),
        ));
        
         $this->add(array(
            'name' => 'in_orden',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Ingrese el orden a mostrar :',          
            ),
            'attributes' => array(               
                'class' => 'span3  ',
                'id'   => 'in_orden',
                'placeholder'=>'Orden'
            ),
        ));
      $this->add(array(
            'name' => 'va_url',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Ruta de la Url :',          
            ),
            'attributes' => array(               
                'class' => 'span14 ',
                'id'   => 'va_nombre',
                'placeholder'=>'Ingrese la url'
            ),
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