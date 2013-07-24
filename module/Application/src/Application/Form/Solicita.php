<?php
namespace Application\Form;

use Zend\Form\Form;

class Solicita extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('application2');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new \Application\Form\SolicitaFiltro());
        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'nombre_complet',
            'type' => 'Text',
             'attributes' => array(          
            
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
              'attributes' => array(          
            
            ),

        ));
        $this->add(array(
            'name' => 'nombre_plato',
            'type' => 'Text',
              'attributes' => array(          
        
            ),
            'options' => array(

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
              'attributes' => array(          
//           'required' => 'required'   
            ),
            'options' => array(

//                'label' => 'nombre restaurante',   

            ),
        ));
                
                                
           $this->add(array(
            'name' => 'telefono',
            'type' => 'Text',
//            'required' => true,
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
                'class' => 'btn btn-primary btn-solicito'
            ),
        ));
    }
}