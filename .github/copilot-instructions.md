# Copilot Instructions for `json-resume-slim`

## Project snapshot (current repository state)
- This repository is a Slim 4 JSON Resume API scaffold (`composer.json`), but runtime app code is not yet implemented in `src/Controller`, `src/Middleware`, or `public`.
- Treat `endpoints.yaml`, `db.schema.sql`, and `jsonresume.schema.json` as the source of truth for behavior until route/controller code exists.

## Architecture and data model
- Domain is JSON Resume, split into normalized tables (`db.schema.sql`) with `basics` as the parent aggregate.
- Child resources (`awards`, `educations`, `projects`, etc.) reference `basics.id` via `basic_id` foreign keys.
- IDs are ULID-like `char(26)` across all tables; keep new IDs compatible with this format.
- Several nested JSON Resume fields are stored as JSON columns with DB-level checks (for example `basics.location`, `basics.profiles`, `skills.keywords`, `projects.highlights`). Preserve JSON shape and `json_valid(...)` compatibility.
- Auth/session state is persisted in `tokens` (`token`, `expires_at`) and should stay separate from resume content tables.

## API contract conventions
- Endpoint contract is documented in `endpoints.yaml`; use it to implement/verify routes.
- Resource pattern is consistent:
  - collection: `POST /<resource>`, `GET /<resource>`
  - item: `PATCH /<resource>/{id}`, `DELETE /<resource>/{id}`
  - examples: `basic`, `award/{award_id}`, `work/{work_id}`
- Auth-related endpoints exist: `auth/login`, `auth/refresh-token`, `auth/logout`.
- Additional utility endpoints are documented: `iso/country`, `iso/language`, `iso/currency`, `chat`.
- The endpoint file includes generated examples where `controller`, `method`, and `route` are `null`; do not treat those fields as implementation metadata.

## Validation and schema alignment
- JSON Resume payload expectations come from `jsonresume.schema.json` (draft-07).
- Date-like resume fields use the schema `iso8601` pattern (`YYYY`, `YYYY-MM`, or `YYYY-MM-DD`) even when DB columns are `datetime`; normalize this mismatch explicitly in implementation code.

## Developer workflow in this repo
- Preferred environment is the dev container (`.devcontainer.json`) with `docker-compose.yml` services `dev` + `mariadb`.
- The `dev` service is intentionally long-running idle (`sleep`) so commands are run interactively inside the container.
- Run quality tools directly (no composer scripts currently defined):
  - `vendor/bin/phpunit`
  - `vendor/bin/phpstan analyse src`
  - `vendor/bin/phpcs --standard=phpcs.xml src tests`
  - `vendor/bin/phpmd src text phpmd.xml`
- `phpstan.neon` analyzes only `src` at level `8`; keep new code in `src` to stay inside static-analysis coverage.

## Coding expectations for future implementation
- Follow PSR-4 namespace mapping `App\\` => `src/` from `composer.json`.
- When adding routes, centralize registration in `src/routes.php` to match project structure.
- Keep schema-first consistency: update `endpoints.yaml`, `db.schema.sql`, and `jsonresume.schema.json` together when changing API shape or persistence fields.
