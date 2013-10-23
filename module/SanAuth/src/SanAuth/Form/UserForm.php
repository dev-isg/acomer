<?php
namespace SanAuth\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;

class UserForm extends Form
{
     public function __construct($name = null)
    {
        parent::__construct('usuario');
        $this->setAttribute('method', 'post');
        $this->setAttribute('endtype', 'multipart/form-data');
   
         $this->add(array(
            'name' => 'va_email',
            'type' => 'Text',
            'attributes' => array(               
                'id' => 'va_email',
                'placeholder'=>'Ingrese un correo valido…'
            )
        ));  
         
         $this->add(array(
            'name' => 'va_contrasena',
            'type' => 'Password',
            'attributes' => array(
                'id'=>'inputPassword',
                'placeholder'=>'Ingrese la contraseña…'
            )
        ));
         
          $this->add(array(
            'name' => 'va_token',
            'type' => 'Hidden',
            'attributes'=>array(
                'id'=>'id'
            )
        ));
          

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Ingresar',
                'class' => 'btn btn-primary'
            ),
        ));
        
        $this->setInputFilter($this->validadores());
    }
    
    public function validadores(){
        $inputFilter = new InputFilter();
        
        $inputFilter->add(array(
            'name' => 'va_email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress'
                )
            ),
        ));
        $inputFilter->add(array(
            'name' => 'va_contrasena',
            'required' => true,
        ));
      
        return $inputFilter;
        
    }
}

