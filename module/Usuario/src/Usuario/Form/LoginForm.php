<?php
namespace Usuario\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'va_nombre',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'span4'
            ),
            'options' => array(
                'label' => 'Usuario',
            ),
        ));
        $this->add(array(
            'name' => 'va_contrasenia',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'span4'
            ),
            'options' => array(
                'label' => 'Password',                
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-info btn-block',
                'value' => 'INGRESAR',
                'id' => 'submitbutton',
            ),
        ));
    }
}