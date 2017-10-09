<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Utils\DateHelper;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Common
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $createDate;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $updateDate;

    public function getId()
    {
        return $this->id;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    // Pre persist
    /**
     * @ORM\PrePersist
     */
    public function init()
    {
        if (!isset($this->createDate)) {
            $this->createDate = DateHelper::getCurrentTimeStamp();
        }
        $this->updateDate = DateHelper::getCurrentTimeStamp();
    }

    /**
     * @ORM\PreUpdate
     */
    public function update()
    {
        $this->updateAt = DateHelper::getCurrentTimeStamp();
    }

}
