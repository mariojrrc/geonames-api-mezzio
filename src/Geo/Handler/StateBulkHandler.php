<?php

declare(strict_types=1);

namespace Geo\Handler;

use Geo\Mapper\Mapper;
use Geo\ValueObject\StateId;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;

class StateBulkHandler implements RequestHandlerInterface
{
    private Mapper $geoMapper;

    public function __construct(Mapper $geoMapper)
    {
        $this->geoMapper = $geoMapper;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (empty($body['ids'] ?? [])) {
            return new JsonResponse([]);
        }

        $ids = array_filter($body['ids'], static function (string $id) {
            return StateId::isValid($id);
        });

        return new JsonResponse(! empty($ids)
            ? $this->geoMapper->bulk($ids)
            : []);
    }
}
