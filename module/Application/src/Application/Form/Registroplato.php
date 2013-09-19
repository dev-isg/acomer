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
            'name' => 'Ta_registro_in_id1',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_plato1',
            'type' => 'Text',
             'attributes' => array(          
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen1',
            'type' => 'File',
              'attributes' => array(          
            
            ),
        ));
        $this->add(array(
            'name' => 'va_descripcion1',
            'type' => 'textarea',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));   
        $this->add(array(
            'name' => 'va_precio1',
            'type' => 'text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
        $this->add(array(
            'name' => 'Ta_registro_in_id2',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_plato2',
            'type' => 'Text',
             'attributes' => array(          
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen2',
            'type' => 'File',
              'attributes' => array(          
            
            ),
        ));
        $this->add(array(
            'name' => 'va_descripcion2',
            'type' => 'textarea',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));   
        $this->add(array(
            'name' => 'va_precio2',
            'type' => 'text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
         $this->add(array(
            'name' => 'Ta_registro_in_id3',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_plato3',
            'type' => 'Text',
             'attributes' => array(          
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen3',
            'type' => 'File',
              'attributes' => array(          
            
            ),
        ));
        $this->add(array(
            'name' => 'va_descripcion3',
            'type' => 'textarea',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));   
        $this->add(array(
            'name' => 'va_precio3',
            'type' => 'text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
        $this->add(array(
            'name' => 'Ta_registro_in_id4',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_plato4',
            'type' => 'Text',
             'attributes' => array(          
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen4',
            'type' => 'File',
              'attributes' => array(          
            
            ),
        ));
        $this->add(array(
            'name' => 'va_descripcion4',
            'type' => 'textarea',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));   
        $this->add(array(
            'name' => 'va_precio4',
            'type' => 'text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        $this->add(array(
            'name' => 'Ta_registro_in_id5',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_plato5',
            'type' => 'Text',
             'attributes' => array(          
            ),
        ));
        $this->add(array(
            'name' => 'va_imagen5',
            'type' => 'File',
              'attributes' => array(          
            
            ),
        ));
        $this->add(array(
            'name' => 'va_descripcion5',
            'type' => 'textarea',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));   
        $this->add(array(
            'name' => 'va_precio5',
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
                'value' => 'Terminar',
                'id' => 'submitbutton2',
                'class' => 'btn btn-primary btn-solicito'
            ),
        ));
    }
}