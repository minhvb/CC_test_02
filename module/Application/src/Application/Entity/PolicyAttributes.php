<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicyAttributesRepository")
 * @ORM\Table(name="policy_attributes")
 */
class PolicyAttributes extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $attributeType;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $position;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $value;
    
    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $valueOfSearch;

}
