<?php
namespace Application\Form;

use Zend\Form\Form;

class Solicita extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('application');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'nombre_complet',
            'type' => 'Text',
            'options' => array(

            // 'label' => 'nombre',
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
            'options' => array(

                // 'label' => 'correo',
            ),
        ));
        $this->add(array(
            'name' => 'nombre_plato',
            'type' => 'Text',
            'options' => array(

//                'label' => 'nombre de plato',
            ),
        ));
        
           $this->add(array(
            'name' => 'descripcion',
            'type' => 'Textarea',
            'attributes' => array(               
                'class' => 'span11',
                'colls'=>40,
                'rows'=>4
            ),
            'options' => array(

//                'label' => 'descripcion',

            ),
        ));

         $this->add(array(
            'name' => 'nombre_restaurant',
            'type' => 'Text',
            'options' => array(

//                'label' => 'nombre restaurante',   

            ),
        ));
                
                                
           $this->add(array(
            'name' => 'telefono',
            'type' => 'Text',
            'options' => array(

//                'label' => 'Telefono',   
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Enviar',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary'
            ),
        ));
    }
}