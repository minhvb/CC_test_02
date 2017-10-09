<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\SearchHistoryRepository")
 * @ORM\Table(name="search_history")
 */
class SearchHistory extends Common
{
    /** @ORM\Column(type="integer", length=11, nullable=false) */
    protected $userId;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $name;

    /** @ORM\Column(type="text", nullable=true) */
    protected $content;

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
}