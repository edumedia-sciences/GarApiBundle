<?php

namespace eduMedia\GarApiBundle\Service;

use eduMedia\GarApiBundle\Service\vo\GarAssignment;
use eduMedia\GarApiBundle\Service\vo\GarCreatableSubscriptionInterface;
use eduMedia\GarApiBundle\Service\vo\GarResource;
use eduMedia\GarApiBundle\Service\vo\GarSubscription;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpClient\NativeHttpClient;

class GarApiService
{

    private string $distributorId;
    private string $sslCert;
    private string $sslKey;
    private string $remoteEnv;
    private string $cacheDirectory;

    private const ENDPOINT_PREFIXES = [
        'prod' => "https://abonnement.gar.education.fr",
        'preprod' => "https://abonnement.partenaire.test-gar.education.fr",
    ];

    public const DATE_FORMAT = "Y-m-d\TH:i:s";

    private ?array $cachedInstitutions = null;

    public function __construct(
        string $distributorId,
        string $sslCert,
        string $sslKey,
        string $remoteEnv,
        string $cacheDirectory
    ) {
        $this->distributorId = $distributorId;
        $this->sslCert = $sslCert;
        $this->sslKey = $sslKey;
        $this->remoteEnv = $remoteEnv;
        $this->cacheDirectory = $cacheDirectory;

        $this->client = new NativeHttpClient([
            'local_cert' => $this->sslCert,
            'local_pk'   => $this->sslKey,
            'headers'    => [
                "Content-type: application/xml",
                "Accept: application/xml",
            ],
        ]);

        if (!file_exists($sslCert)) {
            throw new FileNotFoundException(sprintf("SSL Cert: %s does not exist", $sslCert));
        }
        if (!file_exists($sslKey)) {
            throw new FileNotFoundException(sprintf("SSL Key: %s does not exist", $sslCert));
        }
        if (!is_dir($cacheDirectory)) {
            try {
                mkdir($cacheDirectory);
            } catch (Exception $e) {
                throw new FileNotFoundException("Could not find or create cache directory $cacheDirectory");
            }
        }
    }

    private function getEndpointPrefix(): string
    {
        return self::ENDPOINT_PREFIXES[$this->remoteEnv];
    }

    private function getEndpoint(string $path): string
    {
        return $this->getEndpointPrefix() . $path;
    }

    private function getInstitutionPath($extension): string
    {
        return $this->cacheDirectory . '/' . date('Y/m/d.') . $extension;
    }

    public function getInstitutions()
    {

        if (isset($this->cachedInstitutions)) {
            return $this->cachedInstitutions;
        }

        $xmlPath = $this->getInstitutionPath('xml');
        $phpPath = $this->getInstitutionPath('php');
        if (!file_exists($phpPath)) {
            $response = $this->client->request('GET', $this->getEndpoint('/etablissements/etablissements.xml'));

            if ($response->getStatusCode() === 200) {
                $content = $response->getContent();

                if (!is_dir(dirname($xmlPath))) {
                    mkdir(dirname($xmlPath), 0777, true);
                }

                file_put_contents($xmlPath, $content);
                $this->cachedInstitutions = $this->xmlToPhp($content, $phpPath);
            } else {
                $this->cachedInstitutions = [];
            }
        } else {
            $this->cachedInstitutions = include $phpPath;
        }

        return $this->cachedInstitutions;
    }

    private function xmlToPhp(string $xml, string $path): array
    {
        $xml = simplexml_load_string(
            $xml,
            "SimpleXMLElement",
            0,
            "http://www.atosworldline.com/listEtablissement/v1.0/"
        );
        $ar = array();

        foreach ($xml->children() as $institutionNode) {
            $institutionData = array();
            foreach ($institutionNode->children() as $child) {
                $institutionData[$child->getName()] = (string)$child;
            }
            $ar[$institutionData['uai']] = $institutionData;
        }

        file_put_contents($path, "<?php\nreturn " . var_export($ar, true) . ";");

        return $ar;
    }

    public function hasInstitution(string $uai): bool
    {
        return array_key_exists($uai, $this->getInstitutions());
    }

    /**
     * @return GarSubscription[]
     */
    public function getInstitutionSubscriptions(string $uai): array {

        $filter = <<<FILTER
            <filtres xmlns="http://www.atosworldline.com/wsabonnement/v1.0/">
                <filtre>
                    <filtreNom>idDistributeurCom</filtreNom>
                    <filtreValeur>{$this->distributorId}</filtreValeur>
                </filtre>
                <filtre>
                    <filtreNom>uaiEtab</filtreNom>
                    <filtreValeur>{$uai}</filtreValeur>
                </filtre>
            </filtres>
        FILTER;

        $response = $this->client->request('POST', $this->getEndpoint('/abonnements'), [
            'body' => $filter
        ]);

        $xml = simplexml_load_string($response->getContent());
        $subscriptions = array();

        foreach ($xml->children() as $subscriptionNode) {
            $subscriptions[] = GarSubscription::createFromNode($subscriptionNode);
        }

        return $subscriptions;
    }

    public function createSubscription(GarCreatableSubscriptionInterface $subscription, GarResource $resource, GarAssignment $assignment): bool
    {
        $input = <<<XML
            <abonnement xmlns="http://www.atosworldline.com/wsabonnement/v1.0/">
                <idAbonnement>{$subscription->getGarSubscriptionId()}</idAbonnement>
                <idDistributeurCom>{$this->distributorId}</idDistributeurCom>
                <idRessource>{$resource->getId()}</idRessource>
                <typeIdRessource>{$resource->getType()}</typeIdRessource>
                <libelleRessource>{$resource->getLabel()}</libelleRessource>
                <debutValidite>{$subscription->getGarSubscriptionFrom()->format(self::DATE_FORMAT)}</debutValidite>
                <finValidite>{$subscription->getGarSubscriptionTo()->format(self::DATE_FORMAT)}</finValidite>
                <uaiEtab>{$subscription->getGarUai()}</uaiEtab>
                <categorieAffectation>{$assignment->getCategory()}</categorieAffectation>
                <typeAffectation>{$assignment->getType()}</typeAffectation>
                <nbLicenceGlobale>{$assignment->getNumGlobalLicence()}</nbLicenceGlobale>
                {$assignment->getAudienceTags()}
            </abonnement>
        XML;

        $response = $this->client->request(
            "PUT",
            $this->getEndpoint("/" . $subscription->getGarSubscriptionId()),
            ['body' => $input]
        );

        return $response->getStatusCode() === 201;
    }

    private function getSubscriptionXml(string $subscriptionId): ?SimpleXMLElement {
        $filter = <<<FILTER
            <filtres xmlns="http://www.atosworldline.com/wsabonnement/v1.0/">
                <filtre>
                    <filtreNom>idAbonnement</filtreNom>
                    <filtreValeur>{$subscriptionId}</filtreValeur>
                </filtre>
            </filtres>
        FILTER;

        $response = $this->client->request('POST', $this->getEndpoint('/abonnements'), [
            'body' => $filter
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $xml = simplexml_load_string($response->getContent());

        foreach ($xml->children() as $child) {
            return $child;
        }

        return null;
    }

    public function updateSubscriptionDates(string $subscriptionId, GarCreatableSubscriptionInterface $subscription): bool {
        $xml = $this->getSubscriptionXml($subscriptionId);

        if (is_null($xml)) {
            return false;
        }

        // Remove UAI tag
        unset($xml->uaiEtab);

        // Set new tag values
        if ($subscription->getGarSubscriptionFrom() !== null) {
            $xml->debutValidite = $subscription->getGarSubscriptionFrom()->format(self::DATE_FORMAT);
        }

        if ($subscription->getGarSubscriptionTo() !== null) {
            $xml->finValidite = $subscription->getGarSubscriptionTo()->format(self::DATE_FORMAT);
        }

        // Hackish
        $namespacedXml = str_replace('<abonnement>', '<abonnement xmlns="http://www.atosworldline.com/wsabonnement/v1.0/">', $xml->asXML());

        $response = $this->client->request('POST', $this->getEndpoint("/$subscriptionId"), [
            'body' => $namespacedXml
        ]);

        return $response->getStatusCode() === 200;
    }

    public function deleteSubscription(string $subscriptionId): bool
    {
        $response = $this->client->request(
            "DELETE",
            $this->getEndpoint("/" . $subscriptionId)
        );

        return $response->getStatusCode() === 204;
    }

}