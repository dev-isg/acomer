<?php
namespace Application\Form;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class SolicitaFiltro extends InputFilter{
    
    public function __construct(){
      
        $this->add(array(
            'name'=>'nombre_complet',
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
            'name'=>'descripcion',
            'required'=>false,
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
            'name'=>'nombre_plato',
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
            'name'=>'nombre_restaurant',
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
            'name'=>'telefono',
            'required'=>false,
            'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 6,
                    'max'      => 20,
                ),
            ))    
        ));          
        
    } 
}
