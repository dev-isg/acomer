<?php
namespace Restaurant\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;


class Usuario
{
    public $in_id;
    public $va_nombre;
    //public $direccion;
    public $va_razon_social;
    public $va_web;
    public $va_imagen;
    public $va_ruc;
    public $Ta_tipo_comida_in_id;

    
    
    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_nombre = (!empty($data['va_nombre'])) ? $data['va_nombre'] : null;
        $this->va_razon_social = (!empty($data['$va_razon_social'])) ? $data['$va_razon_social'] : null;
        $this->va_web= (!empty($data['va_web'])) ? $data['va_web'] : null;
        $this->va_imagen     = (!empty($data['va_imagen'])) ? $data['va_imagen'] : null;
        $this->va_ruc= (!empty($data['va_ruc'])) ? $data['va_ruc'] : null;
        $this->Ta_tipo_comida_in_id = (!empty($data['Ta_tipo_comida_in_id'])) ? $data['Ta_tipo_comida_in_id'] : null;
//$this->direccion  = (!empty($data['direccion'])) ? $data['direccion'] : null;
    }
// 
}