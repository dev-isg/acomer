<?php
namespace SanAuth\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;



class PasswordForm extends Form
{
     public function __construct($name = null)
    {
        parent::__construct('CambiarPass');
        $this->setAttribute('method', 'post');
        
         $this->add(array(
            'name' => 'va_email',
            'type' => 'email',
            'attributes' => array(               
                'placeholder'=>'yourmail@email.com',
                'class'=>'form-control',
                'id' => 'va_email'
            ),
        ));  
         

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Enviar',
                'class' => 'btn btn-bricky pull-right btnEmailPass'
                
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
        
        return $inputFilter;
    }
}

