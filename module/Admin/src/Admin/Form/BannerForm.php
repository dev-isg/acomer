<?php
namespace Admin\Form;

use Zend\Form\Form;
class BannerForm extends Form
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
                'label' => 'Nombre de la pestania',          
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
                'label' => 'Orden',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'in_orden',
                'placeholder'=>'Ingrese el orden a mostrar'
            ),
        ));
     
         
         $this->add(array(
            'name' => 'va_imagen',
            'type' => 'File',
              'attributes' => array(               
                'class' => '',
                'id'   => 'va_imagen',
                'placeholder'=>'Ingrese su imagen'
            ),
            'options' => array(
                'label' => 'Agregar Imagen : ',
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