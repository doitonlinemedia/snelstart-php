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
    public static function getByArticleAndCustomer(Artikel $artikel, Relatie $relatie, $aantal = 1)
    {
        return new Request('GET',
            sprintf('prijsafspraken/explicit-parameters?artikelPublicIdentifier=%s&aantal=%f&relatiePublicIdentifier=%f',
                $relatie->getId()->toString(), $aantal, $artikel->getId()->toString()));
    }


}
