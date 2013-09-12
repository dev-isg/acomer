<?php
namespace Application\Form;

use Zend\Form\Form;

class Registroplato extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('registroplato');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new \Application\Form\RegistroplatoFiltro());
        $this->add(array(
            'name' => 'Ta_registro_in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_plato',
            'type' => 'Text',
             'attributes' => array(          
            
            ),
        ));


        $this->add(array(
            'name' => 'va_imagen',
            'type' => 'File',
              'attributes' => array(          
            
            ),

        ));
        $this->add(array(
            'name' => 'va_descripcion',
            'type' => 'textarea',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
      
        
        $this->add(array(
            'name' => 'va_precio',
            'type' => 'text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
    
        
         
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Enviar',
                'id' => 'submitbutton2',
                'class' => 'btn btn-primary btn-solicito'
            ),
        ));
    }
}