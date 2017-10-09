<?php

/*
 * To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace Application\Entity\Repository;

use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class PolicyViewStatisticRepository extends EntityRepository {

	public function updatePolicyViewStatistic( $policyId, $totalView, $roleId ){
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->update('\Application\Entity\PolicyViewStatistic', 'pvs')
			->set('pvs.totalView', ':totalView' )
			->set('pvs.roleId', ':roleId' )
			->where('pvs.policyId = :policyId')
			->andWhere('pvs.date = :date')
			->setParameter(':totalView', $totalView + 1 )
			->setParameter(':roleId', $roleId )
			->setParameter(':policyId', $policyId)
			->setParameter(':date', date('Y-m-d'));
		return $qb->getQuery()->execute();
	}

	public function updatePolicyViewStatisticDownload($policyId, $totalDownload, $roleId){
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->update('\Application\Entity\PolicyViewStatistic', 'pvs')
		->set('pvs.totalDownloadPDF', ':totalDownloadPDF' )
		->set('pvs.roleId', ':roleId' )
		->where('pvs.policyId = :policyId')
		->andWhere('pvs.date = :date')
		->setParameter(':totalDownloadPDF', $totalDownload + 1 )
		->setParameter(':roleId', $roleId )
		->setParameter(':policyId', $policyId)
		->setParameter(':date', date('Y-m-d'));
		return $qb->getQuery()->execute();
	}

	public function updatePolicyViewStatisticPrint($policyId, $totalPrint, $roleId){
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->update('\Application\Entity\PolicyViewStatistic', 'pvs')
		->set('pvs.totalPrint', ':totalPrint' )
		->set('pvs.roleId', ':roleId' )
		->where('pvs.policyId = :policyId')
		->andWhere('pvs.date = :date')
		->setParameter(':totalPrint', $totalPrint + 1 )
		->setParameter(':roleId', $roleId )
		->setParameter(':policyId', $policyId)
		->setParameter(':date', date('Y-m-d'));
		return $qb->getQuery()->execute();
	}

	public function getQuerySelectYearViewStatisticGroup(){
        $em = $this->getEntityManager();
        $tablePolicyViewStatistic = $em->getClassMetadata('Application\Entity\PolicyViewStatistic')->getTableName();
	    $sql = "SELECT policyId, SUM(totalView) as totalView FROM $tablePolicyViewStatistic
                WHERE `date` >= :date
	            GROUP BY policyId";

	    return $sql;
    }

    public function getPolicyData($startMonth, $endMonth){
        $condition = "";

        if(!empty($startMonth)){
            $startDate = $startMonth . "/01";
            $condition .= " AND date>='" . $startDate . "'";
        }

        if(!empty($endMonth)){
            $endDate = strtotime('+1 month', strtotime($endMonth . "/01"));
            $endDate = date("Y/m/d", $endDate);
            $condition .= " AND date<'" . $endDate . "'";
        }

        $sql = "SELECT policyId, roleId, date, totalView, totalDownloadPDF, totalPrint FROM policy_view_statistic WHERE 1 " . $condition . " AND roleId IN (3,4,5,6) ORDER BY date DESC";
        $connection = $this->getEntityManager()->getConnection();
        $rows = $connection->fetchAll($sql);

        $datas = array();
        if(!empty($rows)){
            $list_policy_id = "";
            $list_policy_arr = array();
            foreach($rows as $row){
                if(!isset($list_policy_arr[$row["policyId"]])) {
                    $list_policy_id .=  "," . $row["policyId"];
                    $list_policy_arr[$row["policyId"]] = $row["policyId"];
                }
            }
            $list_policy_id = ltrim($list_policy_id, ",");

            $sql = "SELECT id, shortName FROM policy WHERE id IN (" . $list_policy_id . ")";
            $connection = $this->getEntityManager()->getConnection();
            $rowsPolicy = $connection->fetchAll($sql);

            $replacePolicy = array();
            foreach($rowsPolicy as $row){
                $replacePolicy[$row["id"]] = $row["shortName"];
            }

            foreach($rows as $i=>$row){
                
                $date = $row["date"];
                if(!isset($datas[$date][$row["policyId"]])){
                    $datas[$date][$row["policyId"]]["date"] = $row["date"];
                    $datas[$date][$row["policyId"]]["policyName"] = isset($replacePolicy[$row["policyId"]]) ? $replacePolicy[$row["policyId"]] : "";
                    $datas[$date][$row["policyId"]]["totalAccessRole3"] = 0;
                    $datas[$date][$row["policyId"]]["totalAccessRole4"] = 0;
                    $datas[$date][$row["policyId"]]["totalAccessRole5"] = 0;
                    $datas[$date][$row["policyId"]]["totalAccessRole6"] = 0;
                    $datas[$date][$row["policyId"]]["totalDownloadPDF"] = 0;
                    $datas[$date][$row["policyId"]]["totalPrint"] = 0;
                }

                if($row["roleId"]==3){
                    $datas[$date][$row["policyId"]]["totalAccessRole3"] += $row["totalView"];
                }

                if($row["roleId"]==4){
                    $datas[$date][$row["policyId"]]["totalAccessRole4"] += $row["totalView"];
                }

                if($row["roleId"]==5){
                    $datas[$date][$row["policyId"]]["totalAccessRole5"] += $row["totalView"];
                }

                if($row["roleId"]==6){
                    $datas[$date][$row["policyId"]]["totalAccessRole6"] += $row["totalView"];
                }

                $datas[$date][$row["policyId"]]["totalDownloadPDF"] += $row["totalDownloadPDF"];
                $datas[$date][$row["policyId"]]["totalPrint"] += $row["totalDownloadPDF"];
            }
        }

        return $datas;
    }

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyViewStatistic', 'policyView')
            ->where($qb->expr()->in('policyView.policyId', ':policyId'))
            ->setParameter(':policyId', $policyIds);

        return $qb->getQuery()->execute();
    }
}
