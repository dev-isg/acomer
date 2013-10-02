<?php
namespace Restaurante\Form;

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
                'label' => 'Nombre del banner :',          
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
                'class' => 'span2  ',
                'id'   => 'in_orden',
                'placeholder'=>'Orden'
            ),
        ));
     
         
         $this->add(array(
            'name' => 'va_imagen',
            'type' => 'File',          
             'validators' => array(
               array(
                        'name'    => 'filemimetype',
                      //  'options' =>  array('mimeType' => 'image/png,image/x-png,image/jpg,image/gif,image/jpeg'),
                        'options' =>  array('mimeType' => 'image/jpg,image/jpeg'),
                    ),
                    array(
                        'name'    => 'filesize',
                        'options' =>  array('max' => 204800),
                       
                    ),
                  ),
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
            'name' => 'va_url',
            'type' => 'text',
              'attributes' => array(               
                'class' => 'span12',
                'id'   => 'va_imagen',
                'placeholder'=>'Ingrese la url'
            ),
            'options' => array(
                'label' => 'Agregar url : ',
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