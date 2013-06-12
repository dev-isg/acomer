<?php
namespace Usuario\Form;

use Zend\Form\Form;

class UsuarioForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('usuario');
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
        ));
        $this->add(array(
            'name' => 'va_apellidos',
            'type' => 'Text',
            'options' => array(
                'label' => 'Apellidos',
            ),
        ));
        $this->add(array(
            'name' => 'va_email',
            'type' => 'Email',
            'options' => array(
                'label' => 'Correo',
            ),
        ));
        $this->add(array(
            'name' => 'Ta_rol_in_id',
            'type' => 'Text',
            'options' => array(
                'label' => 'roles',
            ),
        ));
        $this->add(array(
            'name' => 'va_contrasenia',
            'type' => 'Password',
            'options' => array(
                'label' => 'Contrasenia',
            ),
        ));
        

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}