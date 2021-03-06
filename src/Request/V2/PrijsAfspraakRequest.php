<?php
/**
 * @author  OptiWise Technologies B.V. <info@optiwise.nl>
 * @project SnelstartApiPHP
 */

namespace SnelstartPHP\Request\V2;

use SnelstartPHP\Model\V2\Artikel;
use function \http_build_query;
use function \array_filter;
use GuzzleHttp\Psr7\Request;
use SnelstartPHP\Model\V2\Relatie;
use SnelstartPHP\Request\BaseRequest;
use SnelstartPHP\Request\ODataRequestDataInterface;
use function sprintf;

final class PrijsAfspraakRequest extends BaseRequest
{
    public static function getExplicitByArticleAndCustomer(Artikel $artikel, Relatie $relatie, $aantal = 1)
    {
        return new Request('GET',
            sprintf('prijsafspraken/explicit-parameters?artikelPublicIdentifier=%s&aantal=%d&relatiePublicIdentifier=%s',
                $artikel->getId()->toString(), $aantal, $relatie->getId()->toString()));
    }
    public static function getByArticleAndCustomer(Artikel $artikel, Relatie $relatie, $aantal = 1)
    {
        return new Request('GET',
            sprintf('prijsafspraken?$filter=Artikel/Id eq guid\'%s\' and Aantal eq %d and Relatie/Id eq guid\'%s\'',
                $artikel->getId()->toString(), $aantal, $relatie->getId()->toString()));
    }
}
