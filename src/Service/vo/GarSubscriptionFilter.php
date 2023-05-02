<?php

namespace eduMedia\GarApiBundle\Service\vo;

class GarSubscriptionFilter
{

    public ?string $distributorId = null;
    public ?string $uai = null;
    public ?string $subscriptionId = null;
    public ?string $assignmentType = null;
    public ?string $assignmentCategory = null;
    public ?string $targetAudience = null;
    public ?string $resourceId = null;
    
    public function getNodeString(): string {

        $filters = [];

        if ($this->distributorId) {
            $filters['idDistributeurCom'] = $this->distributorId;
        }

        if ($this->uai) {
            $filters['uaiEtab'] = $this->uai;
        }

        if ($this->subscriptionId) {
            $filters['idAbonnement'] = $this->subscriptionId;
        }

        if ($this->assignmentType) {
            $filters['typeAffectation'] = $this->assignmentType;
        }

        if ($this->assignmentCategory) {
            $filters['categorieAffectation'] = $this->assignmentCategory;
        }

        if ($this->targetAudience) {
            $filters['publicCible'] = $this->targetAudience;
        }

        if ($this->resourceId) {
            $filters['codeProjetRessource'] = $this->resourceId;
        }
        
        $filterNodes = array_map(function($key, $value) {
            return "<filtre><filtreNom>{$key}</filtreNom><filtreValeur>{$value}</filtreValeur></filtre>";
        }, array_keys($filters), array_values($filters));
        
        return '<filtres xmlns="http://www.atosworldline.com/wsabonnement/v1.0/">'. implode('', $filterNodes) .'</filtres>';
    }

}