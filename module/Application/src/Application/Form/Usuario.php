<?php
namespace Application\Form;

use Zend\Form\Form;

class usuarioForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('application');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'nombre',
            'type' => 'Text',
            'options' => array(
                'label' => 'nombre',
            ),
        ));
        $this->add(array(
            'name' => 'apellido',
            'type' => 'Text',
            'options' => array(
                'label' => 'apellidos',
            ),
        ));
        $this->add(array(
            'name' => 'pass',
            'type' => 'password',
            'options' => array(
                'label' => 'contrasenia',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
            'options' => array(
                'label' => 'correo',
            ),
        ));
         $form = new Form('rol');
     $form->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'rol',
             'options' => array(
                     'label' => 'selecccione el rol?',
                     'value_options' => array(
                             '1' => 'Administrador',
                             '2' => 'editor'
                     ),
             )
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