<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\SettingMailRepository")
 * @ORM\Table(name="setting_mail")
 */
class SettingMail extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $userId;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $attributesPolicyId;

    /** @ORM\Column(columnDefinition="TINYINT DEFAULT 0 NOT NULL") */
    protected $attributeType;

    /** @ORM\Column(columnDefinition="TINYINT DEFAULT 0 NOT NULL") */
    protected $isDisplayed;
    
}