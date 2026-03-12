
# json-resume-slim

A **Slim 4 JSON Resume API scaffold** for managing structured resume data with a normalized relational database and RESTful endpoints.

## Overview

This project provides a backend API foundation for the [JSON Resume](https://jsonresume.org/) standard. It uses:

- **Slim 4** framework for routing and middleware
- **MariaDB** with normalized schema (`db.schema.sql`)
- **JSON Resume Schema** (draft-07) for validation
- **Containerized dev environment** with Docker

## Key Features

- RESTful API for resume sections (`basics`, `awards`, `education`, `projects`, `work`, etc.)
- Normalized database model with ULID-like IDs (`char(26)`)
- JSON columns for complex nested fields (e.g., `profiles`, `keywords`, `highlights`)
- Token-based authentication (`auth/login`, `auth/refresh-token`)
- Utility endpoints for ISO data (`country`, `language`, `currency`)
- Static analysis with PHPStan, PHPCS, and PHPMD

## Getting Started

**Start the dev container:**
```bash
docker-compose up dev mariadb
```

**Run inside the container:**
```bash
vendor/bin/phpunit              # Tests
vendor/bin/phpstan analyse src  # Static analysis
vendor/bin/phpcs --standard=phpcs.xml src tests  # Code style
```

## Project Structure

- `src/` – Application code (PSR-4 `App\\` namespace)
- `endpoints.yaml` – API contract documentation
- `db.schema.sql` – Database schema (source of truth)
- `jsonresume.schema.json` – Validation schema (draft-07)

See the [Copilot Instructions](/.github/copilot-instructions.md) for detailed architecture and conventions.
