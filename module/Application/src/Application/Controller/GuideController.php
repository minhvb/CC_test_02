<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class GuideController extends BaseController
{   
    public function indexAction() {
        $href = array();
        $href['sitePolicy'] = $this->url()->fromRoute('guide/default', array('action' => 'site-policy'));
        $href['relatedLink'] = $this->url()->fromRoute('guide/default', array('action' => 'related-link'));
        $href['privacyPolicy'] = $this->url()->fromRoute('guide/default', array('action' => 'privacy-policy'));
        $href['inquiries'] = $this->url()->fromRoute('guide/default', array('action' => 'inquiries'));
        $this->viewModel->setVariable('href', $href);
        return $this->viewModel;
    }

    public function sitePolicyAction() {
        return $this->viewModel;
    }

    public function relatedLinkAction() {
        return $this->viewModel;
    }

    public function privacyPolicyAction() {
        return $this->viewModel;
    }

    public function inquiriesAction() {
        return $this->viewModel;
    }
}