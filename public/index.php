<?php

require __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Jaeger\Config;
use Jaeger\Tracer;
use OpenTracing\GlobalTracer;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use const Jaeger\SAMPLED_FLAG;
use const Jaeger\SAMPLER_TYPE_CONST;
use const OpenTracing\Formats\HTTP_HEADERS;

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
            }
        }

        return $headers;
    }
}
$config = new Config(
    [
        'sampler' => [
            'type' => SAMPLER_TYPE_CONST,
            'param' => true,
        ],
        'local_agent' => [
            'reporting_host' => 'jaeger',
        ],
        'logging' => true,
    ],
    'date-app',
    new Logger('php', [new StreamHandler('php://stdout')])
);
$config->initializeTracer();

register_shutdown_function(static function() {
    /* Flush the tracer to the backend */
    $tracer = GlobalTracer::get();
    $tracer->flush();
});

try {
    /** @var Tracer $tracer */
    $tracer = GlobalTracer::get();

    $spanContext = $tracer->extract(
        HTTP_HEADERS,
        getallheaders()
    );
    if ($spanContext) {
        $requestScope = $tracer->startActiveSpan('request', ['child_of' => $spanContext]);

        if (array_key_exists('getDate', $_GET)) {
            $span = $tracer->startActiveSpan('getDate');
            $currentDateTime = date('Y-m-d H:i:s');
            echo $currentDateTime;
            $span->close();
        } else {
            $span = $tracer->startActiveSpan('getDateRequest');
            $client = new Client;
            $headers = [];
            $tracer->inject( $span->getSpan()->getContext(), HTTP_HEADERS, $headers);
            $request = new Request('GET', 'http://nginx/?getDate', $headers);
            $response = $client->send($request);
            echo $response->getBody();
            $span->close();
        }

        $requestScope->close();
    }
} catch (Exception $e) {
    var_dump($e);
}
