<?php
namespace Restaurante\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
//use Zend\Validator\File\Size;

class Restaurante implements InputFilterAwareInterface
{

    
    public $in_id;
    public $va_nombre;
    public $va_razon_social;
    public $va_web;
    public $va_imagen;
    public $va_ruc;
    public $en_estado;
    public $Ta_tipo_comida_in_id;
        
    protected $inputFilter;


    
    
    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_nombre = (!empty($data['va_nombre'])) ? $data['va_nombre'] : null;
        $this->va_razon_social = (!empty($data['va_razon_social'])) ? $data['va_razon_social'] : null;
        $this->va_web    = (!empty($data['va_web'])) ? $data['va_web'] : null;
        $this->va_imagen = (!empty($data['va_imagen'])) ? $data['va_imagen'] : null;//'default-img.jpg'
        $this->va_ruc    = (!empty($data['va_ruc'])) ? $data['va_ruc'] : null;
        $this->en_estado    = (!empty($data['en_estado'])) ? $data['en_estado'] : null;
        $this->Ta_tipo_comida_in_id = (!empty($data['Ta_tipo_comida_in_id'])) ? $data['Ta_tipo_comida_in_id'] : null;
        
//$this->direccion  = (!empty($data['direccion'])) ? $data['direccion'] : null;
    }
// Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'in_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));


            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'va_nombre',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'va_razon_social',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
//$inputFilter->add($factory->createInput(array(
//                'name'     => 'va_web',
//                'required' => false,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 10,
//                            'max'      => 100,
//                        ),
//                    ),
//                ),
//            )));
             $inputFilter->add(
                $factory->createInput(array(
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
                        'options' =>  array('min' =>20,'max' => 204800),
                    ),
                  ),
               ))
            );
             $inputFilter->add($factory->createInput(array(
                'name'     => 'va_ruc',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 11,
                            'max'      => 11,
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'Ta_tipo_comida_in_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',

                    ),
                ),
            )));
//   
//    $inputFilter->add($factory->createInput(array(
//                'name'     => 'va_modalidad',
//                'required' => false,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 10,
//                            'max'      => 12,
//                        ),
//                    ),
//                ),
//            )));
    
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}