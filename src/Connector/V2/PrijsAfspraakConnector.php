<?php
/**
 * @author  IntoWebDevelopment <info@intowebdevelopment.nl>
 * @project SnelstartApiPHP
 */

namespace SnelstartPHP\Connector\V2;

use Ramsey\Uuid\UuidInterface;
use SnelstartPHP\Exception\SnelstartResourceNotFoundException;
use SnelstartPHP\Model\V2\Artikel;
use SnelstartPHP\Model\V2\Prijsafspraak;
use SnelstartPHP\Model\V2\Relatie;
use SnelstartPHP\Request\ArtikelRequest;
use SnelstartPHP\Request\V2\PrijsAfspraakRequest;
use function json_decode;

class PrijsAfspraakConnector extends BaseConnector
{
    public function getPrijsAfspraakByRelatieAndArtikel(Artikel $article, Relatie $relatie, $aantal = 1)
    {
        {
            try {
                return json_decode($this->connection->doRequest(PrijsAfspraakRequest::getByArticleAndCustomer($article,
                    $relatie, $aantal))->getBody()->getContents());
            } catch (SnelstartResourceNotFoundException $e) {
                return null;
            }
        }
    }

    public function getExplicitPrijsAfspraakByRelatieAndArtikel(Artikel $article, Relatie $relatie, $aantal = 1)
    {
        {
            try {
                return json_decode($this->connection->doRequest(PrijsAfspraakRequest::getExplicitByArticleAndCustomer($article,
                    $relatie, $aantal))->getBody()->getContents());
            } catch (SnelstartResourceNotFoundException $e) {
                return null;
            }
        }
    }
}
