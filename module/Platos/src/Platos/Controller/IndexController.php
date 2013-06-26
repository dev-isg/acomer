<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Platos\Controller;

use Platos\Form\PlatosForm; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Platos\Model\Platos;
use Zend\Form\Element;

class IndexController extends AbstractActionController
{
    protected $platosTable;
    public function indexAction()
    {
        $lista=$this->getPlatosTable()->fetchAll();
        return new ViewModel(array(
            'platos' => $lista
        ));
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }
    
    public function agregarplatosAction(){
        $form = new PlatosForm();
        return array('form' => $form);
        
    }
    
        public function getPlatosTable()
    {
        if (!$this->platosTable) {
            $sm = $this->getServiceLocator();
            $this->platosTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->platosTable;
    }
}
