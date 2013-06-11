<?php
namespace Usuario\Model;

class Usuario
{
    public $in_id;
    public $va_nombre;
    //public $direccion;
    public $va_apellidos;
    public $va_email;
    public $va_contraseña;
    public $en_estado;
    public $Ta_rol_in_id;
    

    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_nombre = (!empty($data['va_nombre'])) ? $data['va_nombre'] : null;
        $this->va_apellidos = (!empty($data['va_apellidos'])) ? $data['va_apellidos'] : null;
        $this->va_email= (!empty($data['va_email'])) ? $data['va_email'] : null;
        $this->va_contraseña     = (!empty($data['va_contraseña'])) ? $data['va_contraseña'] : null;
        $this->en_estado= (!empty($data['en_estado'])) ? $data['en_estado'] : null;
        $this->Ta_rol_in_id = (!empty($data['Ta_rol_in_id'])) ? $data['Ta_rol_in_id'] : null;
//$this->direccion  = (!empty($data['direccion'])) ? $data['direccion'] : null;
    }
}