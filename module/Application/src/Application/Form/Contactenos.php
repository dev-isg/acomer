<?php
namespace Application\Form;

use Zend\Form\Form;
use Application\Controller\IndexController;
use Zend\InputFilter\InputFilterProviderInterface;

class Contactenos extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('applicationxxx');
        $this->setAttribute('method', 'post');        

        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'nombre',            
            'type' => 'Text',
            'attributes' => array(
            'id' => 'nombre',
             'required' => 'required'            
            ),
            'options' => array(

            // 'label' => 'nombre',
            ),
            'validators' => array( 
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 100,
                        ),
                    ),
            )
            
        ));


        $this->add(array(
            'name' => 'email',
             'type' => 'Email',
              'attributes' => array(
            'id' => 'email',
           'required' => 'required'     
            ),
            'options' => array(

                // 'label' => 'correo',
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
            'name' => 'asunto',            
            'type' => 'Text',
             'attributes' => array(
            'id' => 'asunto',
           'required' => 'required'   
            ),
            'options' => array(

//                'label' => 'nombre de plato',
            ),
           
        ));
        
           $this->add(array(
            'name' => 'mensaje',
            'type' => 'Textarea',
            'attributes' => array(               
                'class' => 'span11',
                'required' => 'required',      
                'id' => 'mensaje',
                'colls'=>40,
                'rows'=>4
            ),
            'options' => array(

//                'label' => 'descripcion',

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