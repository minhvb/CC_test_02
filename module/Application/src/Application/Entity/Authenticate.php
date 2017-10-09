<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\AuthenticateRepository")
 * @ORM\Table(name="authenticate")
 */
class Authenticate extends Common
{
    /**
     * Foreign key reference to Role
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $roleId;

    /**
     * Foreign key reference to Action
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $actionId;

    /** @ORM\Column(type="boolean", nullable=false, options={"default":0, "comment":"0. active, 1. inactive"}) */
    protected $status;
}
