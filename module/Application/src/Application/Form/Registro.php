<?php
namespace Application\Form;

use Zend\Form\Form;

class Registro extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('application2');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new \Application\Form\RegistroFiltro());
        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'va_nombre_contacto',
            'type' => 'Text',
             'attributes' => array(          
            
            ),
        ));


        $this->add(array(
            'name' => 'va_correo',
            'type' => 'Email',
              'attributes' => array(          
            
            ),

        ));
        $this->add(array(
            'name' => 'va_nombre_restaurante',
            'type' => 'Text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
        $this->add(array(
            'name' => 'va_imagen',
            'type' => 'File',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
        
        
       $this->add(array(
            'name' => 'Ta_tipo_comida_in_id',
            'type' => 'Select',  
             'attributes' => array(               
                'class' => 'span4',
                'id'   => 'Ta_tipocomida_in_id'
            ),
           'options' => array('label' => '',
                     'value_options' => array(
                         
                          '' => 'selecccione :',
              ),
             )
        ));
       
       $this->add(array(
            'name' => 'cantidad_platos',
            'type' => 'Select',  
             'attributes' => array(               
                'class' => 'span3',
                'id'   => 'cantidad_platos'
            ),
           'options' => array('label' => '',
                     'value_options' => array(
                         ''=>'',
                          '1' => '1',
                         '2' => '2','3' => '3','4' => '4','5' => '5',
                         
                         
                         
              ),
             )
        ));
        
         $this->add(array(
            'name' => 'va_direccion',
            'type' => 'Text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
              $this->add(array(
            'name' => 'va_horario',
            'type' => 'Text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
         $this->add(array(
            'name' => 'va_telefono',
            'type' => 'Text',
              'attributes' => array(          
        
            ),
            'options' => array(

            ),
        ));
         
    
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Siguiente',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-solicito'
            ),
        ));
    }
}