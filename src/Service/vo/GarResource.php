<?php

namespace eduMedia\GarApiBundle\Service\vo;

class GarResource
{

    private string $id;
    private string $type = 'ark';
    private string $label;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return GarResource
     */
    public function setId(string $id): GarResource
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return GarResource
     */
    public function setType(string $type): GarResource
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return GarResource
     */
    public function setLabel(string $label): GarResource
    {
        $this->label = $label;

        return $this;
    }

}