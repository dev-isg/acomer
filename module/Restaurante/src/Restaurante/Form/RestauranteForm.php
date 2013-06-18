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
            'name' => 'name_rest',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Nombre del Restaurante',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'name_rest',
                'placeholder'=>'Ingrese nombre del restaurante'
            ),
        ));
        $this->add(array(
            'name' => 'raz_rest',
            'type' => 'Text',
              'attributes' => array(               
                'class' => 'span10',
                'id'   => 'raz_rest',
                'placeholder'=>'Ingrese la Razon Social'
            ),
            'options' => array(
                'label' => 'Razon Social',
            ),
        ));
        $this->add(array(
            'name' => 'web_rest',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'web_rest',
                'placeholder'=>'Ingrese su página Web'
            ),
            'options' => array(
                'label' => 'Página Web',
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen',
            'type' => 'File',
              'attributes' => array(               
                'class' => '',
                'id'   => '',
                'placeholder'=>'Ingrese su página Web'
            ),
            'options' => array(
                'label' => 'Imagen : ',
            ),
        ));
        
       // $echo = new IndexController();
       //$echo->rolesAction();
        $this->add(array(
            'name' => 'esp_rol',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'esp_rol'
            ),
           'options' => array(
                     'label' => 'Especialidad',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Criolla',
                             '2' => 'Marina',                   
                     ),
             )
        ));
        
        
  
     
        
        $this->add(array(
            'name' => 'ruc_rest',
            'type' => 'Text',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'ruc_rest',
                 'placeholder'=>'Repita su Ruc'
            ),
            'options' => array(
                'label' => 'Ruc',
            ),
        ));
        
        $this->add(array(
            'name' => 'va_modalidad',
            'type' => 'MultiCheckbox',
             'attributes' => array(               
                'class' => 'checkbox inline',
                'id'   => 'check-mod',
                 'placeholder'=>'Ingrese su modalidad de pago'
            ),
            'options' => array(
                     'label' => 'Modalidad de Pago?',
                     'value_options' => array(
                             '1' => 'visa',
                             '2' => 'mastercard',                
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