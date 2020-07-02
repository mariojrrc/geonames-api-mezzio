<?php

declare(strict_types=1);

namespace Geo\Handler;

use Geo\Entity\CollectionInterface;
use Geo\Entity\EntityInterface;
use Geo\Mapper\MapperInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use LosMiddleware\ApiServer\Exception\MethodNotAllowedException;
use LosMiddleware\ApiServer\Exception\ValidationException;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function array_key_exists;
use function array_keys;
use function is_array;
use function strtoupper;

abstract class MongoRestHandler implements RequestHandlerInterface
{
    public const IDENTIFIER_NAME = 'id';

    protected ServerRequestInterface $request;
    protected int $itemCountPerPage = 25;
    protected MapperInterface $mapper;
    protected ResourceGenerator $resourceGenerator;
    protected HalResponseFactory $responseFactory;
    protected ProblemDetailsResponseFactory $problemDetailsFactory;

    public function __construct(
        MapperInterface $mapper,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory,
        ProblemDetailsResponseFactory $problemDetailsFactory
    ) {
        $this->mapper                = $mapper;
        $this->resourceGenerator     = $resourceGenerator;
        $this->responseFactory       = $responseFactory;
        $this->problemDetailsFactory = $problemDetailsFactory;
    }

    /**
     * @return ResponseInterface|JsonResponse
     *
     * phpcs:disable
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $requestMethod = strtoupper($request->getMethod());
        $id = $request->getAttribute(static::IDENTIFIER_NAME);
        $this->request = $request;

        try {
            switch ($requestMethod) {
                case 'GET':
                    return isset($id)
                        ? $this->handleFetch($id)
                        : $this->handleFetchAll();
                case 'POST':
                    if (isset($id)) {
                        return $this->problemDetailsFactory->createResponse(
                            $request,
                            405,
                            'Invalid entity operation POST'
                        );
                    }
                    return $this->handlePost();
                case 'PUT':
                    return isset($id)
                        ? $this->handleUpdate($id)
                        : $this->handleUpdateList();
                case 'PATCH':
                    return isset($id)
                        ? $this->handlePatch($id)
                        : $this->handlePatchList();
                case 'DELETE':
                    return isset($id)
                        ? $this->handleDelete($id)
                        : $this->handleDeleteList();
                case 'OPTIONS':
                    return $this->handleOptions();
                default:
                    throw MethodNotAllowedException::create();
            }
        } catch (MethodNotAllowedException $ex) {
            return $this->generateErrorResponse('Method not allowed', 405);
        } catch (ValidationException $ex) {
            return $this->generateErrorResponse('Unprocessable Entity', 422, $ex->getAdditionalData());
        } catch (Throwable $ex) {
            throw $ex;
        }
    }
    // phpcs:enable

    /**
     * Call the InputFilter to filter and validate data
     *
     * @return array
     */
    protected function validateBody(bool $validateAll = true): array
    {
        $data = $this->request->getParsedBody();

        if (! is_array($data)) {
            $data = [];
        }

        $inputFilter = $this->mapper->createEntityInputFilter();
        if ($validateAll === false) {
            $inputFilter->setValidationGroup(array_keys($data));
        }

        if (! $inputFilter->setData($data)->isValid()) {
            throw ValidationException::fromMessages($inputFilter->getMessages());
        }

        $values = $inputFilter->getValues();
        $parsed = [];

        foreach ($values as $key => $value) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            $parsed[$key] = $value;
        }

        return $parsed;
    }

    /**
     * Generates a proper response based on the Entity ot Collection
     *
     * @param EntityInterface|CollectionInterface $entity
     */
    protected function generateResponse($entity, int $statusCode = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(
            $this->request,
            $this->resourceGenerator->fromObject($entity, $this->request)
        );

        return $response->withStatus($statusCode);
    }

    /**
     * @param array $arrayMessage
     */
    protected function generateErrorResponse(
        string $message,
        int $statusCode = 500,
        array $arrayMessage = []
    ): ResponseInterface {
        return $this->problemDetailsFactory->createResponse(
            $this->request,
            $statusCode,
            $message,
            '',
            '',
            $arrayMessage
        );
    }

    /**
     * @return ResponseInterface|JsonResponse
     */
    protected function handleFetch(string $id): ResponseInterface
    {
        $entity = $this->fetch($id);
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }

        return $this->generateResponse($entity);
    }

    protected function handleFetchAll(): ResponseInterface
    {
        return $this->generateResponse($this->fetchAll());
    }

    /**
     * @throws ValidationException
     */
    protected function handlePost(): ResponseInterface
    {
        return $this->generateResponse($this->create($this->validateBody()));
    }

    /**
     * @throws ValidationException
     */
    protected function handleUpdate(string $id): ResponseInterface
    {
        $entity = $this->update($id, $this->validateBody(false));
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }

        return $this->generateResponse($entity);
    }

    /**
     * @throws MethodNotAllowedException
     * @throws ValidationException
     */
    protected function handleUpdateList(): ResponseInterface
    {
        return $this->generateResponse($this->updateList($this->validateBody(false)));
    }

    /**
     * @throws ValidationException
     */
    protected function handlePatch(string $id): ResponseInterface
    {
        $entity = $this->patch($id, $this->validateBody(false));
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }

        return $this->generateResponse($entity);
    }

    /**
     * @throws MethodNotAllowedException
     * @throws ValidationException
     */
    protected function handlePatchList(): ResponseInterface
    {
        return $this->generateResponse($this->patchList($this->validateBody(false)));
    }

    protected function handleDelete(string $id): ResponseInterface
    {
        $entity = $this->delete($id);
        if ($entity === null) {
            return $this->generateErrorResponse('Entity not found', 404);
        }

        return new EmptyResponse(204);
    }

    protected function handleDeleteList(): ResponseInterface
    {
        $this->deleteList();

        return new JsonResponse(null, 204);
    }

    public function fetch(string $id): ?EntityInterface
    {
        return $this->mapper->fetchById($id);
    }

    public function fetchAll(array $query = [], array $options = []): CollectionInterface
    {
        $withDeleted = $this->request->getQueryParams()['deleted'] ?? '0';
        if ($withDeleted !== 'true' && $withDeleted !== '1') {
            $query['deleted'] = ['$ne' => true];
        }

        return $this->mapper->fetchAllBy($query, (bool) $withDeleted, $options);
    }

    public function create(array $data): EntityInterface
    {
        $entity = $this->mapper->createEntity($data);
        $this->mapper->insert($entity);

        return $entity;
    }

    public function update(string $id, array $data): ?EntityInterface
    {
        $entity = $this->mapper->fetchById($id);
        if ($entity === null) {
            return null;
        }

        return $this->mapper->update($entity, $data);
    }

    public function updateList(array $data): CollectionInterface
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    public function delete(string $id): ?EntityInterface
    {
        $entity = $this->mapper->fetchById($id);
        if ($entity === null) {
            return null;
        }

        return $this->mapper->delete($entity);
    }

    /**
     * @throws MethodNotAllowedException
     */
    public function deleteList(): void
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    public function patch(string $id, array $data): ?EntityInterface
    {
        return $this->update($id, $data);
    }

    public function patchList(array $data): CollectionInterface
    {
        throw new MethodNotAllowedException('Method not allowed', 405);
    }

    private function handleOptions(): ResponseInterface
    {
        return new EmptyResponse();
    }
}
