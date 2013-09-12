<?php
namespace Application\Form;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class RegistroFiltro extends InputFilter{
    
    public function __construct(){
      
        $this->add(array(
            'name'=>'va_nombre_contacto',
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
            'name' => 'va_correo',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress'
                )
            ),
        ));
        
        
          $this->add(array(
            'name' => 'va_nombre_restaurante',
            'required' => true,
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
            'name'=>'va_direccion',
            'required'=>true,
            'validators'=>array(
              array(
                'name'    => 'StringLength',
                'options' => array(
                    'encoding' => 'UTF-8',
                    'min'      => 3,
                    'max'      => 200,
                ),
            ))    
        ));
        
        $this->add(array(
            'name'=>'va_horario',
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
            'name'=>'va_telefono',
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
