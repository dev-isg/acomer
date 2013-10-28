<?php
namespace SanAuth\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;


class UpdatepassForm extends Form
{
     public function __construct($name = null)
    {
        parent::__construct('UpdatePass');
        $this->setAttribute('method', 'post');
        $this->setAttribute('endtype', 'multipart/form-data');
   
         $this->add(array(
            'name' => 'va_contrasena',
            'type' => 'password',
//            'options' => array(
//                'label' => 'Nombre de usario:',          
//            ),
            'attributes' => array(               
                'class' => 'form-control',
                'id' => 'inputPassword',
                'placeholder'=>'Ingrese su nueva contraseña'
            ),
        ));  
         
         $this->add(array(
             'name' => 'verificar_contrasena',
             'type' => 'password',
         
             'options' => array(
                 'label' => '',
             ),
             'attributes' => array(
                 'class' => 'form-control',
                 'id' => 'verificar_contrasena',
                 'placeholder'=>'Confirme la contraseña…'
             ),
         ));
         

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Cambiar',
                'class' => 'btn btn-bricky pull-right btnUpPass'
            ),
        ));
        $this->setInputFilter($this->validadores());
    }
    public function validadores(){
    
        $inputFilter = new InputFilter();
  
        $inputFilter->add(array(
            'name' => 'va_contrasena',
            'required' => true,
            'filters' => array( array('name' => 'StringTrim'), ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 6,
                        'max'      => 128,
                    ),
                ),
            ),
        ));
         
        $inputFilter->add(array(
            'name' => 'verificar_contrasena',
            'required' => true,
            'filters' => array ( array('name' => 'StringTrim'), ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array( 'min' => 6 ),
                ),
                array(
                    'name' => 'identical',
                    'options' => array('token' => 'va_contrasena' )
                ),
            ),
        ));
    
        return $inputFilter;
    }
}