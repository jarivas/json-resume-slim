# Copilot Instructions for `json-resume-slim`

## Purpose
This repository implements a JSON Resume REST API using Slim 4 and MariaDB.

Use this document as the operational context for Copilot in this codebase.

## Project snapshot (current repository state)
- Auth is implemented (`POST /auth/login`, `POST /auth/logout`, `POST /auth/refresh-token`) with tokens persisted in `tokens`.
- Most resource endpoints (`basic`, `award`, `education`, etc.) are documented in contract/schema files, but not fully wired in routes/controllers yet.
- `src/routes.php` currently registers auth routes only.
- For pending resources, treat `endpoints.yaml`, `db.schema.sql`, and `jsonresume.schema.json` as source of truth.

## Confirmed project decisions
- Canonical route style is without `/api` prefix (for now).
- Login payload is `username` + `password`.
- Keep this guide in English.

## Source of truth (priority order)
1. `db.schema.sql` (actual persistence model: tables, columns, FK, types, JSON checks).
2. `jsonresume.schema.json` (JSON Resume payload shape and semantic validation).
3. `endpoints.yaml` (expected HTTP contract).
4. Existing code in `src/` (implementation patterns).

If contract and code diverge, prefer schema + agreed team decisions and document mismatches in your change notes.

## Architecture and data model
- Domain is JSON Resume, split into normalized tables (`db.schema.sql`) with `basics` as the parent aggregate.
- Child resources (`awards`, `educations`, `projects`, etc.) reference `basics.id` via `basic_id` foreign keys.
- IDs are ULID-like `char(26)` across all tables; for new records use `Ulid::generate()->__toString()`.
- Several nested JSON Resume fields are stored as JSON columns with DB-level checks (for example `basics.location`, `basics.profiles`, `skills.keywords`, `projects.highlights`). Preserve JSON shape and `json_valid(...)` compatibility.
- Auth/session state is persisted in `tokens` (`token`, `expires_at`) and should stay separate from resume content tables.

## Project architecture
- Framework: Slim 4.
- Namespace: `App\\` mapped to `src/` (PSR-4).
- Entry point: `public/index.php`.
- Bootstrap: `App\Helper\App`.
- Routes: `src/routes.php`.
- Main pattern:
  - `Controller` receives request/response and delegates.
  - `Service` parses request body (`setData`) and executes logic.
  - `Model` handles DB operations (lightweight ORM-style layer).

## API contract conventions
- Endpoint contract is documented in `endpoints.yaml`; use it to implement/verify routes.
- Resource pattern is consistent:
  - collection: `POST /<resource>`, `GET /<resource>`
  - item: `PATCH /<resource>/{id}`, `DELETE /<resource>/{id}`
  - examples: `basic`, `award/{award_id}`, `work/{work_id}`
- Auth-related endpoints exist: `auth/login`, `auth/refresh-token`, `auth/logout`.
- Additional utility endpoints are documented: `iso/country`, `iso/language`, `iso/currency`, `chat`.
- The endpoint file includes generated examples where `controller`, `method`, and `route` are `null`; do not treat those fields as implementation metadata.

## Implemented authentication
### Flow
- `POST /auth/login`
  - Validate `username` and `password` against `.env` (`USERNAME`, `PASSWORD`).
  - Create random token + expiration (`+1 hour`) in `tokens`.
- `POST /auth/logout` (protected)
  - Read token from `Authorization: Bearer <token>`.
  - Delete token from DB.
- `POST /auth/refresh-token` (protected)
  - Delete current token and create a new one.

### Middleware
- `App\Middleware\Route\Authentication`:
  - Reject when bearer token is missing or expired.
  - Return `401` JSON: `{ "error": "Unauthorized" }`.

## Validation and schema alignment
- JSON Resume payload expectations come from `jsonresume.schema.json` (draft-07).
- Date-like resume fields use the schema `iso8601` pattern (`YYYY`, `YYYY-MM`, or `YYYY-MM-DD`) even when DB columns are `datetime`; normalize this mismatch explicitly in implementation code.

## Implementation conventions
- Keep DB column naming conventions (snake_case where applicable).
- Preserve `Controller -> Service -> Model` flow.
- Use `Controller::respond()` for JSON responses.
- Keep request body parsing in `Service::setData()` (parsed body or JSON body).
- Centralize route registration in `src/routes.php`.

## How to implement pending endpoints
1. Define/confirm route in `src/routes.php`.
2. Add controller per endpoint/resource in `src/Controller/...`.
3. Add service in `src/Service/...` with required-field and format validation, then CRUD using models.
4. Reuse existing models in `src/Model/*`.
5. Ensure JSON field serialization/deserialization consistency.
6. Return consistent statuses (200/201/400/401/404).

## Developer workflow in this repo
- Preferred environment is the dev container (`.devcontainer.json`) with `docker-compose.yml` services `dev` + `mariadb`.
- The `dev` service is intentionally long-running idle (`sleep`) so commands are run interactively inside the container.
- Run quality tools directly (no composer scripts currently defined):
  - `vendor/bin/phpunit`
  - `vendor/bin/phpstan analyse src`
  - `vendor/bin/phpcs --standard=phpcs.xml src tests`
  - `vendor/bin/phpmd src text phpmd.xml`
- `phpstan.neon` analyzes only `src` at level `8`; keep new code in `src` to stay inside static-analysis coverage.

## Key environment variables
- `MARIADB_HOST`, `MARIADB_PORT`, `MARIADB_DATABASE`, `MARIADB_USER`, `MARIADB_PASSWORD`
- `APP_ENV`, `APP_DISPLAY_ERROR_DETAILS`
- `USERNAME`, `PASSWORD`

## Practical rule for changes
When changing API shape or persistence fields, keep these files aligned in the same task:
- `endpoints.yaml`
- `db.schema.sql`
- `jsonresume.schema.json`
