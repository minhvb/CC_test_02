<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Administrator\Service\PolicyManagementService;
use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class PolicyRepository extends EntityRepository
{

    const DRAFT = 1;
    const NOT_DRAFT = 0;
    const ONE_DAY_TIME = 24 * 60 * 60 - 1;
    const ONE_YEAR_TIME = 365 * 24 * 60 * 60;

    public function getDataByIdAndDepartment($policyId, $search)
    {
        if (!$policyId) {
            return NULL;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('policy')->from('\Application\Entity\Policy', 'policy')
            ->where('policy.id = :policyId')->setParameter(':policyId', $policyId);
        if (!empty($search['bureauId'])) {
            $qb->andWhere('policy.bureauId = :bureauId')->setParameter(':bureauId', $search['bureauId']);
        }
        if (!empty($search['departmentId'])) {
            $qb->andWhere('policy.departmentId = :departmentId')->setParameter(':departmentId', $search['departmentId']);
        }
        if (!empty($search['divisionId'])) {
            $qb->andWhere('policy.divisionId = :divisionId')->setParameter(':divisionId', $search['divisionId']);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getTotalResultBySearch($search)
    {
        $datetime = new \DateTime('now');
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('count(policy.id)')->from('\Application\Entity\Policy', 'policy');
        if (isset($search['txtKeyword']) && $search['txtKeyword'] != '') {
            $qbAttributes = $em->createQueryBuilder();
            $qbAttributes->select('policyAttrMapping.policyId')
                ->from('\Application\Entity\PolicyAttributeMapping', 'policyAttrMapping')
                ->innerJoin('\Application\Entity\PolicyAttributes', 'policyAttr', Join::WITH, 'policyAttr.id = policyAttrMapping.attributesPolicyId')
                ->where($qbAttributes->expr()->in('policyAttr.attributeType', ApplicationConst::LIST_ARRTRIBUTES_SEARCH))
                ->andWhere($qbAttributes->expr()->like('policyAttr.value', ':keyword'))
                ->setParameter(':keyword', '%' . trim($search['txtKeyword']) . '%');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('policy.name', ':keyword'),
                    $qb->expr()->like('policy.shortName', ':keyword'),
                    $qb->expr()->like('policy.purpose', ':keyword'),
                    $qb->expr()->like('policy.detailOfSupportArea', ':keyword'),
                    $qb->expr()->like('policy.content', ':keyword'),
                    $qb->expr()->like('policy.contact', ':keyword'),
                    $qb->expr()->in('policy.id', $qbAttributes->getDQL())
                )
            )->setParameter(':keyword', '%' . trim($search['txtKeyword']) . '%');
        }
        if (!empty($search['ddlBureauId'])) {
            $qb->andWhere('policy.bureauId = :bureauId')->setParameter(':bureauId', $search['ddlBureauId']);
        }
        if (!empty($search['ddlDepartmentId'])) {

            $qb->andWhere('policy.departmentId = :departmentId')->setParameter(':departmentId', $search['ddlDepartmentId']);
        }
        if (!empty($search['ddlDivisionId'])) {
            $qb->andWhere('policy.divisionId = :divisionId')->setParameter(':divisionId', $search['ddlDivisionId']);
        }
        if (isset($search['cbAttentionFlag']) && intval($search['cbAttentionFlag']) > 0) {
            $qb->andWhere('policy.attentionFlag = :attentionFlag')->setParameter(':attentionFlag', intval($search['cbAttentionFlag']));
        }

        if (isset($search['cbTypePolicy']) && is_array($search['cbTypePolicy'])) {
            $qbTypePolicy = $em->createQueryBuilder();
            $orXTypePolicy = $qbTypePolicy->expr()->orX();
            if (in_array(ApplicationConst::POLICY_TYPE_EDITING, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->eq('policy.isDraft', 1));
            }
            if (in_array(ApplicationConst::POLICY_TYPE_WAITING_PUBLIC, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->gt('policy.publishStartdate', $datetime->getTimestamp()),
                            $qb->expr()->isNotNull('policy.publishStartdate')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('policy.publishEnddate'),
                            $qb->expr()->isNull('policy.publishStartdate'),
                            $qb->expr()->gt('policy.publishEnddate', $datetime->getTimestamp())
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNull('policy.publishEnddate'),
                            $qb->expr()->isNull('policy.publishStartdate')
                        )
                    ),
                    $qb->expr()->eq('policy.isDraft', 0)
                ));
            }
            if (in_array(ApplicationConst::POLICY_TYPE_PUBLIC, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('policy.publishStartdate'),
                            $qb->expr()->isNotNull('policy.publishEnddate'),
                            $qb->expr()->lte('policy.publishStartdate', $datetime->getTimestamp()),
                            $qb->expr()->gte('policy.publishEnddate', $datetime->getTimestamp())
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('policy.publishStartdate'),
                            $qb->expr()->isNull('policy.publishEnddate'),
                            $qb->expr()->lte('policy.publishStartdate', $datetime->getTimestamp())
                        )
                    ),
                    $qb->expr()->eq('policy.isDraft', 0)
                ));
            }
            if (in_array(ApplicationConst::POLICY_TYPE_PRIVATE, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->andX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('policy.publishEnddate'),
                        $qb->expr()->lt('policy.publishEnddate', $datetime->getTimestamp())
                    ),
                    $qb->expr()->eq('policy.isDraft', 0)
                ));
            }
            $qb->andWhere($orXTypePolicy);
        }
        if ( !empty($search['publishStartdate']) ) {
        	$qb->andWhere('policy.publishStartdate >= :publishStartdate')->setParameter(':publishStartdate', strtotime($search['publishStartdate']));
        	if ( !empty($search['publishEnddate']) ) {
        		$qb->andWhere('policy.publishEnddate <= :publishEnddate')->setParameter(':publishEnddate', strtotime($search['publishEnddate']));
        	}
        }
        if ( empty($search['publishStartdate']) && !empty($search['publishEnddate']) ) {
        	$qb->andWhere('policy.publishEnddate <= :publishEnddate')->setParameter(':publishEnddate', strtotime($search['publishEnddate']));
        }
        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $ex) {
            return false;
        }

    }

    public function getPolicyBySearchAndPage($search, $page, $resultPerPage)
    {
        $datetime = new \DateTime('now');
        if ($page < 1) $page = 1;
        $firstResult = ($page - 1) * $resultPerPage;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('policy')->from('\Application\Entity\Policy', 'policy');

        $qb->orderBy('policy.name', 'DESC')
            ->addOrderBy('policy.bureauId', 'DESC')
            ->addOrderBy('policy.departmentId', 'DESC')
            ->addOrderBy('policy.divisionId', 'DESC')
            ->addOrderBy('policy.publishStartdate', 'DESC')
            ->addOrderBy('policy.publishEnddate', 'DESC')
            ->addOrderBy('policy.id', 'DESC');

        if (isset($search['txtKeyword']) && $search['txtKeyword'] != '') {
            $qbAttributes = $em->createQueryBuilder();
            $qbAttributes->select('policyAttrMapping.policyId')
                ->from('\Application\Entity\PolicyAttributeMapping', 'policyAttrMapping')
                ->innerJoin('\Application\Entity\PolicyAttributes', 'policyAttr', Join::WITH, 'policyAttr.id = policyAttrMapping.attributesPolicyId')
                ->where($qbAttributes->expr()->in('policyAttr.attributeType', ApplicationConst::LIST_ARRTRIBUTES_SEARCH))
                ->andWhere($qbAttributes->expr()->like('policyAttr.value', ':keyword'))
                ->setParameter(':keyword', '%' . trim($search['txtKeyword']) . '%');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('policy.name', ':keyword'),
                    $qb->expr()->like('policy.shortName', ':keyword'),
                    $qb->expr()->like('policy.purpose', ':keyword'),
                    $qb->expr()->like('policy.detailOfSupportArea', ':keyword'),
                    $qb->expr()->like('policy.content', ':keyword'),
                    $qb->expr()->like('policy.contact', ':keyword'),
                    $qb->expr()->in('policy.id', $qbAttributes->getDQL())
                )
            )->setParameter(':keyword', '%' . trim($search['txtKeyword']) . '%');
        }
        if (!empty($search['ddlBureauId'])) {
            $qb->andWhere('policy.bureauId = :bureauId')->setParameter(':bureauId', $search['ddlBureauId']);
        }
        if (!empty($search['ddlDepartmentId'])) {
            $qb->andWhere('policy.departmentId = :departmentId')->setParameter(':departmentId', $search['ddlDepartmentId']);
        }
        if (!empty($search['ddlDivisionId'])) {
            $qb->andWhere('policy.divisionId = :divisionId')->setParameter(':divisionId', $search['ddlDivisionId']);
        }
        if (isset($search['cbAttentionFlag']) && intval($search['cbAttentionFlag']) > 0) {
            $qb->andWhere('policy.attentionFlag = :attentionFlag')->setParameter(':attentionFlag', intval($search['cbAttentionFlag']));
        }
        if (isset($search['cbTypePolicy']) && is_array($search['cbTypePolicy'])) {
            $qbTypePolicy = $em->createQueryBuilder();
            $orXTypePolicy = $qbTypePolicy->expr()->orX();
            if (in_array(ApplicationConst::POLICY_TYPE_EDITING, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->eq('policy.isDraft', 1));
            }
            if (in_array(ApplicationConst::POLICY_TYPE_WAITING_PUBLIC, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->gt('policy.publishStartdate', $datetime->getTimestamp()),
                            $qb->expr()->isNotNull('policy.publishStartdate')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('policy.publishEnddate'),
                            $qb->expr()->isNull('policy.publishStartdate'),
                            $qb->expr()->gt('policy.publishEnddate', $datetime->getTimestamp())
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNull('policy.publishEnddate'),
                            $qb->expr()->isNull('policy.publishStartdate')
                        )
                    ),
                    $qb->expr()->eq('policy.isDraft', 0)
                ));
            }
            if (in_array(ApplicationConst::POLICY_TYPE_PUBLIC, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('policy.publishStartdate'),
                            $qb->expr()->isNotNull('policy.publishEnddate'),
                            $qb->expr()->lte('policy.publishStartdate', $datetime->getTimestamp()),
                            $qb->expr()->gte('policy.publishEnddate', $datetime->getTimestamp())
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('policy.publishStartdate'),
                            $qb->expr()->isNull('policy.publishEnddate'),
                            $qb->expr()->lte('policy.publishStartdate', $datetime->getTimestamp())
                        )
                    ),
                    $qb->expr()->eq('policy.isDraft', 0)
                ));
            }
            if (in_array(ApplicationConst::POLICY_TYPE_PRIVATE, $search['cbTypePolicy'])) {
                $orXTypePolicy->add($qb->expr()->andX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('policy.publishEnddate'),
                        $qb->expr()->lt('policy.publishEnddate', $datetime->getTimestamp())
                    ),
                    $qb->expr()->eq('policy.isDraft', 0)
                ));
            }
            $qb->andWhere($orXTypePolicy);
        }
        if ( !empty($search['publishStartdate']) ) {
        	$qb->andWhere('policy.publishStartdate >= :publishStartdate')->setParameter(':publishStartdate', strtotime($search['publishStartdate']));
        	if ( !empty($search['publishEnddate']) ) {
        		$qb->andWhere('policy.publishEnddate <= :publishEnddate')->setParameter(':publishEnddate', strtotime($search['publishEnddate']));
        	}
        }
        if ( empty($search['publishStartdate']) && !empty($search['publishEnddate']) ) {
        	$qb->andWhere('policy.publishEnddate <= :publishEnddate')->setParameter(':publishEnddate', strtotime($search['publishEnddate']));
        }
        $qb->setFirstResult($firstResult)->setMaxResults($resultPerPage);
        return $qb->getQuery()->getArrayResult();
    }
    
    public function getSurveyPolicyBySearchAndPage($search, $page, $resultPerPage)
    {
    	$datetime = new \DateTime('now');
    	if ($page < 1) $page = 1;
    	$firstResult = ($page - 1) * $resultPerPage;
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();
    	$qb->select('policy')->from('\Application\Entity\Policy', 'policy');
    
    	$qb->orderBy('policy.name', 'DESC')
    	->addOrderBy('policy.bureauId', 'DESC')
    	->addOrderBy('policy.departmentId', 'DESC')
    	->addOrderBy('policy.divisionId', 'DESC')
    	->addOrderBy('policy.publishStartdate', 'DESC')
    	->addOrderBy('policy.publishEnddate', 'DESC')
    	->addOrderBy('policy.id', 'DESC');
    
    	if (isset($search['txtKeyword']) && $search['txtKeyword'] != '') {
    		$qb->andWhere($qb->expr()->like('policy.name', ':keyword'))
    		->setParameter(':keyword', '%' . trim($search['txtKeyword']) . '%');
    	}
    	if (!empty($search['ddlBureauId'])) {
    		$qb->andWhere('policy.bureauId = :bureauId')->setParameter(':bureauId', $search['ddlBureauId']);
    	}
    	if (!empty($search['ddlDepartmentId'])) {
    		$qb->andWhere('policy.departmentId = :departmentId')->setParameter(':departmentId', $search['ddlDepartmentId']);
    	}
    	if (!empty($search['ddlDivisionId'])) {
    		$qb->andWhere('policy.divisionId = :divisionId')->setParameter(':divisionId', $search['ddlDivisionId']);
    	}
    	if (isset($search['cbAttentionFlag']) && intval($search['cbAttentionFlag']) > 0) {
    		$qb->andWhere('policy.attentionFlag = :attentionFlag')->setParameter(':attentionFlag', intval($search['cbAttentionFlag']));
    	}
    	if (isset($search['cbTypePolicy']) && is_array($search['cbTypePolicy'])) {
    		$qbTypePolicy = $em->createQueryBuilder();
    		$orXTypePolicy = $qbTypePolicy->expr()->orX();
    		if (in_array(ApplicationConst::POLICY_TYPE_EDITING, $search['cbTypePolicy'])) {
    			$orXTypePolicy->add($qb->expr()->eq('policy.isDraft', 1));
    		}
    		if (in_array(ApplicationConst::POLICY_TYPE_WAITING_PUBLIC, $search['cbTypePolicy'])) {
    			$orXTypePolicy->add($qb->expr()->andX(
    					$qb->expr()->orX(
    							$qb->expr()->andX(
    									$qb->expr()->gt('policy.publishStartdate', $datetime->getTimestamp()),
    									$qb->expr()->isNotNull('policy.publishStartdate')
    							),
    							$qb->expr()->andX(
    									$qb->expr()->isNotNull('policy.publishEnddate'),
    									$qb->expr()->isNull('policy.publishStartdate'),
    									$qb->expr()->gt('policy.publishEnddate', $datetime->getTimestamp())
    							),
    							$qb->expr()->andX(
    									$qb->expr()->isNull('policy.publishEnddate'),
    									$qb->expr()->isNull('policy.publishStartdate')
    							)
    					),
    					$qb->expr()->eq('policy.isDraft', 0)
    			));
    		}
    		if (in_array(ApplicationConst::POLICY_TYPE_PUBLIC, $search['cbTypePolicy'])) {
    			$orXTypePolicy->add($qb->expr()->andX(
    					$qb->expr()->orX(
    							$qb->expr()->andX(
    									$qb->expr()->isNotNull('policy.publishStartdate'),
    									$qb->expr()->isNotNull('policy.publishEnddate'),
    									$qb->expr()->lte('policy.publishStartdate', $datetime->getTimestamp()),
    									$qb->expr()->gte('policy.publishEnddate', $datetime->getTimestamp())
    							),
    							$qb->expr()->andX(
    									$qb->expr()->isNotNull('policy.publishStartdate'),
    									$qb->expr()->isNull('policy.publishEnddate'),
    									$qb->expr()->lte('policy.publishStartdate', $datetime->getTimestamp())
    							)
    					),
    					$qb->expr()->eq('policy.isDraft', 0)
    			));
    		}
    		if (in_array(ApplicationConst::POLICY_TYPE_PRIVATE, $search['cbTypePolicy'])) {
    			$orXTypePolicy->add($qb->expr()->andX(
    					$qb->expr()->andX(
    							$qb->expr()->isNotNull('policy.publishEnddate'),
    							$qb->expr()->lt('policy.publishEnddate', $datetime->getTimestamp())
    					),
    					$qb->expr()->eq('policy.isDraft', 0)
    			));
    		}
    		$qb->andWhere($orXTypePolicy);
    	}
    	if ( !empty($search['publishStartdate']) ) {
    		$qb->andWhere('policy.publishStartdate >= :publishStartdate')->setParameter(':publishStartdate', strtotime($search['publishStartdate']));
    		if ( !empty($search['publishEnddate']) ) {
    			$qb->andWhere('policy.publishEnddate <= :publishEnddate')->setParameter(':publishEnddate', strtotime($search['publishEnddate']));
    		}
    	}
    	if ( empty($search['publishStartdate']) && !empty($search['publishEnddate']) ) {
    		$qb->andWhere('policy.publishEnddate <= :publishEnddate')->setParameter(':publishEnddate', strtotime($search['publishEnddate']));
    	}
    	$qb->setFirstResult($firstResult)->setMaxResults($resultPerPage);
    	return $qb->getQuery()->getArrayResult();
    }

    public function getDataByArrayPolicy($policyIds, $search = array())
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('policy')->from('\Application\Entity\Policy', 'policy')
            ->where($qb->expr()->in('policy.id', ':policyId'))->setParameter(':policyId', $policyIds);

        if (!empty($search['bureauId'])) {
            $qb->andWhere('policy.bureauId = :bureauId')->setParameter(':bureauId', $search['bureauId']);
        }
        if (!empty($search['departmentId'])) {
            $qb->andWhere('policy.departmentId = :departmentId')->setParameter(':departmentId', $search['departmentId']);
        }
        if (!empty($search['divisionId'])) {
            $qb->andWhere('policy.divisionId = :divisionId')->setParameter(':divisionId', $search['divisionId']);
        }
        return $qb->getQuery()->getArrayResult();
    }

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\Policy', 'policy')
            ->where($qb->expr()->in('policy.id', ':policyId'))->setParameter(':policyId', $policyIds);

        return $qb->getQuery()->execute();
    }

    public function updatePublishDateByListPolicy($policyIds, $isUpdateStartDate = 0, $isUpdateEndDate = 0)
    {
        if (!is_array($policyIds) || ($isUpdateStartDate == 0 && $isUpdateEndDate == 0)) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\Policy', 'policy');
        if ($isUpdateStartDate > 0) {
            $qb->set('policy.publishStartdate', DateHelper::getCurrentTimeStamp());

        }
        if ($isUpdateEndDate > 0) {
            $qb->set('policy.publishEnddate', DateHelper::getCurrentTimeStamp());
        }
        $qb->where($qb->expr()->in('policy.id', ':policyId'))->setParameter(':policyId', $policyIds);

        return $qb->getQuery()->execute();
    }

    private function createQueryAttr($search)
    {
        // prepare search attribute
        $attrArray = array();
        $countAttrType = 0;
        $attributes = array('searchContent', 'searchField', 'searchTargetPeople', 'searchTargetJob',
            'searchAmount', 'searchNumberPeople', 'searchArea');

        foreach ($attributes as $attribute) {
            if (!empty($search[$attribute]) && is_array($search[$attribute])) {
                $attrArray = array_merge_recursive($attrArray, $search[$attribute]);
                $countAttrType++;
            }
        }
        
        return array($attrArray, $countAttrType);
    }

    public function getPublishPoliciesBySearch($search, $filter, $userId)
    {
        $sqlQuery = $this->createPolicySearchQuery($search, $filter);

        // create execute object
        $stmt = $this->createStmtObjectPolicySearch($sqlQuery, $search, $filter, $userId);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPublishPoliciesBySearchPaging($search, $firstResult, $resultPerPage, $filter, $userId)
    {
        $sqlQuery = $this->createPolicySearchQuery($search, $filter);
        // paging
        $sqlQuery = "SELECT * FROM ($sqlQuery) as p LIMIT $firstResult, $resultPerPage";

        // create execute object
        $stmt = $this->createStmtObjectPolicySearch($sqlQuery, $search, $filter, $userId);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNumberPublishPoliciesBySearch($search, $filter, $userId)
    {
        $sqlQuery = $this->createPolicySearchQuery($search, $filter);

        // count number records
        $sqlQuery = "SELECT COUNT(*) FROM ($sqlQuery) as p";

        // create execute object
        $stmt = $this->createStmtObjectPolicySearch($sqlQuery, $search, $filter, $userId);

        $stmt->execute();
        return intval($stmt->fetchColumn());
    }

    public function createPolicySearchQuery($search, $filter)
    {
        list($sqlQuery, $sqlOrder) = $this->createPolicySearchQueryForUnion($search, $filter);
        list($sqlQueryTemp, $sqlOrder) = $this->createPolicySearchQueryForUnion($search, $filter, $isTemp = true);
        $sqlQuery = "$sqlQuery UNION $sqlQueryTemp $sqlOrder";
        return $sqlQuery;
    }

    public function createPolicySearchQueryForUnion($search, $filter, $isTemp = false)
    {
        $em = $this->getEntityManager();

        $subFixTable = $isTemp ? 'Temp' : '';
        // create table name
        $tablePolicy = $em->getClassMetadata('Application\Entity\Policy' . $subFixTable)->getTableName();
        $tableRecruitmentTime = $em->getClassMetadata('Application\Entity\RecruitmentTime' . $subFixTable)->getTableName();
        $tablePolicyView = $em->getClassMetadata('Application\Entity\PolicyView')->getTableName();
        $tablePolicyFavourite = $em->getClassMetadata('Application\Entity\PolicyFavourite')->getTableName();

        // create sql
        $sqlSelectArray = array('p.id, p.name, p.content, p.pdfFile, p.publishStartdate, p.publishEnddate, p.isDraft
                        , p.recruitmentFlag, p.emailSettingDate, p.createDate, p.detailRecruitmentTime, pf.userId,
                      MIN(
                        CASE
                            WHEN p.recruitmentFlag = 1 THEN :any_time_recruit
                            ELSE
                                CASE
                                    WHEN rt.startDate > :currentTime THEN
                                        CASE
                                            WHEN rt.startDate - :currentTime < :before_soon THEN :before_soon_recruit
                                            ELSE :before_recruit
                                        END
                                    WHEN rt.startDate <= :currentTime THEN
                                        CASE
                                            WHEN rt.deadline IS NULL AND rt.endDate >= :currentTime THEN 
                                                CASE 
                                                    WHEN rt.endDate - :currentTime < :deadline_soon THEN :soon_recruitment
                                                    ELSE :in_recruit
                                                END
                                            WHEN rt.deadline IS NOT NULL AND rt.deadline >= :currentTime THEN
                                                CASE
                                                    WHEN rt.deadline - :currentTime < :deadline_soon THEN :soon_recruitment
                                                    ELSE :in_recruit
                                                END
                                            ELSE :after_recruit
                                        END
                                    ELSE :after_recruit
                                END
                        END
                      ) as recruitmentStatus,
                      MIN(
                        CASE
                            WHEN rt.startDate <= :currentTime THEN
                                CASE
                                    WHEN rt.deadline IS NULL AND rt.endDate >= :currentTime THEN
                                        rt.endDate - :currentTime
                                    WHEN rt.deadline IS NOT NULL AND rt.deadline >= :currentTime THEN
                                        rt.deadline - :currentTime
                                    ELSE :max_int
                                END
                            ELSE :max_int
                        END
                    ) as remainRecruitmentDate');

        $sqlFrom = ' FROM ' . $tablePolicy . ' as p 
                         LEFT JOIN ' . $tableRecruitmentTime . ' as rt ON p.id = rt.policyId
                         LEFT JOIN ' . $tablePolicyFavourite . ' as pf ON p.id = pf.policyId AND pf.userId = :userId';

        $sqlWhereArray = array('p.isDraft = ' . self::NOT_DRAFT . '
                        AND (rt.policyId IS NOT NULL OR p.recruitmentFlag = 1)
                        AND ((p.publishStartdate IS NOT NULL AND p.publishEnddate IS NOT NULL 
                               AND p.publishStartdate <= :currentTime && p.publishEnddate >= :currentTime)
                              OR 
                             (p.publishStartdate IS NOT NULL AND p.publishEnddate IS NULL AND p.publishStartdate <= :currentTime )
                             )');
        if ($isTemp) {
            array_push($sqlWhereArray, 'p.updateDateSchedule >= :currentTime');
        } else {
            array_push($sqlWhereArray, '(p.updateDateSchedule < :currentTime OR p.updateDateSchedule IS NULL)');
        }

        $sqlGroupByArray = array('p.id');
        $sqlOrderArray = array('publishStartdate DESC');
        $sqlHavingArray = array();

        if ($filter == 'deadline') {
            array_push($sqlWhereArray, '(p.recruitmentFlag = 0 OR p.recruitmentFlag IS NULL)');
            array_push($sqlHavingArray, ' recruitmentStatus = :soon_recruitment');
            array_unshift($sqlOrderArray, 'remainRecruitmentDate ASC');
        }

        if ($filter == 'access') {
            $tablePolicyViewStatistic = $em->getRepository('Application\Entity\PolicyViewStatistic')
                ->getQuerySelectYearViewStatisticGroup();
            $sqlFrom .= ' LEFT JOIN (' . $tablePolicyViewStatistic . ') as pvs
                            ON pvs.policyId = p.id';
            array_push($sqlSelectArray, 'MAX(pvs.totalView) as totalView');
            array_unshift($sqlOrderArray, 'totalView DESC');
        }

        if ($filter == 'recent') {
            $sqlFrom .= ' LEFT JOIN ' . $tablePolicyView . ' as pv 
                            ON pv.policyId = p.id';
            array_push($sqlSelectArray, 'pv.viewDate');
            array_push($sqlWhereArray, 'pv.userId = :userId');
            array_push($sqlGroupByArray, 'pv.id');
            array_unshift($sqlOrderArray, 'viewDate DESC');
        }

        if ($filter == 'mail') {
            array_push($sqlWhereArray, 'p.emailNotificationFlag = 1');
        }

        if ($filter == 'like') {
            array_push($sqlWhereArray, 'pf.userId IS NOT NULL');
        }

        $attrArray = array();
        $countAttrType = 0;
        if ($filter != 'like' && $filter != 'recent' && $filter != 'mail') {

            // magic numbers please refer policy_attribute table.
            if (!empty($search['searchAmount']) && is_array($search['searchAmount'])) {
                $searchAmountConfig = \Application\Utils\ApplicationConst::SEARCH_AMOUNT;
                $searchAmount = $search['searchAmount'];
                $search["searchAmount"] = array();
                foreach($searchAmount as $attrId){
                    $search["searchAmount"] = array_merge($search["searchAmount"], $searchAmountConfig[$attrId]);
                }
                $search['searchAmount'] = array_unique($search['searchAmount'], SORT_REGULAR);
                sort($search['searchAmount']);
            }

            if (!empty($search['searchNumberPeople']) && is_array($search['searchNumberPeople'])) {
                $searchNumberPeopleConfig = \Application\Utils\ApplicationConst::SEARCH_NUMBER_PEOPLE;
                $searchNumberPeople = $search['searchNumberPeople'];
                $search["searchNumberPeople"] = array();
                foreach($searchNumberPeople as $attrId){
                    $search["searchNumberPeople"] = array_merge($search["searchNumberPeople"], $searchNumberPeopleConfig[$attrId]);
                }
                $search['searchNumberPeople'] = array_unique($search['searchNumberPeople'], SORT_REGULAR);
                sort($search['searchNumberPeople']);
            }

            list($attrArray, $countAttrType) = $this->createQueryAttr($search);
            $queryAttributeKeyWord = $em->getRepository('Application\Entity\PolicyAttributeMapping')
                ->createQueryPolicyIdByFreeWord($isTemp);
            array_push($sqlWhereArray, '(p.name LIKE :keyword OR p.shortName LIKE :keyword OR p.purpose LIKE :keyword 
                                      OR p.detailOfSupportArea LIKE :keyword OR p.content LIKE :keyword OR p.contact LIKE :keyword
                                      OR p.id IN (' . $queryAttributeKeyWord . '))');
        }

        $attrArray = array_merge_recursive($attrArray, array(53, 54));
        $countAttrType++;

        if (!empty($attrArray)) {
            $queryByAttributes = $em->getRepository('Application\Entity\PolicyAttributeMapping')
                ->createQueryPolicyIdByAttributes($attrArray, $countAttrType, $isTemp);
            array_push($sqlWhereArray, 'p.id IN (' . $queryByAttributes . ')');
        }

        if (!empty($search['searchPolicyType']) && is_array($search['searchPolicyType'])) {
            if (in_array(ApplicationConst::TYPE_IN_RECRUITMENT_TIME, $search['searchPolicyType'])) {
                array_push($search['searchPolicyType'], ApplicationConst::TYPE_RECRUITMENT_EXPIRE_SOON);
                array_push($search['searchPolicyType'], ApplicationConst::TYPE_ANYTIME_RECRUITMENT);
            }
            if (in_array(ApplicationConst::TYPE_BEFORE_RECRUITMENT_TIME, $search['searchPolicyType'])) {
                array_push($search['searchPolicyType'], ApplicationConst::TYPE_BEFORE_SOON_RECRUITMENT_TIME);
            }
            array_push($sqlHavingArray, ' recruitmentStatus IN (' . implode(',', $search['searchPolicyType']) . ')');
        }

        $sqlSelect = !empty($sqlSelectArray) ? 'SELECT ' . implode(', ', $sqlSelectArray) : '';
        $sqlWhere = !empty($sqlWhereArray) ? ' WHERE ' . implode(' AND ', $sqlWhereArray) : '';
        $sqlGroupBy = !empty($sqlGroupByArray) ? ' GROUP BY ' . implode(', ', $sqlGroupByArray) : '';
        $sqlOrder = !empty($sqlOrderArray) ? ' ORDER BY ' . implode(', ', $sqlOrderArray) : '';
        $sqlHaving = !empty($sqlHavingArray) ? ' HAVING ' . implode(' AND ', $sqlHavingArray) : '';

        $sqlQuery = $sqlSelect . $sqlFrom . $sqlWhere . $sqlGroupBy . $sqlHaving;

        return array($sqlQuery, $sqlOrder);
    }

    private function createStmtObjectPolicySearch($sqlQuery, $search, $filter, $userId)
    {
        $em = $this->getEntityManager();
        $con = $em->getConnection();
        $stmt = $con->prepare($sqlQuery);
        $stmt->bindValue(':soon_recruitment', ApplicationConst::TYPE_RECRUITMENT_EXPIRE_SOON);
        $stmt->bindValue(':in_recruit', ApplicationConst::TYPE_IN_RECRUITMENT_TIME);
        $stmt->bindValue(':before_soon_recruit', ApplicationConst::TYPE_BEFORE_SOON_RECRUITMENT_TIME);
        $stmt->bindValue(':before_recruit', ApplicationConst::TYPE_BEFORE_RECRUITMENT_TIME);
        $stmt->bindValue(':after_recruit', ApplicationConst::TYPE_AFTER_RECRUITMENT_TIME);
        $stmt->bindValue(':any_time_recruit', ApplicationConst::TYPE_ANYTIME_RECRUITMENT);
        $stmt->bindValue(':deadline_soon', PolicyManagementService::POLICY_EXPIRE_SOON_TIME);
        $stmt->bindValue(':before_soon', PolicyManagementService::POLICY_BEFORE_SOON_TIME);
        $stmt->bindValue(':currentTime', DateHelper::getCurrentTimeStamp());
        $stmt->bindValue(':max_int', PHP_INT_MAX, \PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        if ($filter == 'access') {
            $oneYearBefore = intval(DateHelper::getCurrentTimeStamp() - self::ONE_YEAR_TIME);
            $stmt->bindValue(':date', DateHelper::convertTimestampToString($oneYearBefore, "Y-m-d"));
        }
        if (!in_array($filter, array('recent', 'like', 'mail'))) {
            $stmt->bindValue(':keyword', '%' . trim(isset($search['searchText']) ? $search['searchText'] : '') . '%', \PDO::PARAM_STR);
        }
        return $stmt;
    }

    public function updatePublishDateIsNullByListPolicy($policyIds)
    {
        if (!is_array($policyIds)) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\Policy', 'policy');

        $qb->set('policy.publishEnddate', ':publishDate')->setParameter(':publishDate', null);
        $qb->where($qb->expr()->in('policy.id', ':policyId'))->setParameter(':policyId', $policyIds);
        $qb->andWhere($qb->expr()->lte('policy.publishEnddate', ':currentTime'))->setParameter(':currentTime', DateHelper::getCurrentTimeStamp());

        return $qb->getQuery()->execute();
    }

    public function getPoliciesForCompare($policyIds)
    {
        $em = $this->getEntityManager();
        $tablePolicy = $em->getClassMetadata('Application\Entity\Policy')->getTableName();
        $tablePolicyAttributeMapping = $em->getClassMetadata('Application\Entity\PolicyAttributeMapping')->getTableName();
        $tablePolicyAttributes = $em->getClassMetadata('Application\Entity\PolicyAttributes')->getTableName();

        $sql = 'SELECT p.id, p.shortName, p.name, p.content, p.purpose, p.detailOfSupportArea, p.detailRecruitmentTime,
                        p.homePage, p.contact ,pa.attributeType, GROUP_CONCAT(pa.`value` SEPARATOR \'\r\') as attributeValue
                FROM ' . $tablePolicy . ' as p
                LEFT JOIN ' . $tablePolicyAttributeMapping . ' as pam ON p.id = pam.policyId
                LEFT JOIN ' . $tablePolicyAttributes . ' as pa on pam.attributesPolicyId = pa.id
                WHERE p.id IN (' . implode(',', $policyIds) . ')
                GROUP BY p.id, pa.attributeType';

        $con = $em->getConnection();
        $stmt = $con->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPolicyHaveNotificationFlag($listPolicy)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p.id')
            ->from('\Application\Entity\Policy', 'p')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('p.emailNotificationFlag', ':emailNotificationFlag'),
                $qb->expr()->in('p.id', ":list_policy")
            ))
            ->setParameter(':emailNotificationFlag', 1)
            ->setParameter(':list_policy', $listPolicy);

        return $qb->getQuery()->getArrayResult();
    }
}
