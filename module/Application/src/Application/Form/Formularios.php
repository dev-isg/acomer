<?php

/**
 * @author César Cancino
 * @copyright 2013
 */
namespace Application\Form;

use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha;
use Zend\Form\Factory;
use  Application\Controller\IndexController;



class Formularios extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('bubi');
        $this->setAttribute('method', 'get');
     
        
       $this->add(array(
            'name' => 'in_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'q',
 
            'type' => 'Text',
         
            'attributes' => array(               
               'required' => true, 
                'id'   => 'q',
                'style' => 'display:none'
            ),
        ));
        $this->add(array(
            'name' => 'va_apellidos',
            'type' => 'Text',
              'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_apellidos',
                'placeholder'=>'Ingrese su Apellido'
            ),
            'options' => array(
                'label' => 'Apellidos',
            ),
        ));
        $this->add(array(
            'name' => 'va_email',
            'type' => 'Email',
            'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_email',
                'placeholder'=>'Ingrese su Correo'
            ),
            'options' => array(
                'label' => 'Correo',
            ),
        ));
        
       // $echo = new IndexController();
       //$echo->rolesAction();
        $this->add(array(
            'name' => 'distrito',
             'type' => 'Text',
         
            'attributes' => array(               
               'required' => true, 
                'id'   => 'fq',
                'style' => 'display:none'
            ),
        ));
     
        $this->add(array(
            'name' => 'va_contrasenia',
            'type' => 'Password',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_contrasenia',
                 'placeholder'=>'Ingrese su Password'
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'va_contrasenia2',
            'type' => 'password',
             'attributes' => array(               
                'class' => 'span10',
                'id'   => 'va_contrasenia2',
                 'placeholder'=>'Repita su Password'
            ),
            'options' => array(
                'label' => 'Repita su Password',
            ),
        ));
        

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'class' => 'verlistado',
                'id' => 'submitbutton',
                'style' => 'display:none'
            ),
        ));
    }
}