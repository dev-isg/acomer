<?php
namespace Local\Form;

use Zend\Form\Form;
use Local\Controller\IndexController;


class LocalForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('local');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        
        $this->add(array(
            'name' => 'ta_restaurante_in_id',
            'type' => 'Hidden',
        ));
        
        $this->add(array(
            'name' => 'de_latitud',
            'type' => 'Hidden',
        ));
                
        $this->add(array(
            'name' => 'de_longitud',
            'type' => 'Hidden',
        ));
                
        $this->add(array(
            'name' => 'va_telefono',
            'type' => 'Text',       
            'options' => array(
                'label' => 'Telefono',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'name_rest',
                'placeholder'=>'Ingrese el telefono'
            ),
        ));
        $this->add(array(
            'name' => 'va_horario',
            'type' => 'Text',
              'attributes' => array(               
                'class' => 'span10',
                'id'   => 'raz_rest',
                'placeholder'=>'Ingrese el horario'
            ),
            'options' => array(
                'label' => 'Horario',
            ),
        ));
        
        $this->add(array(
            'name' => 'va_rango_precio',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'web_rest',
                'placeholder'=>'Ingrese el precio'
            ),
            'options' => array(
                'label' => 'Rango de precio',
            ),
        ));
        
        $this->add(array(
            'name' => 'ta_dia_in_id',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'web_rest',
                'placeholder'=>'Ingrese el/los dia(s) de atencion'
            ),
            'options' => array(
                'label' => 'Dias de atencion',
            ),
        ));
        
            $this->add(array(
            'name' => 'va_direccion',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'direccion_loc',
                'placeholder'=>'Ingrese el direccion'
            ),
            'options' => array(
                'label' => 'Direccion Local',
            ),
        ));
            
                
        $this->add(array(
            'name' => 'Ta_tipo_comida_in_id',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'esp_rol'
            ),
           'options' => array(
                     'label' => 'Especialidad',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Criolla',
                             '2' => 'Marina',                   
                     ),
             )
        ));
        
            $this->add(array(
            'name' => 'ch_distrito',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'esp_rol'
            ),
           'options' => array(
                     'label' => 'Distrito',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Criolla',
                             '2' => 'Marina',                   
                     ),
             )
        ));
            
            $this->add(array(
            'name' => 'ch_provincia',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'esp_rol'
            ),
           'options' => array(
                     'label' => 'Provincia',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Criolla',
                             '2' => 'Marina',                   
                     ),
             )
        ));
        
                        $this->add(array(
            'name' => 'ch_departamento',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'esp_rol'
            ),
           'options' => array(
                     'label' => 'Departamento',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Criolla',
                             '2' => 'Marina',                   
                     ),
             )
        ));
                        
               $this->add(array(
            'name' => 'ch_pais',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'esp_rol'
            ),
           'options' => array(
                     'label' => 'Pais',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Peru'                  
                     ),
             )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'class' => 'btn btn-success',
                'id' => 'submitbutton',
            ),
        ));
        
        
    }
}
