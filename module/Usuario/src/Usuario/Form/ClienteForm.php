<?php
namespace Usuario\Form;

use Zend\Form\Form;
use Usuario\Controller\IndexController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ClienteForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('cliente');
        
        $this->setAttribute('method', 'post');
        
        
        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
               
        $this->add(array(
            'name' => 'va_nombre',
            'type' => 'Text',
          
            'options' => array(
                'label' => 'Nombre',          
            ),
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_nombre',
                'placeholder'=>'Ingrese su nombre'
            ),
        
        ));
        
                $this->add(array(
            'name' => 'va_email',
            'type' => 'Email',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_email',
                'placeholder'=>'Ingrese su Correo'
            ),
            'options' => array(
                'label' => 'Correo',
            ),
            'validators' => array( 
                array( 
                    'name' => 'EmailAddress', 
                    'options' => array( 
                        'messages' => array( 
                            \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Email address format is invalid' 
                        ) 
                    ) 
                ) 
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