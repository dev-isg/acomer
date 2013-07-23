<?php

namespace Application\Form;

use Zend\Form\Form;
use Application\Controller\IndexController;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;

class Contactenos extends Form {

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('application');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new \Application\Form\ContactenosFiltro());

        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'nombre',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'nombre',
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
            'attributes' => array(
                'id' => 'email',
            ),
        ));
        $this->add(array(
            'name' => 'asunto',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'asunto',
            ),
        ));

        $this->add(array(
            'name' => 'mensaje',
            'type' => 'Textarea',
            'attributes' => array(
                'class' => 'span11',
                'id' => 'mensaje',
                'colls' => 40,
                'rows' => 4
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Enviar',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-solicito'
            ),
        ));
    }

}