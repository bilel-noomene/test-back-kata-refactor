<?php

namespace App\Entity;

class Quote
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $siteId;

    /**
     * @var int
     */
    private $destinationId;

    /**
     * @var string
     */
    private $dateQuoted;

    public function __construct(int $id, int $siteId, int $destinationId, \DateTime $dateQuoted)
    {
        $this->id = $id;
        $this->siteId = $siteId;
        $this->destinationId = $destinationId;
        $this->dateQuoted = $dateQuoted;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Quote
     */
    public function setId(int $id): Quote
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     * @return Quote
     */
    public function setSiteId(int $siteId): Quote
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationId(): int
    {
        return $this->destinationId;
    }

    /**
     * @param int $destinationId
     * @return Quote
     */
    public function setDestinationId(int $destinationId): Quote
    {
        $this->destinationId = $destinationId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateQuoted(): \DateTime
    {
        return $this->dateQuoted;
    }

    /**
     * @param \DateTime $dateQuoted
     * @return Quote
     */
    public function setDateQuoted(\DateTime $dateQuoted): Quote
    {
        $this->dateQuoted = $dateQuoted;

        return $this;
    }
}