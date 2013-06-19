<?php
namespace Restaurante\Form;

use Zend\Form\Form;
use Restaurante\Controller\IndexController;


class RestauranteForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('restaurante');
        $this->setAttribute('method', 'post');
       $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Nombre del Restaurante',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'va_nombre',
                'placeholder'=>'Ingrese nombre del restaurante'
            ),
        ));
        $this->add(array(
            'name' => 'va_razon_social',
            'type' => 'Text',
              'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_razon_social',
                'placeholder'=>'Ingrese la Razon Social'
            ),
            'options' => array(
                'label' => 'Razon Social',
            ),
        ));
        $this->add(array(
            'name' => 'va_web',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_web',
                'placeholder'=>'Ingrese su página Web'
            ),
            'options' => array(
                'label' => 'Página Web',
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen',
            'type' => 'text',
              'attributes' => array(               
                'class' => '',
                'id'   => 'va_imagen',
                'placeholder'=>'Ingrese su página Web'
            ),
            'options' => array(
                'label' => 'Imagen : ',
            ),
        ));
        
       // $echo = new IndexController();
       //$echo->rolesAction();

        $this->add(array(
            'name' => 'Ta_tipo_comida_in_id',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'Ta_tipo_comida_in_id'
            ),
           'options' => array(
                     'label' => 'Especialidad',
                     'value_options' => array(
                          '' => 'selecccione :',
                         
                                               
                     ),
             )
        ));
        
        
  
     
        
        $this->add(array(
            'name' => 'va_ruc',
            'type' => 'Text',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_ruc',
                 'placeholder'=>'Repita su Ruc'
            ),
            'options' => array(
                'label' => 'Ruc',
            ),
        ));
        
        $this->add(array(
            'name' => 'va_modalidad',
            'type' => 'MultiCheckbox',
            'Label' => 'va_modalidad',
             'attributes' => array(               
                'class' => 'checkbox inline',
                'id'   => 'va_modalidad',
                 'placeholder'=>'Ingrese su modalidad de pago'
            ),
            'options' => array(

                     'value_options' => array(
                         '' => 'selecccione :',
                     ),
             )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'class' => 'btn btn-success',
                'id' => 'submitbutton',
            ),
        ));
    }
}