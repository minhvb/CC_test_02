<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicyAttributeMappingTempRepository")
 * @ORM\Table(name="policy_attribute_mapping_temp")
 */
class PolicyAttributeMappingTemp extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $policyId;

    /** @ORM\Column(type="smallint", length=4, nullable=true) */
    protected $attributesPolicyId;

    /** @ORM\Column(type="boolean", nullable=true, options={"default"=0}) */
    protected $attributeType;

    public function getPolicyId()
    {
        return $this->policyId;
    }

    public function getAttributesPolicyId()
    {
        return $this->attributesPolicyId;
    }

    public function setPolicyId($policyId)
    {
        $this->policyId = $policyId;
    }

    public function setAttributesPolicyId($attributesPolicyId)
    {
        $this->attributesPolicyId = $attributesPolicyId;
    }

    /**
     * @return mixed
     */
    public function getAttributeType()
    {
        return $this->attributeType;
    }

    /**
     * @param mixed $attributeType
     */
    public function setAttributeType($attributeType)
    {
        $this->attributeType = $attributeType;
    }


}
