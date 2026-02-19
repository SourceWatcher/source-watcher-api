# Source Watcher API

REST API for the [Source Watcher](https://github.com/TheCocoTeam/source-watcher) project. It provides authentication (JWT + refresh tokens), database migrations, and HTTP endpoints used by [Source Watcher Board](../source-watcher-board) and other clients. The API uses [Source Watcher Core](../source-watcher-core) for ETL pipelines when that functionality is wired in.

## Requirements

- PHP 7.4 or later
- [Composer](https://getcomposer.org/)
- MySQL (or compatible) database for the API’s own data (users, refresh tokens, items, db connections, etc.)
- **Core dependency:** The API’s Composer autoload expects the Core library at `core/src/` (namespace `Coco\SourceWatcher\`). Copy or symlink [source-watcher-core](../source-watcher-core) into this project as `core/`, or use a Composer path repository so that `core/` is present when the API runs.

## Installation

1. Ensure Core is available at `core/` (see above).
2. Copy `.env.example` to `.env` and set your database and driver values.
3. Install dependencies:

```bash
composer install
```

## Environment

Create a `.env` file in this directory (see `.env.example`). Required variables include:

| Variable     | Description                | Example     |
|-------------|----------------------------|-------------|
| `DB_ADAPTER`| Database adapter for Phinx | `mysql`     |
| `DB_HOST`   | Database host              | `localhost` |
| `DB_NAME`   | Database name              | `source_watcher` |
| `DB_USER`   | Database user              | `root`      |
| `DB_PASS`   | Database password          | (your pass) |
| `DB_PORT`   | Database port              | `3306`      |
| `DB_CHARSET`| Character set              | `utf8`      |
| `DB_DRIVER` | PDO driver                 | `pdo_mysql` |

**Note:** `DB_NAME` is required by `index.php` and the DAOs but is not in `.env.example`; add it to your `.env`.

On each request, the API runs Phinx migrations for the configured database, then handles the request.

## Running the API

Run with PHP’s built-in server (for development):

```bash
php -S localhost:8181 -t .
```

Then open:

- **Base URL:** http://localhost:8181/
- **API entry:** http://localhost:8181/index.php (or configure your web server so that the document root points here and routes through `index.php`)

For production, point a web server (e.g. Apache with `mod_rewrite` or nginx) at this directory and ensure requests are forwarded to `index.php`.

## Main endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET    | `/api/v1/.well-known/jwks.json` | No  | Public keys for JWT verification |
| POST   | `/api/v1/credentials`           | No  | Login (username/password), returns access + refresh tokens |
| POST   | `/api/v1/jwt`                   | No  | Validate access token (header: `x-access-token`) |
| POST   | `/api/v1/refresh-token`         | No  | Exchange access + refresh token for new pair |
| GET    | `/api/v1/db-connection-type`    | Yes | List database connection types |
| *      | `/api/v1/database-seeding`     | Yes | Run database seeding |
| *      | `/api/v1/item`, `/api/v1/item/{id}` | Yes | Item CRUD — **controller not yet implemented** |

Protected routes require `access_token` (and `refresh_token` when refreshing) in the request (query or body as configured by the client).

## Project layout

- `index.php` — Front controller: loads `.env`, runs migrations, routes to controllers.
- `src/` — API code: `Framework/`, `Security/`, `Database/`, Phinx migrations under `src/phinx/`.
- `core/` — Must contain [Source Watcher Core](../source-watcher-core) (see Requirements).

## Current limitations

- **ItemController** is referenced in `index.php` but the class file `src/Core/Item/ItemController.php` does not exist. Requests to `/api/v1/item` or `/api/v1/item/{id}` will fail until that controller is implemented.
