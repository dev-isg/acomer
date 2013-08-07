<?php
namespace Usuario\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comentarios
{
    public $in_id;
    public $tx_descripcion;
    public $en_estado;
    public $da_fecha;

    public $Ta_cliente_in_id;
    public $Ta_plato_in_id;   
    public $Ta_puntaje_in_id;
  
    protected $inputFilter;
    
    
    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->tx_descripcion = (!empty($data['tx_descripcion'])) ? $data['tx_descripcion'] : null;
        $this->en_estado = (!empty($data['en_estado'])) ? $data['en_estado'] : null;
        $this->da_fecha= (!empty($data['da_fecha'])) ? $data['da_fecha'] : null;
        $this->Ta_cliente_in_id     = (!empty($data['Ta_cliente_in_id'])) ? $data['Ta_cliente_in_id'] : null;
        $this->Ta_plato_in_id= (!empty($data['Ta_plato_in_id'])) ? $data['Ta_plato_in_id'] : null;
        $this->Ta_puntaje_in_id = (!empty($data['Ta_puntaje_in_id'])) ? $data['Ta_puntaje_in_id'] : null;
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
  
    $inputFilter->add($factory->createInput(array(
                'name'     => 'Ta_puntaje_in_id',
                'required' => true
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}