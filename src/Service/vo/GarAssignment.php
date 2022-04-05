<?php

namespace eduMedia\GarApiBundle\Service\vo;

class GarAssignment
{

    private string $category = 'transferable';
    private string $type = 'ETABL';
    private string $numGlobalLicence = 'ILLIMITE';
    private array $audience = [];

    public const AUDIENCE_STUDENT = 'ELEVE';
    public const AUDIENCE_TEACHER = 'ENSEIGNANT';
    public const AUDIENCE_DOCUMENTALIST = 'DOCUMENTALISTE';
    public const AUDIENCE_OTHER = 'AUTRE PERSONNEL';

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return GarAssignment
     */
    public function setCategory(string $category): GarAssignment
    {
        $this->category = $category;

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
     * @return GarAssignment
     */
    public function setType(string $type): GarAssignment
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumGlobalLicence(): string
    {
        return $this->numGlobalLicence;
    }

    /**
     * @param string $numGlobalLicence
     * @return GarAssignment
     */
    public function setNumGlobalLicence(string $numGlobalLicence): GarAssignment
    {
        $this->numGlobalLicence = $numGlobalLicence;

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
     * @return GarAssignment
     */
    public function setAudience(array $audience): GarAssignment
    {
        $this->audience = $audience;

        return $this;
    }

    public function getAudienceTags(): string
    {
        $parts = [];
        foreach ($this->audience as $item) {
            $parts []= sprintf("<publicCible>%s</publicCible>", $item);
        }
        return implode("\r\n    ", $parts);
    }

}