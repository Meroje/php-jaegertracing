<?php

require __DIR__.'/../vendor/autoload.php';

use Jaeger\Config;
use OpenTracing\GlobalTracer;
use OpenTracing\Formats;

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
            }
        }

        return $headers;
    }
}
$config = new Config(
    [
        'sampler' => [
            'type' => 'const',
            'param' => true,
        ],
        'local_agent' => [
            'reporting_host' => 'jaeger',
        ],
        'logging' => true,
    ],
    'date-app'
);
$config->initializeTracer();
/** @var \Jaeger\Tracer $tracer */
$tracer = GlobalTracer::get();
try {
    $spanContext = $tracer->extract(
        Formats\HTTP_HEADERS,
        getallheaders()
    );
    if ($spanContext) {
        $scope = $tracer->startActiveSpan('getDate', ['child_of' => $spanContext]);
        $currentDateTime = date('Y-m-d H:i:s');
        echo $currentDateTime;
        $scope->close();
    }
    $tracer->flush();
} catch (Exception $e) {
    var_dump($e);
}
