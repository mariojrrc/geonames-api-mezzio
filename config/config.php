<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Symfony\Component\Dotenv\Dotenv;

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    (new Dotenv(true))->overload($envFile);
}

date_default_timezone_set(getenv('TIMEZONE'));

if (extension_loaded('newrelic')) {
    newrelic_set_appname('geonames_api');
}

$cacheConfig = ['config_cache_path' => 'data/cache/config-cache.php'];

// phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
$aggregator = new ConfigAggregator([
    \Laminas\Serializer\ConfigProvider::class,
    \LosMiddleware\ApiServer\ConfigProvider::class,
    \Laminas\I18n\ConfigProvider::class,
    \Laminas\Log\ConfigProvider::class,
    \Laminas\InputFilter\ConfigProvider::class,
    \Laminas\Filter\ConfigProvider::class,
    \Laminas\Validator\ConfigProvider::class,
    \Laminas\Paginator\ConfigProvider::class,
    \Laminas\Cache\ConfigProvider::class,
    \Mezzio\Hal\ConfigProvider::class,
    \Laminas\I18n\ConfigProvider::class,
    \Laminas\Hydrator\ConfigProvider::class,
    \Mezzio\ProblemDetails\ConfigProvider::class,
    \Laminas\HttpHandlerRunner\ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    // Include cache configuration
    new ArrayProvider($cacheConfig),
    \Mezzio\Helper\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Laminas\Diactoros\ConfigProvider::class,
    // Default App module config
    App\ConfigProvider::class,
    Geo\ConfigProvider::class,
    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider(realpath(__DIR__) . sprintf(
        '/autoload/{{,*.}global,{,*.}%s,{,*.}local}.php',
        getenv('APPLICATION_ENV') ?: 'production'
    )),
    // Load development config if it exists
    new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
