<?php

namespace App;

class Config
{
	public function __construct(
		public string $host = '127.0.0.1',
		public string $port = '15672',
		public string $user = 'mcp',
		public string $pass = 'mcp',
		public string $vhost = '/',
	) {
		$this->host  = getenv('RABBITMQ_HOST')  ?: $host;
		$this->port  = getenv('RABBITMQ_PORT')  ?: $port;
		$this->user  = getenv('RABBITMQ_USER')  ?: $user;
		$this->pass  = getenv('RABBITMQ_PASS')  ?: $pass;
		$this->vhost = getenv('RABBITMQ_VHOST') ?: $vhost;
	}
}
