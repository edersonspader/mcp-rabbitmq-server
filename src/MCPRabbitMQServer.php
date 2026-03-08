<?php

namespace App;

class MCPRabbitMQServer
{
	private string $baseUrl;

	public function __construct(
		private Config $config,
	) {
		$this->baseUrl = sprintf('http://%s:%s', $config->host, $config->port);
	}

	protected function guard(string $method): void
	{
		if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
			throw new \InvalidArgumentException('Invalid HTTP method: ' . $method);
		}
	}

	public function exec(string $method, string $endpoint, string $payload = ''): array
	{
		$method = strtoupper(trim($method));
		$this->guard($method);

		$url = $this->baseUrl . $endpoint;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $this->config->user . ':' . $this->config->pass);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		if ($payload !== '') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		}

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error    = curl_error($ch);
		curl_close($ch);

		if ($error) {
			throw new \RuntimeException('cURL error: ' . $error);
		}

		if ($httpCode >= 400) {
			$detail = $response ?: '(no body)';
			throw new \RuntimeException(sprintf('RabbitMQ API error [HTTP %d]: %s', $httpCode, $detail));
		}

		if ($response === '' || $response === false) {
			return ['status' => 'ok', 'http_code' => $httpCode];
		}

		$decoded = json_decode($response, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return ['response' => $response, 'http_code' => $httpCode];
		}

		if (is_array($decoded)) {
			return $decoded;
		}

		return ['data' => $decoded, 'http_code' => $httpCode];
	}
}
