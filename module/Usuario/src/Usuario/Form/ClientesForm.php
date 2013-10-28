<?php
namespace Usuario\Form;

use Zend\Form\Form;
use Usuario\Controller\IndexController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class ClientesForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('RegistroUser');
        $this->setAttribute('method', 'post');
        
        
        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
            'attributes' => array(
                'id' => 'in_id',
            ),
        ));

        $this->add(array(
            'name' => 'va_nombre_cliente',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'va_nombre_cliente',
                'class' => 'form-control',
                'placeholder' => 'Ingrese el nombre de usario…'
            ),
        ));


        $this->add(array(
            'name' => 'va_contrasena',
            'type' => 'Password',
//            'options' => array(
//                'label' => 'Password:',          
//            ),
            'attributes' => array(
                'id' => 'va_contrasena',
                'placeholder' => 'Ingrese la contraseña…'
            ),
        ));
        
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'va_notificacion',
            'options' => array(
                
                'use_hidden_element' => true,
                'checked_value' => 'si',
                'unchecked_value' => 'no'
            )
        ));

        $this->add(array(
            'name' => 'verificar_contrasena',
            'type' => 'password',
            'options' => array(
                'label' => '',
            ),
            'attributes' => array(
                'id' => 'verificar_contrasena',                
                'placeholder' => 'Confirme la contraseña…'
            ),
        ));
        
            $this->add(array(
            'name' => 'va_email',
            'type' => 'Email',
            'attributes' => array(
                'id'   => 'va_email',
                'placeholder'=>'Ingrese su Correo'
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
                'value' => 'Registrarme',
                'class' => 'btn btn-bricky pull-right btnRegisU',
                'id' => 'submitbutton'
            ),
        ));
      
    }
}