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
            'attributes'=>array(
                'id'=>'id'
            )
        ));
        
        $this->add(array(
            'name' => 'ta_restaurante_in_id',
            'type' => 'Hidden',
            'attributes'=>array(
                'id'=>'ta_restaurante_in_id'
            )
        ));
        
        $this->add(array(
            'name' => 'de_latitud',
            'type' => 'Hidden',
             'attributes' => array(               
                'id' => 'de_latitud')
        ));
                
        $this->add(array(
            'name' => 'de_longitud',
            'type' => 'Hidden',
            'attributes' => array(               
           'id' => 'de_longitud')
        ) );
                
        $this->add(array(
            'name' => 'va_telefono',
            'type' => 'Text',       
            'options' => array(
                'label' => 'Telefono',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'va_telefono',
                'placeholder'=>'Ingrese el telefono'
            ),
        ));
        
                $this->add(array(
            'name' => 'va_email',
            'type' => 'Text',       
            'options' => array(
                'label' => 'Email',          
            ),
            'attributes' => array(               
                'class' => 'span10  ',
                'id'   => 'va_email',
                'placeholder'=>'Ingrese el correo'
            ),
        ));
                
        $this->add(array(
            'name' => 'va_horario',
            'type' => 'Text',
              'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_horario',
                'placeholder'=>'Ingrese el horario'
            ),
            'options' => array(
                'label' => 'Horario',
            ),
        ));
        
                $this->add(array(
            'name' => 'va_horario_opcional',
            'type' => 'Text',
              'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_horario_opcional',
                'placeholder'=>'Ingrese el horario opcional'
            ),
            'options' => array(
                'label' => 'Horario opcional',
            ),
        ));
                
        
        $this->add(array(
            'name' => 'va_rango_precio',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_rango_precio',
                'placeholder'=>'Ingrese el precio'
            ),
            'options' => array(
                'label' => 'Rango de precio',
            ),
        ));
        
        $this->add(array(
            'name' => 'va_dia',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_dia',
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
                'id'   => 'va_direccion',
                'placeholder'=>'Ingrese el direccion'
            ),
            'options' => array(
                'label' => 'Direccion Local',
            ),
        ));
            
                
//        $this->add(array(
//            'name' => 'Ta_tipo_comida_in_id',
//            'type' => 'Select',
//             'attributes' => array(               
//                'class' => 'span10',
//                'id'   => 'Ta_tipo_comida_in_id'
//            ),
//           'options' => array(
//                     'label' => 'Especialidad',
//                     'value_options' => array(
//                          '' => 'selecccione :',
//                             '1' => 'Criolla',
//                             '2' => 'Marina',                   
//                     ),
//                
//             )
//        ));
        
            $this->add(array(
            'name' => 'distrito',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'distrito'
            ),
           'options' => array(
                     'label' => 'Distrito',
                     'value_options' => array(
                          '' => 'selecccione :'                                                
                     ),
                    'disable_inarray_validator' => true
             )
        ));
            
            $this->add(array(
            'name' => 'provincia',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'provincia'
            ),
           'options' => array(
                     'label' => 'Provincia',
                     'value_options' => array(
                          '' => 'selecccione :'                                                
                     ),
               'disable_inarray_validator' => true
             )
        ));
        
            $this->add(array(
            'name' => 'departamento',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'departamento'
            ),
           'options' => array(
                     'label' => 'Departamento',
                     'value_options' => array(
                          '' => 'selecccione :',
                                          
                     ),
               'disable_inarray_validator' => true
             )
        ));
                        
               $this->add(array(
            'name' => 'pais',
            'type' => 'Select',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'pais'
            ),
           'options' => array(
                     'label' => 'Pais',
                     'value_options' => array(
                          '' => 'selecccione :',
                             '1' => 'Peru'                  
                     ),
               'disable_inarray_validator' => true
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
        
        
                $this->add(array(
            'name' => 'servicio',
            'type' => 'MultiCheckbox',
            'label' => 'Modalidad de Pago?',
             'attributes' => array(               
                'class' => 'checkbox inline',
                'id'   => 'servicio',
                 'placeholder'=>'Ingrese su modalidad de pago'
            ),
            'options' => array(
                     
                     'value_options' => array(
                
                     ),
             )
        ));                        
            $this->add(array(
            'name' => 'va_direccion_referencia',
            'type' => 'Text',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_direccion_referencia',
                'placeholder'=>'Ingrese el direccion'
            ),
            'options' => array(
                'label' => 'Direccion de referencia',
            ),
        ));

        
    }
}
