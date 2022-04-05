<?php

namespace eduMedia\GarApiBundle\Service\vo;

use DateTime;

interface GarCreatableSubscriptionInterface
{

    function getGarUai(): string;
    function getGarSubscriptionId(): string;
    function getGarResourceId(): string;
    function getGarSubscriptionFrom(): ?DateTime;
    function getGarSubscriptionTo(): ?DateTime;

}