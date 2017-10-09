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
use Application\Entity\Notice;

class NoticeRepository extends EntityRepository
{

    public function getListNotice($firstResult = null, $resultPerPage = null) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('n')
            ->from('\Application\Entity\Notice', 'n')
            ->where('n.id IS NOT NULL');

        if ($firstResult !== null && $resultPerPage !== null) {
            $qb->setFirstResult($firstResult)->setMaxResults($resultPerPage);
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function getListNoticeBySearch($firstResult = null, $resultPerPage = null,$search = array()) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('n')
            ->from('\Application\Entity\Notice', 'n')
            ->where('n.id IS NOT NULL');

        if (!empty($search)) {
            if ($search['keyWord']) {
                $search['keyWord'] = strip_tags($search['keyWordy']);
                $qb->andWhere('n.title LIKE :keyWord')
                    ->setParameter(':keyWord', '%'.$search['keyWord'].'%');
            }

            if ($search['firstPublicDate']) {
                $qb->andWhere('n.firstPublicDate > :firstPublicDate')
                    ->setParameter(':firstPublicDate', DateHelper::convertDateToNumber($search['firstPublicDate']));
            }

            if ($search['lastPublicDate']) {
                $qb->andWhere('n.lastPublicDate < :lastPublicDate')
                    ->setParameter(':lastPublicDate', DateHelper::convertDateToNumber($search['lastPublicDate']));
            }
        }

        if (isset($search['noticeStatus']) && !empty($search['noticeStatus'])) {

            if (count($search['noticeStatus']) == 1) {
                if ($search['noticeStatus'][0] == 1) {
                    $qb->andWhere('n.firstPublicDate > :currentTime')
                    ->setParameter(':currentTime', time());
                } elseif ($search['noticeStatus'][0] == 2) {
                    $qb->andWhere('n.firstPublicDate < :currentTime')
                        ->andWhere('n.lastPublicDate > :currentTime OR n.lastPublicDate IS NULL')
                        ->setParameter(':currentTime', time());
                } else {
                    $qb->andWhere('n.lastPublicDate < :currentTime')
                    ->setParameter(':currentTime', time());
                }
            } elseif (count($search['noticeStatus']) == 2) {
                if (in_array(1, $search['noticeStatus']) && in_array(2, $search['noticeStatus'])) {
                    $qb->andWhere('n.firstPublicDate < :currentTime AND (n.lastPublicDate > :currentTime OR n.lastPublicDate IS NULL) OR n.firstPublicDate > :currentTime')
                        ->setParameter(':currentTime', time());
                } elseif (in_array(1, $search['noticeStatus']) && in_array(3, $search['noticeStatus'])) {
                    $qb->andWhere('n.firstPublicDate > :currentTime OR n.lastPublicDate < :currentTime')
                    ->setParameter(':currentTime', time());
                } else {
                    $qb->andWhere('n.firstPublicDate < :currentTime AND (n.lastPublicDate > :currentTime OR n.lastPublicDate IS NULL) OR n.lastPublicDate < :currentTime')
                        ->setParameter(':currentTime', time());
                }
            } else {
                $qb->andWhere('n.firstPublicDate < :currentTime AND n.lastPublicDate > :currentTime OR n.firstPublicDate > :currentTime OR n.lastPublicDate < :currentTime')
                        ->setParameter(':currentTime', time());
            }
        }

        if ($firstResult !== null && $resultPerPage !== null) {
            $qb->setFirstResult($firstResult)->setMaxResults($resultPerPage);
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function getNoticeDetail($noticeId){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('n.id as noticeId, n.title, n.description, n.firstPublicDate, n.lastPublicDate, n.type, n.surveyId, n.no')
            ->from('\Application\Entity\Notice', 'n')
            ->where('n.id = :noticeId')
            ->setParameter(':noticeId',(int)$noticeId);

        return $qb->getQuery()->getSingleResult();
    }

    public function getTotalNotices($search) {
        $parameters = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('count(n.id)')
            ->from('\Application\Entity\Notice', 'n');

        if (!empty($search)) {
            if ($search['keyWord']) {
                $search['keyWord'] = strip_tags($search['keyWordy']);
                $qb->andWhere('n.title LIKE :keyWord')
                    ->setParameter(':keyWord', '%'.$search['keyWord'].'%');
            }

            if ($search['firstPublicDate']) {
                $qb->andWhere('n.firstPublicDate > :firstPublicDate')
                    ->setParameter(':firstPublicDate', DateHelper::convertDateToNumber($search['firstPublicDate']));
            }

            if ($search['lastPublicDate']) {
                $qb->andWhere('n.lastPublicDate < :lastPublicDate')
                    ->setParameter(':lastPublicDate', DateHelper::convertDateToNumber($search['lastPublicDate']));
            }
        }

        if (isset($search['noticeStatus']) && !empty($search['noticeStatus'])) {

            if (count($search['noticeStatus']) == 1) {
                if ($search['noticeStatus'][0] == 1) {
                    $qb->andWhere('n.firstPublicDate > :currentTime')
                    ->setParameter(':currentTime', time());
                } elseif ($search['noticeStatus'][0] == 2) {
                    $qb->andWhere('n.firstPublicDate < :currentTime')
                        ->andWhere('n.lastPublicDate > :currentTime OR n.lastPublicDate IS NULL')
                        ->setParameter(':currentTime', time());
                } else {
                    $qb->andWhere('n.lastPublicDate < :currentTime')
                    ->setParameter(':currentTime', time());
                }
            } elseif (count($search['noticeStatus']) == 2) {
                if (in_array(1, $search['noticeStatus']) && in_array(2, $search['noticeStatus'])) {
                    $qb->andWhere('n.firstPublicDate < :currentTime AND (n.lastPublicDate > :currentTime OR n.lastPublicDate IS NULL) OR n.firstPublicDate > :currentTime')
                        ->setParameter(':currentTime', time());
                } elseif (in_array(1, $search['noticeStatus']) && in_array(3, $search['noticeStatus'])) {
                    $qb->andWhere('n.firstPublicDate > :currentTime OR n.lastPublicDate < :currentTime')
                    ->setParameter(':currentTime', time());
                } else {
                    $qb->andWhere('n.firstPublicDate < :currentTime AND (n.lastPublicDate > :currentTime OR n.lastPublicDate IS NULL) OR n.lastPublicDate < :currentTime')
                        ->setParameter(':currentTime', time());
                }
            } else {
                $qb->andWhere('n.firstPublicDate < :currentTime AND n.lastPublicDate > :currentTime OR n.firstPublicDate > :currentTime OR n.lastPublicDate < :currentTime')
                        ->setParameter(':currentTime', time());
            }
        }

        $totalResults = $qb->getQuery()->getSingleScalarResult();

        return $totalResults;
    }

    public function addNotice($data) {
        if (!isset($data['surveyId'])) {
            $data['surveyId'] = 0;
        }
        
        $em = $this->getEntityManager();
        $firstPublicDate = DateHelper::convertDateToNumber($data['noticeFirstPublicDate'].' 00:00:00');
        $lastPublicDate = DateHelper::convertDateToNumber($data['noticeLastPublicDate'].' 23:59:59');

        $notice = new Notice();
        $notice->setSurveyId((int)$data['surveyId']);
        $notice->setTitle(strip_tags($data['noticeTitle']));
        $notice->setDescription(strip_tags($data['noticeDescription']));
        $notice->setType($data['noticeType']);
        $notice->setIsRead(0);
        $notice->setFirstPublicDate($firstPublicDate);
        $notice->setLastPublicDate($lastPublicDate);
        $notice->setCreateDate(time());
        $notice->setUpdateDate(time());

        $em->persist($notice);
        $em->flush();
        
        return $notice->getId();
    }

    public function updateNotice($data) {
        if (!isset($data['surveyId'])) {
            $data['surveyId'] = null;
        }
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $firstPublicDate = DateHelper::convertDateToNumber($data['noticeFirstPublicDate'].' 00:00:00');
        $lastPublicDate = DateHelper::convertDateToNumber($data['noticeLastPublicDate'].' 23:59:59');

        $qb->update('\Application\Entity\Notice', 'notice')
           ->set('notice.title', ':title')
           ->set('notice.description', ':description')
           ->set('notice.surveyId', ':surveyId')
           ->set('notice.firstPublicDate', ':firstPublicDate')
           ->set('notice.lastPublicDate', ':lastPublicDate')
           ->set('notice.updateDate', ':updateDate')
           ->where('notice.id = :noticeId')
           ->setParameter(':title', strip_tags($data['noticeTitle']))
           ->setParameter(':description', strip_tags($data['noticeDescription']))
           ->setParameter(':surveyId', $data['surveyId'])
           ->setParameter(':firstPublicDate', $firstPublicDate)
           ->setParameter(':lastPublicDate', $lastPublicDate)
           ->setParameter(':updateDate', time())
           ->setParameter(':noticeId', (int)$data['noticeId']);

        $qb->getQuery()->execute();
        return $data['noticeId'];
    }

    public function delelteListNotice($listNotice) {
        if (is_array($listNotice)) {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->delete('\Application\Entity\Notice', 'n')
                ->where($qb->expr()->in('n.id', $listNotice ));

            return $qb->getQuery()->execute();
        }
    }

    public function getSurveyIdByListNotice($listNotice) {
        if (is_array($listNotice)) {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select('n.surveyId')
                ->from('\Application\Entity\Notice', 'n')
                ->where($qb->expr()->in('n.id', $listNotice ));

            return $qb->getQuery()->getArrayResult();
        }
    }

    public function publicNotice($listNotice) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->update('\Application\Entity\Notice', 'notice')
            ->set('notice.firstPublicDate', time())
            ->set('notice.lastPublicDate', 'NULL')
            ->where($qb->expr()->in('notice.id', $listNotice ));

        return $qb->getQuery()->execute();
    }

    public function privateNotice($listNotice) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->update('\Application\Entity\Notice', 'notice')
            ->set('notice.firstPublicDate', ':currentTime')
            ->set('notice.lastPublicDate', ':currentTime')
            ->where($qb->expr()->in('notice.id', $listNotice ))
            ->setParameter(':currentTime', time() - 86400);

        return $qb->getQuery()->execute();
    }
}
