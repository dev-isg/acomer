<?php
namespace Usuario\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Usuario
{
    public $in_id;
    public $va_nombre;
    //public $direccion;
    public $va_apellidos;
    public $va_email;
    public $va_contrasenia;
    public $en_estado;
    public $Ta_rol_in_id;
    protected $inputFilter;
    
    
    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_nombre = (!empty($data['va_nombre'])) ? $data['va_nombre'] : null;
        $this->va_apellidos = (!empty($data['va_apellidos'])) ? $data['va_apellidos'] : null;
        $this->va_email= (!empty($data['va_email'])) ? $data['va_email'] : null;
        $this->va_contrasenia     = (!empty($data['va_contrasenia'])) ? $data['va_contrasenia'] : null;
        
        $this->en_estado= (!empty($data['en_estado'])) ? $data['en_estado'] : null;
        $this->Ta_rol_in_id = (!empty($data['Ta_rol_in_id'])) ? $data['Ta_rol_in_id'] : null;
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
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'va_email',
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
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'va_contrasenia',
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
                            'min'      => 6,
                            'max'      =>20 ,
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'va_contrasenia2',
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
                            'min'      => 6,
                            'max'      =>20 ,
                        ),
                    ),
                ),
            )));
  $inputFilter->add($factory->createInput(array(
                'name'     => 'Ta_rol_in_id',
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
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}