<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicyRepository")
 * @ORM\Table(name="policy")
 */
class Policy extends Common
{

    /** @ORM\Column(type="string", length=20, nullable=true) */
    protected $bureauId;

    /** @ORM\Column(type="string", length=20, nullable=true) */
    protected $departmentId;

    /** @ORM\Column(type="string", length=20, nullable=true) */
    protected $divisionId;

    /** @ORM\Column(type="string", length=100, nullable=true) */
    protected $shortName;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $name;

    /** @ORM\Column(type="boolean", nullable=true) */
    protected $recruitmentFlag;

    /** @ORM\Column(type="text", length=1000, nullable=true) */
    protected $content;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $homepage;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $agencyImplement;

    /** @ORM\Column(type="boolean", nullable=true) */
    protected $attentionFlag;

    /** @ORM\Column(type="boolean", nullable=true) */
    protected $recruitmentForm;

    /** @ORM\Column(type="string", length=1024, nullable=true) */
    protected $purpose;

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    protected $contact;

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    protected $pdfFile;

    /** @ORM\Column(type="smallint", length=4, nullable=true) */
    protected $supportArea;

    /** @ORM\Column(type="string", length=1024, nullable=true) */
    protected $detailOfSupportArea;

    /** @ORM\Column(type="text", length=1024, nullable=true) */
    protected $detailRecruitmentTime;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $publishStartdate;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $publishEnddate;

    /** @ORM\Column(type="smallint", length=4, nullable=true) */
    protected $emailNotificationFlag;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $emailSettingDate;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $createBy;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $updateBy;

    /** @ORM\Column(type="boolean", nullable=true) */
    protected $isDraft;

    /** @ORM\Column(type="text", length=1024, nullable=true) */
    protected $summaryUpdate;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $updateDateSchedule;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $updateDateDisplay;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $roleId;

    /**
     * @return mixed
     */
    public function getBureauId()
    {
        return $this->bureauId;
    }

    /**
     * @param mixed $bureauId
     */
    public function setBureauId($bureauId)
    {
        $this->bureauId = $bureauId;
    }

    /**
     * @return mixed
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @param mixed $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @return mixed
     */
    public function getDivisionId()
    {
        return $this->divisionId;
    }

    /**
     * @param mixed $divisionId
     */
    public function setDivisionId($divisionId)
    {
        $this->divisionId = $divisionId;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param mixed $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getRecruitmentFlag()
    {
        return $this->recruitmentFlag;
    }

    /**
     * @param mixed $recruitmentFlag
     */
    public function setRecruitmentFlag($recruitmentFlag)
    {
        $this->recruitmentFlag = $recruitmentFlag;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * @param mixed $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * @return mixed
     */
    public function getAgencyImplement()
    {
        return $this->agencyImplement;
    }

    /**
     * @param mixed $agencyImplement
     */
    public function setAgencyImplement($agencyImplement)
    {
        $this->agencyImplement = $agencyImplement;
    }

    /**
     * @return mixed
     */
    public function getAttentionFlag()
    {
        return $this->attentionFlag;
    }

    /**
     * @param mixed $attentionFlag
     */
    public function setAttentionFlag($attentionFlag)
    {
        $this->attentionFlag = $attentionFlag;
    }

    /**
     * @return mixed
     */
    public function getRecruitmentForm()
    {
        return $this->recruitmentForm;
    }

    /**
     * @param mixed $recruitmentForm
     */
    public function setRecruitmentForm($recruitmentForm)
    {
        $this->recruitmentForm = $recruitmentForm;
    }

    /**
     * @return mixed
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @param mixed $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getPdfFile()
    {
        return $this->pdfFile;
    }

    /**
     * @param mixed $pdfFile
     */
    public function setPdfFile($pdfFile)
    {
        $this->pdfFile = $pdfFile;
    }

    /**
     * @return mixed
     */
    public function getSupportArea()
    {
        return $this->supportArea;
    }

    /**
     * @param mixed $supportArea
     */
    public function setSupportArea($supportArea)
    {
        $this->supportArea = $supportArea;
    }

    /**
     * @return mixed
     */
    public function getDetailOfSupportArea()
    {
        return $this->detailOfSupportArea;
    }

    /**
     * @param mixed $detailOfSupportArea
     */
    public function setDetailOfSupportArea($detailOfSupportArea)
    {
        $this->detailOfSupportArea = $detailOfSupportArea;
    }

    /**
     * @return mixed
     */
    public function getDetailRecruitmentTime()
    {
        return $this->detailRecruitmentTime;
    }

    /**
     * @param mixed $detailRecruitmentTime
     */
    public function setDetailRecruitmentTime($detailRecruitmentTime)
    {
        $this->detailRecruitmentTime = $detailRecruitmentTime;
    }

    /**
     * @return mixed
     */
    public function getPublishStartdate()
    {
        return $this->publishStartdate;
    }

    /**
     * @param mixed $publishStartdate
     */
    public function setPublishStartdate($publishStartdate)
    {
        $this->publishStartdate = $publishStartdate;
    }

    /**
     * @return mixed
     */
    public function getPublishEnddate()
    {
        return $this->publishEnddate;
    }

    /**
     * @param mixed $publishEnddate
     */
    public function setPublishEnddate($publishEnddate)
    {
        $this->publishEnddate = $publishEnddate;
    }

    /**
     * @return mixed
     */
    public function getEmailNotificationFlag()
    {
        return $this->emailNotificationFlag;
    }

    /**
     * @param mixed $emailNotificationFlag
     */
    public function setEmailNotificationFlag($emailNotificationFlag)
    {
        $this->emailNotificationFlag = $emailNotificationFlag;
    }

    /**
     * @return mixed
     */
    public function getEmailSettingDate()
    {
        return $this->emailSettingDate;
    }

    /**
     * @param mixed $emailSettingDate
     */
    public function setEmailSettingDate($emailSettingDate)
    {
        $this->emailSettingDate = $emailSettingDate;
    }

    /**
     * @return mixed
     */
    public function getCreateBy()
    {
        return $this->createBy;
    }

    /**
     * @param mixed $createBy
     */
    public function setCreateBy($createBy)
    {
        $this->createBy = $createBy;
    }

    /**
     * @return mixed
     */
    public function getUpdateBy()
    {
        return $this->updateBy;
    }

    /**
     * @param mixed $updateBy
     */
    public function setUpdateBy($updateBy)
    {
        $this->updateBy = $updateBy;
    }

    /**
     * @return mixed
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * @param mixed $isDraft
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     * @return mixed
     */
    public function getSummaryUpdate()
    {
        return $this->summaryUpdate;
    }

    /**
     * @param mixed $summaryUpdate
     */
    public function setSummaryUpdate($summaryUpdate)
    {
        $this->summaryUpdate = $summaryUpdate;
    }

    /**
     * @return mixed
     */
    public function getUpdateDateSchedule()
    {
        return $this->updateDateSchedule;
    }

    /**
     * @param mixed $updateDateSchedule
     */
    public function setUpdateDateSchedule($updateDateSchedule)
    {
        $this->updateDateSchedule = $updateDateSchedule;
    }

    /**
     * @return mixed
     */
    public function getUpdateDateDisplay()
    {
        return $this->updateDateDisplay;
    }

    /**
     * @param mixed $updateDateDisplay
     */
    public function setUpdateDateDisplay($updateDateDisplay)
    {
        $this->updateDateDisplay = $updateDateDisplay;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param mixed $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }


}
