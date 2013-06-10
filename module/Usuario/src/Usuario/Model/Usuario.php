<?php
namespace Usuario\Model;

class Usuario
{
    public $in_id;
    public $va_nombre;
    public $direccion;

    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_nombre = (!empty($data['va_nombre'])) ? $data['va_nombre'] : null;
        $this->direccion  = (!empty($data['direccion'])) ? $data['direccion'] : null;
    }
}