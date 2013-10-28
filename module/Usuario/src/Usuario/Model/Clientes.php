<?php
namespace Usuario\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Clientes
{
    public $in_id;
    public $va_nombre_cliente;
    public $va_email;
    public $verificar_contrasena;
    public $va_contrasena;
    public $en_estado;
    public $va_notificacion;
    public $id_facebook;
    public $va_logout;
    public $va_fecha_ingreso;
    public $va_recupera_contrasena;
    public $va_fecha_exp;
    public $va_verificacion;
    protected $inputFilter;
    
    
    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_nombre_cliente = (!empty($data['va_nombre_cliente'])) ? $data['va_nombre_cliente'] : null;
        $this->verificar_contrasena = (!empty($data['va_contrasena'])) ? $data['va_contrasena'] : null;
        $this->va_email= (!empty($data['va_email'])) ? $data['va_email'] : null;
        $this->va_contrasena     = (!empty($data['va_contrasena'])) ? $data['va_contrasena'] : null;
        $this->en_estado= (!empty($data['en_estado'])) ? $data['en_estado'] : null;
        $this->va_notificacion= (!empty($data['va_notificacion'])) ? $data['va_notificacion'] : null;
        $this->id_facebook = (!empty($data['id_facebook'])) ? $data['id_facebook'] : null;
        $this->va_logout     = (!empty($data['va_logout'])) ? $data['va_logout'] : null;
        $this->va_fecha_ingreso = (!empty($data['va_fecha_ingreso'])) ? $data['va_fecha_ingreso'] : null;
        $this->va_recupera_contrasena= (!empty($data['va_recupera_contrasena'])) ? $data['va_recupera_contrasena'] : null;
        $this->va_fecha_exp     = (!empty($data['va_fecha_exp'])) ? $data['va_fecha_exp'] : null;
        $this->va_verificacion= (!empty($data['va_verificacion'])) ? $data['va_verificacion'] : null;
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
                'name'     => 'va_nombre_cliente',
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
                'name'     => 'va_contrasena',
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
                'name' => 'verificar_contrasena', 
                'required' => false, 
                'filters' => array ( array('name' => 'StringTrim'), ), 
                'validators' => array( 
                    array( 
                        'name'    => 'StringLength', 
                        'options' => array( 'min' => 6 ), 
                    ), 
                    array( 
                        'name' => 'identical', 
                        'options' => array('token' => 'va_contrasena' ) 
                    ), 
                ), 
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}