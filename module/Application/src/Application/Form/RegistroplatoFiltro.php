<?php
namespace Application\Form;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class RegistroplatoFiltro extends InputFilter{
    
    public function __construct(){
      
        $this->add(array(
            'name'=>'va_nombre_plato',
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
            'name' => 'va_descripcion',
            'required' => true,
             'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 3,
                    'max'      => 250,
                ),
            )) 
        ));
          
          $this->add(array(
                    'name'     => 'va_imagen',
                    'required' => false,
                     'validators' => array(
                    array(
                        'name'    => 'filemimetype',
                      //  'options' =>  array('mimeType' => 'image/png,image/x-png,image/jpg,image/gif,image/jpeg'),
                        'options' =>  array('mimeType' => 'image/jpg,image/jpeg'),
                    ),
                    array(
                        'name'    => 'filesize',
                        'options' =>  array('max' => 204800),
                    ),
                  ),
               )
            );
                
        $this->add(array(
            'name'=>'va_precio',
            'required'=>true,
            'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 3,
                    'max'      => 20,
                ),
            ))    
        ));
        
      
        
                 
        
    } 
}
