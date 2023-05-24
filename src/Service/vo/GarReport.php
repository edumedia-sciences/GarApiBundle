<?php

namespace eduMedia\GarApiBundle\Service\vo;

use DateTimeImmutable;

class GarReport
{

    public const STATUS_ACKNOWLEDGED = "PRIS_EN_COMPTE";
    public const STATUS_NOT_ACKNOWLEDGED = "NON_PRIS_EN_COMPTE";
    public const STATUS_ALL = "TOUT";

    private string $name;
    private DateTimeImmutable $timestamp;
    private int $size;
    private string $status;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): GarReport
    {
        $this->name = $name;
        return $this;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): GarReport
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): GarReport
    {
        $this->size = $size;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): GarReport
    {
        $this->status = $status;
        return $this;
    }

}