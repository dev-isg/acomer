<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Restaurant\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Restaurant\Model\Restaurant; 

class IndexController extends AbstractActionController
{
    protected $restaurantTable;
    public function indexAction()
    {
        
     
        return new ViewModel(array(
            'restaurantes' => $this->getRestaurantTable()->fetchAll(),
        ));
    }

    public function fooAction()
    {
       echo "ccc"
;exit;        return array();
    }
    
    public function getRestaurantTable()
    {
        if (!$this->restaurantTable) {
            $sm = $this->getServiceLocator();
            $this->restaurantTable = $sm->get('Restaurant\Model\RestaurantTable');
            echo "sss";
        }  else {
            
         {
            
            echo "sss";
        }}
        return $this->restaurantTable;
    }
}
