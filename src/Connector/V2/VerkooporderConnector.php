<?php
/**
 * @author  OptiWise Technologies B.V. <info@optiwise.nl>
 * @project SnelstartApiPHP
 */

namespace SnelstartPHP\Connector\V2;

use Ramsey\Uuid\UuidInterface;
use SnelstartPHP\Connector\BaseConnector;
use SnelstartPHP\Exception\PreValidationException;
use SnelstartPHP\Exception\SnelstartResourceNotFoundException;
use SnelstartPHP\Mapper\V2\VerkooporderMapper;
use SnelstartPHP\Model\V2\Verkooporder;
use SnelstartPHP\Request\ODataRequestData;
use SnelstartPHP\Request\V2\VerkooporderRequest;

final class VerkooporderConnector extends BaseConnector
{
    public function find(UuidInterface $id): ?Verkooporder
    {
        try {
            $mapper = new VerkooporderMapper();
            $request = new VerkooporderRequest();

            return $mapper->find($this->connection->doRequest($request->find($id)));
        } catch (SnelstartResourceNotFoundException $e) {
            return null;
        }
    }

    public function add(Verkooporder $verkooporder): Verkooporder
    {
        if ($verkooporder->getId() !== null) {
            throw PreValidationException::unexpectedIdException();
        }

        $verkooporderMapper = new VerkooporderMapper();
        $verkooporderRequst = new VerkooporderRequest();

        return $verkooporderMapper->add($this->connection->doRequest($verkooporderRequst->add($verkooporder)));
    }

    public function updateVerkoopOrder(Verkooporder $verkooporder): Verkooporder
    {
        if ($verkooporder->getId() === null) {
            throw new PreValidationException("Verkooporder should have an ID.");
        }

        $verkooporderMapper = new VerkooporderMapper();
        $verkooporderRequst = new VerkooporderRequest();

        return $verkooporderMapper->updateVerkoopOrder($this->connection->doRequest($verkooporderRequst->updateVerkoopOrder($verkooporder)));
    }

    public function delete(Verkooporder $verkooporder): void
    {
        if ($verkooporder->getId() !== null) {
            throw PreValidationException::shouldHaveAnIdException();
        }

        $verkooporderMapper = new VerkooporderMapper();
        $verkooporderRequst = new VerkooporderRequest();

        $verkooporderMapper->delete($this->connection->doRequest($verkooporderRequst->delete($verkooporder)));
    }

    public function findAll(?ODataRequestData $ODataRequestData = null): iterable
    {
        return  (new VerkooporderMapper())->findAll($this->connection->doRequest( (new VerkooporderRequest())->findAll($ODataRequestData)));
    }
}