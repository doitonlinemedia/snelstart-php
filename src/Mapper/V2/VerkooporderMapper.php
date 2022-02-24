<?php
/**
 * @author  IntoWebDevelopment <info@intowebdevelopment.nl>
 * @project SnelstartApiPHP
 */

namespace SnelstartPHP\Mapper\V2;

use Money\Currency;
use Money\Money;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use SnelstartPHP\Mapper\AbstractMapper;
use SnelstartPHP\Model\Adres;
use SnelstartPHP\Model\IncassoMachtiging;
use SnelstartPHP\Model\Kostenplaats;
use SnelstartPHP\Model\Land;
use SnelstartPHP\Model\Type\ProcesStatus;
use SnelstartPHP\Model\Type\VerkooporderBtwIngaveModel;
use SnelstartPHP\Model\V2\Artikel;
use SnelstartPHP\Model\V2\Relatie;
use SnelstartPHP\Model\V2\Verkoopfactuur;
use SnelstartPHP\Model\V2\Verkooporder;
use SnelstartPHP\Model\V2\VerkooporderRegel;
use SnelstartPHP\Model\V2\Verkoopordersjabloon;
use SnelstartPHP\Snelstart;

final class VerkooporderMapper extends AbstractMapper
{
    public function add(ResponseInterface $response): Verkooporder
    {
        $this->setResponseData($response);
        return $this->map(new Verkooporder());
    }

    public function delete(ResponseInterface $response): void
    {

    }

    public function map(Verkooporder $verkooporder, array $data = []): Verkooporder
    {
        $data = empty($data) ? $this->responseData : $data;
        $adresMapper = new AdresMapper();

        /**
         * @var Verkooporder $verkooporder
         */
        $verkooporder = $this->mapArrayDataToModel($verkooporder, $data);
        $verkooporder->setRelatie(Relatie::createFromUUID(Uuid::fromString($data["relatie"]["id"])))
            ->setProcesStatus(new ProcesStatus($data["procesStatus"]));

        if ($data["incassomachtiging"] !== null) {
            $verkooporder->setIncassomachtiging(IncassoMachtiging::createFromUUID(Uuid::fromString($data["incassomachtiging"]["id"])));
        }

        if ($data["afleveradres"] !== null) {
            $verkooporder->setAfleveradres($adresMapper->mapAdresToSnelstartObject($data["afleveradres"]));
        }

        if ($data["factuuradres"] !== null) {
            $verkooporder->setFactuuradres($adresMapper->mapAdresToSnelstartObject($data["factuuradres"]));
        }

        if ($data["kostenplaats"] !== null) {
            $verkooporder->setKostenplaats(Kostenplaats::createFromUUID(Uuid::fromString($data["kostenplaats"]["id"])));
        }

        $regels = array_map(function(array $data) {
            return (new VerkooporderRegel())
                ->setArtikel(Artikel::createFromUUID(Uuid::fromString($data["artikel"]["id"])))
                ->setOmschrijving($data["omschrijving"])
                ->setStuksprijs($this->getMoney($data["stuksprijs"]))
                ->setAantal($data["aantal"])
                ->setKortingsPercentage($data["kortingsPercentage"])
                ->setTotaal($this->getMoney($data["totaal"]))
                ;
        }, $data["regels"]);

        $verkooporder->setRegels(...$regels);

        if ($data["factuurkorting"] !== null) {
            $verkooporder->setFactuurkorting($this->getMoney($data["factuurkorting"]));
        }

        if ($data["totaalExclusiefBtw"] !== null) {
            $verkooporder->setTotaalExclusiefBtw($this->getMoney($data["totaalExclusiefBtw"]));
        }

        if ($data["totaalInclusiefBtw"] !== null) {
            $verkooporder->setTotaalInclusiefBtw($this->getMoney($data["totaalInclusiefBtw"]));
        }

        if ($data["verkoopfactuur"] !== null) {
            $verkooporder->setVerkoopfactuur(Verkoopfactuur::createFromUUID(Uuid::fromString($data["verkoopfactuur"])));
        }

        if ($data["verkoopordersjabloon"] !== null) {
            $verkooporder->setVerkoopordersjabloon(Verkoopordersjabloon::createFromUUID(Uuid::fromString($data["verkoopordersjabloon"]["id"])));
        }

        return $verkooporder;
    }

    public function findAll($response): \Generator
    {
        $this->setResponseData($response);
        foreach ($this->responseData as $data) {
            yield $this->mapResponseToVerkooporderModel(new Verkooporder(), $data);
        }
    }

    /**
     * Map the data from the response to the model.
     */
    public function mapResponseToVerkooporderModel(Verkooporder $verkooporder, array $data = []): Verkooporder
    {
        $data = empty($data) ? $this->responseData : $data;

        /**
         * @var Verkooporder $verkooporder
         */
        $verkooporder = $this->mapArrayDataToModel($verkooporder, $data);

        if (isset($data["relatie"])) {
            $verkooporder->setRelatie(Relatie::createFromUUID(Uuid::fromString($data["relatie"]["id"])));
        }

        if (isset($data["procesStatus"])) {
            $verkooporder->setProcesStatus(new ProcesStatus($data["procesStatus"]));
        }

        if (isset($data["datum"])) {
            $verkooporder->setDatum(new \DateTimeImmutable($data["datum"]));
        }

        if (isset($data["incassomachtiging"]["id"])) {
            $incassoMachtiging = IncassoMachtiging::createFromUUID(Uuid::fromString($data["incassomachtiging"]["id"]));
            $verkooporder->setIncassoMachtiging($incassoMachtiging);
        }

        if (isset($data["kostenplaats"]) && strlen($data["kostenplaats"]) > 0) {
            $verkooporder->setKostenplaats(
                Kostenplaats::createFromUUID(Uuid::fromString($data["kostenplaats"]["id"]))
            );
        }

        $regels = [];
        foreach ($data["regels"] ?? [] as $regel) {
            $regelObject = (new VerkooporderRegel())
                ->setOmschrijving($regel["omschrijving"])
                ->setAantal(($regel['aantal']));

            if ($regel["artikel"]) {
                $regelObject->setArtikel(Artikel::createFromUUID(Uuid::fromString($regel["artikel"]["id"])));
            }

            if (isset($regel["stuksprijs"])) {
                $prijs = (int)($regel["stuksprijs"] * 100);
                $regelObject->setStuksprijs(new Money($prijs, new Currency("EUR")));
            }

            if (isset($regel["kortingsPercentage"])) {
                $prijs = (int)($regel["kortingsPercentage"] * 100);
                $regelObject->setKortingsPercentage(new Money($prijs, new Currency("EUR")));
            }

            if (isset($regel["totaal"])) {
                $prijs = (int)($regel["totaal"] * 100);
                $regelObject->setTotaal(new Money($prijs, new Currency("EUR")));
            }


            $regels[] = $regelObject;
        }

        $verkooporder->setRegels($regels);


        if (!empty($data["afleveradres"])) {
            $verkooporder->setAfleveradres(
                $this->mapAddressToVerkooporderAddress($data["afleveradres"], Adres::class)
            );
        }

        if (!empty($data["factuuradres"])) {
            $verkooporder->setFactuuradres(
                $this->mapAddressToVerkooporderAddress($data["factuuradres"], Adres::class)
            );
        }

        if (isset($data["verkooporderBtwIngaveModel"])) {
            $verkooporder->setVerkooporderBtwIngaveModel(new VerkooporderBtwIngaveModel($data["verkooporderBtwIngaveModel"]));
        }

        if (isset($data["factuurkorting"])) {
            $verkooporder->setFactuurKorting(new Money($data["factuurkorting"] * 100, new Currency("EUR")));
        }

        if (isset($data["verkoopfactuur"])) {
            $verkooporder->setVerkoopfactuur(
                Verkoopfactuur::createFromUUID(Uuid::fromString($data["verkoopfactuur"]["id"]))
            );
        }
        return $verkooporder;
    }

    /**
     * Map the response data to the model. Should extend the RelatieAdres class.
     *
     * @param array  $address
     * @param string $addressClass
     * @return Adres
     */
    public function mapAddressToVerkooporderAddress(array $address, string $addressClass): Adres
    {
        /**
         * @var Adres $class
         */
        $class = new $addressClass;

        if (!$class instanceof Adres) {
            throw new \InvalidArgumentException(sprintf("Only classes that extend '%s' are allowed here.", Adres::class));
        }

        $land = Land::createFromUUID(Uuid::fromString($address["land"]["id"]));

        return $class
            ->setContactpersoon($address["contactpersoon"])
            ->setStraat($address["straat"])
            ->setPostcode($address["postcode"])
            ->setPlaats($address["plaats"])
            ->setLand($land);
    }
}
