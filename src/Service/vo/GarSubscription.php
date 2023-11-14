<?php

namespace eduMedia\GarApiBundle\Service\vo;

use DateTime;
use SimpleXMLElement;

class GarSubscription
{

    private string $subscriptionId;
    private string $distributorId;
    private string $resourceId;
    private DateTime $from;
    private DateTime $to;
    private string $uai;
    private array $audience;

    public const DATE_FORMAT = "Y-m-d\TH:i:s.uT";

    public static function createFromNode(SimpleXMLElement $xml): self {

        $data = [];

        foreach ($xml->children() as $child) {
            $childName = $child->getName();

            if (!array_key_exists($childName, $data)) {
                $data[$childName] = [];
            }

            $data[$childName][] = (string)$child;
        }

        foreach ($data as $key => $value) {
            if (count($value) === 1) {
                $data[$key] = $value[0];
            }
        }

        return (new self())
            ->setSubscriptionId($data['idAbonnement'])
            ->setDistributorId($data['idDistributeurCom'])
            ->setResourceId($data['idRessource'])
            ->setFrom(DateTime::createFromFormat(self::DATE_FORMAT, $data['debutValidite']))
            ->setTo(DateTime::createFromFormat(self::DATE_FORMAT, $data['finValidite']))
            ->setUai($data['uaiEtab'])
            ->setAudience($data['publicCible'])
            ;
    }

    /**
     * @return string
     */
    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    /**
     * @param string $subscriptionId
     * @return GarSubscription
     */
    public function setSubscriptionId(string $subscriptionId): GarSubscription
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getDistributorId(): string
    {
        return $this->distributorId;
    }

    /**
     * @param string $distributorId
     * @return GarSubscription
     */
    public function setDistributorId(string $distributorId): GarSubscription
    {
        $this->distributorId = $distributorId;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @param string $resourceId
     * @return GarSubscription
     */
    public function setResourceId(string $resourceId): GarSubscription
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @param DateTime $from
     * @return GarSubscription
     */
    public function setFrom(DateTime $from): GarSubscription
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTo(): DateTime
    {
        return $this->to;
    }

    /**
     * @param DateTime $to
     * @return GarSubscription
     */
    public function setTo(DateTime $to): GarSubscription
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getUai(): string
    {
        return $this->uai;
    }

    /**
     * @param string $uai
     * @return GarSubscription
     */
    public function setUai(string $uai): GarSubscription
    {
        $this->uai = $uai;

        return $this;
    }

    /**
     * @return array
     */
    public function getAudience(): array
    {
        return $this->audience;
    }

    /**
     * @param array $audience
     * @return GarSubscription
     */
    public function setAudience(array $audience): GarSubscription
    {
        $this->audience = $audience;

        return $this;
    }

}