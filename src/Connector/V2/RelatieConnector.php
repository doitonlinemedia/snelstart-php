<?php
/**
 * @author  IntoWebDevelopment <info@intowebdevelopment.nl>
 * @project SnelstartApiPHP
 */

namespace SnelstartPHP\Connector\V2;

use Ramsey\Uuid\UuidInterface;
use SnelstartPHP\Connector\BaseConnector;
use SnelstartPHP\Exception\PreValidationException;
use SnelstartPHP\Exception\SnelstartResourceNotFoundException;
use SnelstartPHP\Mapper\V2 as Mapper;
use SnelstartPHP\Model\V2 as Model;
use SnelstartPHP\Model\Type\Relatiesoort;
use SnelstartPHP\Request\ODataRequestData;
use SnelstartPHP\Request\V2 as Request;

final class RelatieConnector extends BaseConnector
{
    public function find(UuidInterface $id): ?Model\Relatie
    {
        try {
            return Mapper\RelatieMapper::find($this->connection->doRequest(Request\RelatieRequest::find($id)));
        } catch (SnelstartResourceNotFoundException $e) {
            return null;
        }
    }

    /**
     * @return Model\Relatie|iterable
     */
    public function findAll(?ODataRequestData $ODataRequestData = null, bool $fetchAll = false, ?iterable $previousResults = null): iterable
    {
        $ODataRequestData = $ODataRequestData ?? new ODataRequestData();
        $relaties = Mapper\RelatieMapper::findAll($this->connection->doRequest(Request\RelatieRequest::findAll($ODataRequestData)));
        $iterator = $previousResults ?? new \AppendIterator();

        if ($relaties->valid()) {
            $iterator->append($relaties);
        }

        if ($fetchAll && $relaties->valid()) {
            if ($previousResults === null) {
                $ODataRequestData->setSkip($ODataRequestData->getTop());
            } else {
                $ODataRequestData->setSkip($ODataRequestData->getSkip() + $ODataRequestData->getTop());
            }

            return $this->findAll($ODataRequestData, true, $iterator);
        }

        return $iterator;
    }

    /**
     * @return Model\Relatie[]|iterable
     */
    public function findAllLeveranciers(?ODataRequestData $ODataRequestData = null, bool $fetchAll = false, ?iterable $previousResults = null): iterable
    {
        $ODataRequestData = $ODataRequestData ?? new ODataRequestData();
        $ODataRequestData->setFilter(\array_merge(
            $ODataRequestData->getFilter(),
            [ sprintf("Relatiesoort/any(soort:soort eq '%s')", Relatiesoort::LEVERANCIER()) ])
        );

        return $this->findAll($ODataRequestData, $fetchAll, $previousResults);
    }

    /**
     * @return Model\Relatie[]|iterable
     */
    public function findAllKlanten(?ODataRequestData $ODataRequestData = null, bool $fetchAll = false, ?iterable $previousResults = null): iterable
    {
        $ODataRequestData = $ODataRequestData ?? new ODataRequestData();
        $ODataRequestData->setFilter(\array_merge(
            $ODataRequestData->getFilter(),
            [ sprintf("Relatiesoort/any(soort:soort eq '%s'))", Relatiesoort::KLANT()) ])
        );

        return $this->findAll($ODataRequestData, $fetchAll, $previousResults);
    }

    public function add(Model\Relatie $relatie): Model\Relatie
    {
        if ($relatie->getId() !== null) {
            throw new PreValidationException("The ID of this relation should be null.");
        }

        return Mapper\RelatieMapper::add($this->connection->doRequest(Request\RelatieRequest::add($relatie)));
    }

    public function update(Model\Relatie $relatie): Model\Relatie
    {
        if ($relatie->getId() === null) {
            throw new PreValidationException("All relations should have an ID.");
        }

        return Mapper\RelatieMapper::update($this->connection->doRequest(Request\RelatieRequest::update($relatie)));
    }
}