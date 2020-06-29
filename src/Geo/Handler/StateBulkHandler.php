<?php

declare(strict_types=1);

namespace Geo\Handler;

use Geo\Mapper\Mapper;
use Geo\ValueObject\StateId;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function array_unique;
use function count;
use function is_array;

class StateBulkHandler implements RequestHandlerInterface
{
    private Mapper $geoMapper;
    private ProblemDetailsResponseFactory $problemDetailsResponseFactory;

    public function __construct(Mapper $geoMapper, ProblemDetailsResponseFactory $problemDetailsResponseFactory)
    {
        $this->geoMapper                     = $geoMapper;
        $this->problemDetailsResponseFactory = $problemDetailsResponseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $ids  = $body['ids'] ?? null;
        if (! is_array($ids)) {
            return $this->problemDetailsResponseFactory->createResponse(
                $request,
                400,
                'Id\'s property is not an array'
            );
        }

        if (empty($ids)) {
            return new JsonResponse([]);
        }

        if (count($ids) > 500) {
            return $this->problemDetailsResponseFactory->createResponse(
                $request,
                400,
                'Id\'s size must not exceed 500.'
            );
        }

        $ids = array_unique(array_filter($body['ids'], static fn (string $id) => StateId::isValid($id)));

        return new JsonResponse(! empty($ids)
            ? $this->geoMapper->bulk($ids)
            : []);
    }
}
