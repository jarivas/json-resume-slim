# CLAUDE.md

## Purpose
This repository implements a JSON Resume REST API using Slim 4 and MariaDB.

This file is a fast operational context for assistants (AI or human) who need to implement or modify features.

## Current status (important)
- **Auth is working** (`login`, `logout`, `refresh-token`) with tokens persisted in `tokens`.
- Most resource endpoints (`basic`, `award`, `education`, etc.) are defined in contract/schema files, but not fully wired in routes/controllers yet.
- `src/routes.php` currently registers auth routes only.

## Confirmed project decisions
- Canonical route style is **without** `/api` prefix (for now).
- Login payload is **`username` + `password`**.
- This guide should stay in **English**.

## Source of truth (priority order)
1. `db.schema.sql` → actual persistence model (tables, columns, FK, types, JSON checks).
2. `jsonresume.schema.json` → JSON Resume payload shape and semantic validation.
3. `endpoints.yaml` → expected HTTP contract (paths, methods, request fields).
4. Existing code in `src/` → current implementation patterns (controller/service/model/middleware).

If contract and code diverge, prefer schema + agreed team decisions, and document the mismatch in your PR/commit.

## Project architecture
- Framework: Slim 4.
- Namespace: `App\\` mapped to `src/` (PSR-4).
- Entry point: `public/index.php`.
- Bootstrap: `App\Helper\App`.
- Routes: `src/routes.php`.
- Main pattern:
  - `Controller` receives request/response and delegates.
  - `Service` parses request body (`setData`) and executes logic.
  - `Model` handles DB operations (custom lightweight ORM-style layer).

## Implemented authentication
### Flow
- `POST /auth/login`
  - Validates `username` and `password` against `.env` (`USERNAME`, `PASSWORD`).
  - Creates random token + expiration (`+1 hour`) in `tokens` table.
- `POST /auth/logout` (protected by middleware)
  - Reads token from `Authorization: Bearer <token>`.
  - Deletes token from DB.
- `POST /auth/refresh-token` (protected by middleware)
  - Deletes current token and creates a new one.

### Middleware
- `App\Middleware\Route\Authentication`:
  - Rejects when bearer token is missing or expired.
  - Returns `401` JSON: `{ "error": "Unauthorized" }`.

## JSON Resume data model
- Root aggregate: `basics`.
- Child resources (`awards`, `educations`, `projects`, `works`, etc.) reference `basics.id` via `basic_id`.
- IDs: `char(26)` ULID-like. For new records use `Ulid::generate()->__toString()`.
- Structured fields are stored as JSON text with `CHECK json_valid(...)`:
  - Example columns: `basics.location`, `basics.profiles`, `skills.keywords`, `projects.highlights`.

## Implementation conventions
- Keep DB column naming conventions (snake_case where applicable).
- Preserve the `Controller -> Service -> Model` flow.
- Use `Controller::respond()` for JSON responses.
- Keep request body parsing in `Service::setData()` (parsed body or JSON body).
- Centralize route registration in `src/routes.php`.

## How to implement pending endpoints
1. Define/confirm route in `src/routes.php`.
2. Add controller per endpoint/resource in `src/Controller/...`.
3. Add service in `src/Service/...` with:
   - required field validation,
   - format validation (email/url/date),
   - CRUD operations using models.
4. Reuse existing models in `src/Model/*`.
5. Ensure proper JSON field serialization/deserialization.
6. Return consistent HTTP statuses (200/201/400/401/404).

## Date normalization note
- `jsonresume.schema.json` allows partial ISO8601-like values (`YYYY`, `YYYY-MM`, `YYYY-MM-DD`) in several fields.
- `db.schema.sql` stores many of those values as `datetime`.
- Services should explicitly normalize this mismatch before persistence.

## Run & quality checks
- Preferred environment: dev container (`docker-compose.yml`: `dev` + `mariadb`).
- Useful commands:
  - `vendor/bin/phpunit`
  - `vendor/bin/phpstan analyse src`
  - `vendor/bin/phpcs --standard=phpcs.xml src tests`
  - `vendor/bin/phpmd src text phpmd.xml`

## Key environment variables
- `MARIADB_HOST`, `MARIADB_PORT`, `MARIADB_DATABASE`, `MARIADB_USER`, `MARIADB_PASSWORD`
- `APP_ENV`, `APP_DISPLAY_ERROR_DETAILS`
- `USERNAME`, `PASSWORD` (current login credentials)

## Practical rule for changes
When changing API shape or persistence model, keep these files aligned in the same task:
- `endpoints.yaml`
- `db.schema.sql`
- `jsonresume.schema.json`
