<?php
namespace Application\Form;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ContactenosFiltro extends InputFilter{
    
    public function __construct(){
 
        $this->add(array(
            'name'=>'nombre',
            'required'=>true,
            'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 3,
                    'max'      => 100,
                ),
            ))    
        ));
        
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress'
                )
            ),
        ));
        
        $this->add(array(
            'name'=>'mensaje',
            'required'=>true,
            'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 3,
                    'max'      => 500,
                ),
            ))    
        ));
                
        $this->add(array(
            'name'=>'asunto',
            'required'=>true,
            'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 3,
                    'max'      => 100,
                ),
            ))    
        ));
        
        
        
    } 
}
