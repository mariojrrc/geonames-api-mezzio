GeoNames API Mezzio/MongoDB Example
=================================================
[![Build Status](https://semaphoreci.com/api/v1/mariojrrc/geonames-api-mezzio/branches/master/badge.svg)](https://semaphoreci.com/mariojrrc/geonames-api)

Este projeto contém uma API REST de exemplo escrita em PHP 7.4 utilizando [Mezzio](https://docs.mezzio.dev/) e [MongoDB](https://mongodb.com/).
Possui basicamente dois endpoints com CRUD:

- /v1/state
- /v1/city

Para acessar a api é necessário utilizar header de autenticação no formato `X-Api-Key: uuid`. Os tokens para  usuário administrador e de consulta são definidos via `.env` localizado na raiz do projeto.

A documentação dos endpoints pode ser encontrada na pasta `public/doc`. Ela é feita utilizando a notação do OpenAPI v3.

## Executando o projeto

**OBS: Necessário PHP 7.4 e MongoDB instalados**

1. Copie ou renomeie o arquivo `.env.dist` e preencha as informações necessárias
2. execute `composer install`
3. execute `composer serve`
4. Faça os testes via [Postman](https://www.getpostman.com/) ou similar no endereço `0.0.0.0:8080/v1/state`

** Uso com Docker **
1. `docker-compose up`
2. `docker exec -t geoname-mezzio-php bash -c "cd /var/www/html && composer install"`
Obs: caso tenha problema com composer install e mongodb-ext, execute `docker exec -t geoname-mezzio-php bash -c "pecl install mongodb"`

**BONUS**
Console disponível para criar estados/cidades disponível via `php bin/console populate:geodb`;
