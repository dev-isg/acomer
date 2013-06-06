<?php
namespace Usuario\Model;

class Usuario
{
    public $id;
    public $nombre;
    public $direccion;

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->nombre = (!empty($data['nombre'])) ? $data['nombre'] : null;
        $this->direccion  = (!empty($data['direccion'])) ? $data['direccion'] : null;
    }
}