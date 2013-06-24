<?php
namespace Local\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class Local implements InputFilterAwareInterface
{
    public $in_id;
    public $va_telefono;
    public $va_horario;
    public $va_rango_precio;
    public $va_direccion;
    public $ta_restaurante_in_id;
    public $ta_mapa_in_id;
    public $ta_ubigeo_in_id;
    public $ta_horario_in_id;
    public $ta_dia_in_id;
    
    public $de_latitud;
    public $de_longitud;
    public $va_horario_opcional;
    
    public $pais;
    public $departamento;
    public $provincia;
    public $distrito;
    
    protected $inputFilter;
//    public $servicio;
    
    public function exchangeArray($data)
    {
        $this->in_id     = (!empty($data['in_id'])) ? $data['in_id'] : null;
        $this->va_telefono     = (!empty($data['va_telefono'])) ? $data['va_telefono'] : null;
        $this->va_horario    = (!empty($data['va_horario'])) ? $data['va_horario'] : null;
        $this->va_rango_precio     = (!empty($data['va_rango_precio'])) ? $data['va_rango_precio'] : null;
        $this->va_direccion     = (!empty($data['va_direccion'])) ? $data['va_direccion'] : null;
        $this->ta_restaurante_in_id     = (!empty($data['ta_restaurante_in_id'])) ? $data['ta_restaurante_in_id'] : null;
        $this->ta_mapa_in_id    = (!empty($data['ta_mapa_in_id'])) ? $data['ta_mapa_in_id'] : null;
        $this->ta_ubigeo_in_id     = (!empty($data['ta_ubigeo_in_id'])) ? $data['ta_ubigeo_in_id'] : null;
        $this->ta_horario_in_id    = (!empty($data['ta_horario_in_id'])) ? $data['ta_horario_in_id'] : null;
        $this->ta_dia_in_id    = (!empty($data['ta_dia_in_id'])) ? $data['ta_dia_in_id'] : null;
        
        $this->de_latitud = (!empty($data['de_latitud'])) ? $data['de_latitud'] : null;
        $this->de_longitud = (!empty($data['de_longitud'])) ? $data['de_longitud'] : null;
        $this->va_horario_opcional = (!empty($data['va_horario_opcional'])) ? $data['va_horario_opcional'] : null;
        
        $this->pais = (!empty($data['pais'])) ? $data['pais'] : null;
        $this->departamento = (!empty($data['departamento'])) ? $data['departamento'] : null;
        $this->provincia = (!empty($data['provincia'])) ? $data['provincia'] : null;
        $this->distrito = (!empty($data['distrito'])) ? $data['distrito'] : null;
        
//        $this->servicio=(!empty($data['servicio'])) ? $data['servicio'] : null;
    }
    
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
            //inicio de validaciones
//            $inputFilter->add($factory->createInput(array(
//                'name'     => 'in_id',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'Int'),
//                ),
//            )));
            
//            $inputFilter->add($factory->createInput(array(
//                'name'     => 'va_telefono',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 18,
//                        ),
//                    ),
//                ),
//            )));
            
                        $inputFilter->add($factory->createInput(array(
                'name'     => 'va_telefono',
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
            
//             $inputFilter->add($factory->createInput(array(
//                'name'     => 'va_horario',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 70,
//                        ),
//                    ),
//                ),
//            )));
             //---------------------------------------------------------------
             
             
//               $inputFilter->add($factory->createInput(array(
//                'name'     => 'de_latitud',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 100,
//                        ),
//                    ),
//                ),
//            )));
//                         
//             $inputFilter->add($factory->createInput(array(
//                'name'     => 'de_longitud',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 100,
//                        ),
//                    ),
//                ),
//            )));
   //---------------------------------------------------------------    
             
//                         $inputFilter->add($factory->createInput(array(
//                'name'     => 'va_rango_precio',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 70,
//                        ),
//                    ),
//                ),
//            )));
//  
//             $inputFilter->add($factory->createInput(array(
//                'name'     => 'va_horario_opcional',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 70,
//                        ),
//                    ),
//                ),
//            )));
//             
//             $inputFilter->add($factory->createInput(array(
//                'name'     => 'va_direccion',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'StripTags'),
//                    array('name' => 'StringTrim'),
//                ),
//                'validators' => array(
//                    array(
//                        'name'    => 'StringLength',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                            'min'      => 1,
//                            'max'      => 250,
//                        ),
//                    ),
//                ),
//            )));
             

            //fin de validaciones
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
    
}