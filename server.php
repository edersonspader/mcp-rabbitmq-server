<?php

require __DIR__ . "/vendor/autoload.php";

use App\Config;
use App\MCPRabbitMQServer;
use Mcp\Server;
use Mcp\Server\Transport\StdioTransport;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

$config = new Config();

$logger = new Logger('mcp');
$logger->pushHandler(new StreamHandler(__DIR__ . '/var/logs/mcp.log', Level::Debug));

$serverInstance = new MCPRabbitMQServer($config);

$server = Server::builder()
	->setServerInfo('rabbitmq-mcp', '1.0')
	->setLogger($logger)
	->addTool(
		fn(string $method, string $endpoint, string $payload = '') => $serverInstance->exec($method, $endpoint, $payload),
		'exec',
		'Execute a command on RabbitMQ via the Management HTTP API',
		null,
		[
			'type' => 'object',
			'properties' => [
				'method' => [
					'type' => 'string',
					'description' => 'HTTP method to use: GET, POST, PUT or DELETE',
					'enum' => ['GET', 'POST', 'PUT', 'DELETE']
				],
				'endpoint' => [
					'type' => 'string',
					'description' => 'Management API path, e.g. /api/overview, /api/queues/%2F, /api/exchanges/%2F/amq.direct/publish'
				],
				'payload' => [
					'type' => 'string',
					'description' => 'Optional JSON body for POST/PUT requests'
				]
			],
			'required' => ['method', 'endpoint']
		]
	)
	->build();

$server->run(new StdioTransport());
