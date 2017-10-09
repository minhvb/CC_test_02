<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role extends Common
{
    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $title;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $description;
    
    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=0})
     */
    protected $position;
}