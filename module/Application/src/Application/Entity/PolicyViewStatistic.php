<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicyViewStatisticRepository")
 * @ORM\Table(name="policy_view_statistic")
 */
class PolicyViewStatistic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /** @ORM\Column(type="integer", nullable=true) */
    protected $policyId;

    /** @ORM\Column(type="integer", nullable=false, options={"default"=0}) */
    protected $totalView = 0;
    
    /** @ORM\Column(type="integer", nullable=false, options={"default"=0}) */
    protected $totalDownloadPDF = 0;
    
    /** @ORM\Column(type="integer", nullable=false, options={"default"=0}) */
    protected $totalPrint = 0;

    /** @ORM\Column(columnDefinition="TINYINT DEFAULT 0 NOT NULL") */
    protected $roleId;
    
    /** @ORM\Column(type="date", nullable=false) */
    protected $date;
    
    /**
     * @return mixed
     */
    public function getPolicyId() {
        return $this->policyId;
    }

    /**
     * @param mixed $policyId
     */
    public function setPolicyId($policyId) {
        $this->policyId = $policyId;
    }

    /**
     * @return mixed
     */
    public function getTotalView() {
        return $this->totalView;
    }

    /**
     * @param mixed $totalView
     */
    public function setTotalView($totalView) {
        $this->totalView = $totalView;
    }
    
    /**
     * @return mixed
     */
    public function getTotalDownloadPDF() {
    	return $this->totalDownloadPDF;
    }
    
    /**
     * @param mixed $totalDownloadPDF
     */
    public function setTotalDownloadPDF($totalDownloadPDF) {
    	$this->totalDownloadPDF = $totalDownloadPDF;
    }
    
    /**
     * @return mixed
     */
    public function getTotalPrint() {
    	return $this->totalPrint;
    }
    
    /**
     * @param mixed $totalPrint
     */
    public function setTotalPrint($totalPrint) {
    	$this->totalPrint = $totalPrint;
    }
    
    /**
     * @return mixed
     */
    public function getRoleId() {
    	return $this->roleId;
    }
    
    /**
     * @param mixed $roleId
     */
    public function setRoleId($roleId) {
    	$this->roleId = $roleId;
    }
	
    /**
     * @return mixed
     */
    public function getDate() {
    	return $this->date;
    }
    
    /**
     * @param mixed $date
     */
    public function setDate($date) {
    	$this->date = $date;
    }
}