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
            'attributes' => array(               
                'class' => 'input',
                'id'   => 'nombre'
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
            'type' => 'Select',
           'options' => array(
                     'label' => 'Rol?',
                     'value_options' => array(
                             '1' => 'Administrador',
                             '2' => 'Editor',                   
                     ),
             )
        ));
        
        
  
     
        $this->add(array(
            'name' => 'va_contrasenia',
            'type' => 'password',
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