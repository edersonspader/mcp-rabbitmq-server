# MCP RabbitMQ Server

> 🇺🇸 [Read in English](README.md)

Servidor MCP (Model Context Protocol) para interação com RabbitMQ via Management HTTP API.

O Model Context Protocol (MCP) permite que aplicações de IA se conectem a servidores externos para acessar ferramentas e recursos. Este servidor expõe uma única ferramenta para executar qualquer operação na Management API do RabbitMQ.

## Funcionalidades

- Execução de qualquer operação suportada pela Management HTTP API do RabbitMQ.
- Suporte a GET, POST, PUT e DELETE.
- Autenticação via usuário/senha configuráveis.
- Logging de todas as operações em `var/logs/mcp.log`.

## Pré-requisitos

- RabbitMQ com o plugin `rabbitmq_management` habilitado (porta padrão `15672`).
- PHP 8.5+ com extensão `curl`.

## Instalação

```bash
composer install
```

## Configuração

Copie o arquivo `.env.example` para `.env` e ajuste as variáveis conforme necessário:

```bash
cp .env.example .env
```

Variáveis de ambiente:
- `RABBITMQ_HOST` (padrão: 127.0.0.1) — Host do RabbitMQ
- `RABBITMQ_PORT` (padrão: 15672) — Porta da Management API
- `RABBITMQ_USER` (padrão: mcp) — Usuário da Management API
- `RABBITMQ_PASS` (padrão: mcp) — Senha da Management API
- `RABBITMQ_VHOST` (padrão: /) — Virtual host padrão (usado como referência nas chamadas)

## Execução

```bash
composer serve
# ou
php server.php
```

O servidor é executado via STDIO, aguardando conexões MCP.

## Ferramenta disponível

### `exec`

Executa um comando na Management HTTP API do RabbitMQ.

**Parâmetros:**
- `method` (string, obrigatório) — Método HTTP: `GET`, `POST`, `PUT` ou `DELETE`.
- `endpoint` (string, obrigatório) — Caminho da API, ex: `/api/queues/%2F`.
- `payload` (string, opcional) — Corpo JSON para requisições POST/PUT.

**Retorno:**
- A resposta JSON parseada da API do RabbitMQ, ou `{ "status": "ok", "http_code": N }` para respostas sem corpo.

## Exemplos de uso

| Operação | method | endpoint | payload |
|---|---|---|---|
| Visão geral do broker | `GET` | `/api/overview` | — |
| Listar filas (vhost /) | `GET` | `/api/queues/%2F` | — |
| Detalhes de uma fila | `GET` | `/api/queues/%2F/minha-fila` | — |
| Criar/declarar fila | `PUT` | `/api/queues/%2F/minha-fila` | `{"durable":true}` |
| Purgar fila | `DELETE` | `/api/queues/%2F/minha-fila/contents` | — |
| Excluir fila | `DELETE` | `/api/queues/%2F/minha-fila` | — |
| Listar exchanges | `GET` | `/api/exchanges/%2F` | — |
| Publicar mensagem | `POST` | `/api/exchanges/%2F/amq.default/publish` | `{"properties":{},"routing_key":"minha-fila","payload":"olá","payload_encoding":"string"}` |
| Consumir mensagens | `POST` | `/api/queues/%2F/minha-fila/get` | `{"count":5,"ackmode":"ack_requeue_true","encoding":"auto"}` |
| Listar conexões | `GET` | `/api/connections` | — |
| Listar consumers | `GET` | `/api/consumers` | — |

> O vhost `/` deve ser codificado como `%2F` na URL.

## Integração com VS Code

Configure o servidor no arquivo de settings do Copilot/MCP apontando para `php server.php` no diretório do projeto.
