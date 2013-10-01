<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Zend\Db\Sql\Sql;
use Admin\Model\Banner;        
use Admin\Form\BannerForm;  
use Admin\Model\BannerTable;  
use Zend\Db\Adapter\Adapter;
use Platos\Model\PlatosTable; 

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    
     protected $bannerTable;
  public $dbAdapter;
    public function indexAction()
    {
       echo 'mama';exit;
        return new viewModel(array());
    }
public function getBannerTable() {
        if (!$this->bannerTable) {
            $sm = $this->getServiceLocator();
            $this->bannerTable = $sm->get('Admin\Model\BannerTable');
        }
        return $this->bannerTable;
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }

        public function agregarbannerAction()
    {
        echo 'mama';exit;
        return new viewModel(array());
    }
}
