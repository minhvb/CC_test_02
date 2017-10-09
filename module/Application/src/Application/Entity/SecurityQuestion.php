<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\SecurityQuestionRepository")
 * @ORM\Table(name="security_question")
 */
class SecurityQuestion extends Common
{
    /** @ORM\Column(type="string", length=500, nullable=true) */
    protected $content;
}