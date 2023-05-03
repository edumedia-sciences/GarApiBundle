<?php

namespace eduMedia\GarApiBundle\Service\vo;

use DateTimeInterface;

interface GarCreatableSubscriptionInterface
{

    function getGarUai(): string;
    function getGarSubscriptionId(): string;
    function getGarSubscriptionFrom(): ?DateTimeInterface;
    function getGarSubscriptionTo(): ?DateTimeInterface;

}