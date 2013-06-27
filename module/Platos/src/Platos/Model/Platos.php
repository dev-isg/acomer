<?php
namespace Platos\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Platos\Model\beta;


class Platos implements InputFilterAwareInterface 
{

    
    public $in_id;
    public $va_imagen;
    public $tx_descripcion;
    public $va_nombre;
    public $va_precio;
    public $en_destaque;
    public $en_estado;
    public $Ta_tipo_plato_in_id;
    public $Ta_puntaje_in_id;
    public $Ta_usuario_in_id;
    
    public $tipo_plato_va_nombre;
    public $restaurante_va_nombre;
//    
//    private $data=array();
//    
//    protected $inputFilter;
//    
// 
//       
//    public function __set($name, $value) {
//        $this->data[$name]=$value;
//    }
//    
//
//    public function __get($name) {
//        return $this->data[$name];  
//    }
    
    public function exchangeArray($data)
    {
        $this->in_id = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_imagen = (!empty($data['va_imagen'])) ? $data['va_imagen'] : null;
        $this->tx_descripcion = (!empty($data['tx_descripcion'])) ? $data['tx_descripcion'] : null;
        $this->va_nombre = (!empty($data['va_nombre'])) ? $data['va_nombre'] : null;
        $this->va_precio = (!empty($data['va_precio'])) ? $data['va_precio'] : null;
        $this->en_destaque = (!empty($data['en_destaque'])) ? $data['en_destaque'] : null;
        $this->en_estado = (!empty($data['en_estado'])) ? $data['en_estado'] : null;
        $this->Ta_tipo_plato_in_id = (!empty($data['Ta_tipo_plato_in_id'])) ? $data['Ta_tipo_plato_in_id'] : null;
        $this->Ta_puntaje_in_id = (!empty($data['Ta_puntaje_in_id'])) ? $data['Ta_puntaje_in_id'] : null;
        $this->Ta_usuario_in_id = (!empty($data['Ta_usuario_in_id'])) ? $data['Ta_usuario_in_id'] : null;
        
//            $this->tipo_plato_va_nombre;
//    $this->restaurante_va_nombre;
//        $this->hola = (!empty($data['hola'])) ? $data['hola'] : null;
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

    }
}
