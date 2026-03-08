# MCP RabbitMQ Server

> 🇧🇷 [Leia em Português](README.pt-BR.md)

MCP (Model Context Protocol) server for interacting with RabbitMQ via the Management HTTP API.

The Model Context Protocol (MCP) enables AI applications to connect to external servers to access tools and resources. This server exposes a single tool to perform any operation on the RabbitMQ Management API.

## Features

- Execution of any operation supported by the RabbitMQ Management HTTP API.
- Support for GET, POST, PUT, and DELETE.
- Authentication via configurable username/password.
- Logging of all operations in `var/logs/mcp.log`.

## Prerequisites

- RabbitMQ with the `rabbitmq_management` plugin enabled (default port `15672`).
- PHP 8.5+ with `curl` extension.

## Installation

```bash
composer install
```

## Configuration

Copy the `.env.example` file to `.env` and adjust the variables as needed:

```bash
cp .env.example .env
```

Environment variables:
- `RABBITMQ_HOST` (default: 127.0.0.1) — RabbitMQ Host
- `RABBITMQ_PORT` (default: 15672) — Management API Port
- `RABBITMQ_USER` (default: mcp) — Management API User
- `RABBITMQ_PASS` (default: mcp) — Management API Password
- `RABBITMQ_VHOST` (default: /) — Default virtual host (used as reference in calls)

## Execution

```bash
composer serve
# or
php server.php
```

The server runs via STDIO, waiting for MCP connections.

## Available Tool

### `exec`

Executes a command on the RabbitMQ Management HTTP API.

**Parameters:**
- `method` (string, required) — HTTP Method: `GET`, `POST`, `PUT`, or `DELETE`.
- `endpoint` (string, required) — API path, e.g., `/api/queues/%2F`.
- `payload` (string, optional) — JSON body for POST/PUT requests.

**Return:**
- The parsed JSON response from the RabbitMQ API, or `{ "status": "ok", "http_code": N }` for responses without a body.

## Usage Examples

| Operation | method | endpoint | payload |
|---|---|---|---|
| Broker overview | `GET` | `/api/overview` | — |
| List queues (vhost /) | `GET` | `/api/queues/%2F` | — |
| Queue details | `GET` | `/api/queues/%2F/my-queue` | — |
| Create/declare queue | `PUT` | `/api/queues/%2F/my-queue` | `{"durable":true}` |
| Purge queue | `DELETE` | `/api/queues/%2F/my-queue/contents` | — |
| Delete queue | `DELETE` | `/api/queues/%2F/my-queue` | — |
| List exchanges | `GET` | `/api/exchanges/%2F` | — |
| Publish message | `POST` | `/api/exchanges/%2F/amq.default/publish` | `{"properties":{},"routing_key":"my-queue","payload":"hello","payload_encoding":"string"}` |
| Consume messages | `POST` | `/api/queues/%2F/my-queue/get` | `{"count":5,"ackmode":"ack_requeue_true","encoding":"auto"}` |
| List connections | `GET` | `/api/connections` | — |
| List consumers | `GET` | `/api/consumers` | — |

> The `/` vhost must be encoded as `%2F` in the URL.

## VS Code Integration

Configure the server in the Copilot/MCP settings file pointing to `php server.php` in the project directory.
