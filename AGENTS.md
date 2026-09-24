# IZGIOS Agent Instructions

This file defines the default working rules for Codex and other coding agents operating on the IZGIOS repository.

## Project Context

- Project: IZGIOS
- Framework: Laravel 13
- PHP: 8.3+
- Frontend: Vue 3, Vite 8, Tailwind CSS 4
- Primary development branch: `gelistirme-kullanici-sistemi`
- Production may run from the same branch, but production changes must never be assumed safe.

## Core Working Rule

Work locally first. Test locally. Commit only validated changes. Deploy to production only after explicit user approval.

Never use production as a development environment.

## Production Safety

Unless the user explicitly asks for a production change, do not:

- run `git pull`, `git switch`, `git reset`, or deployment commands on production;
- run `php artisan migrate`, `migrate:fresh`, `db:seed`, destructive Artisan commands, or database writes on production;
- modify production `.env` values;
- restart web, queue, database, or system services;
- delete, move, overwrite, or chmod production files;
- edit production database rows;
- expose or commit passwords, API keys, tokens, SSH keys, APP_KEY values, or other secrets.

Read-only inspection of production is acceptable when needed to diagnose or mirror the live environment.

## Local Development Environment

Preferred Windows local setup:

- Laragon
- PHP 8.3+
- Composer 2.x
- Node.js 22+
- npm 10+
- MySQL 8.x

The local development database should use a separate local database, such as `izgios_local`.

Do not point a local development environment at the production database.

## Local Startup

From the repository root:

```bash
composer install
npm install
```

Run Laravel in one terminal:

```bash
php artisan serve
```

Run Vite in a second terminal:

```bash
npm run dev
```

Default local application URL:

```text
http://127.0.0.1:8000
```

## Database Rules

- Prefer a fresh copy of the production database imported into a local-only database when reproducing production behavior.
- Before running migrations, inspect migration history and current schema.
- Never use `migrate:fresh` on a database containing data that must be preserved.
- Treat migration conflicts as code issues to investigate, not as permission to modify production immediately.
- When database credentials are needed, do not print or commit secrets.

## Git Workflow

1. Start from `gelistirme-kullanici-sistemi` unless the user explicitly requests another branch.
2. Inspect `git status` before making changes.
3. Keep changes focused on the requested task.
4. Do not include unrelated formatting or cleanup.
5. Run relevant tests/checks before committing.
6. Summarize changed files and behavior clearly.
7. Commit only after the change is coherent and locally validated.
8. Do not merge to `main` or deploy to production without explicit user approval.

## Coding Guidelines

- Follow existing Laravel conventions and project structure.
- Prefer small, reversible changes.
- Reuse existing services, models, policies, helpers, and components before introducing new abstractions.
- Preserve tenant/firma boundaries where applicable.
- Preserve existing authorization behavior unless the task explicitly changes permissions.
- Validate user input through Laravel validation/Form Requests where appropriate.
- Avoid hard-coded credentials, URLs, IDs, role names, or environment-specific values.
- Keep UI behavior consistent with existing layouts and components.
- Do not silently change database schema or business rules.

## Before Editing

For each task:

1. Identify the relevant routes, controllers, models, services, migrations, views/components, and tests.
2. Trace the current behavior before changing it.
3. Check whether the requested behavior already exists elsewhere in the codebase.
4. Consider database, authorization, tenant/firma isolation, and backward compatibility.
5. State any ambiguity before making a risky assumption.

## Validation

Where applicable, run:

```bash
php artisan test
```

For frontend work, also run:

```bash
npm run build
```

If the full test suite is not practical, run the narrowest relevant checks and clearly state what was and was not tested.

## Secrets and Sensitive Files

Never commit:

- `.env`
- database dumps containing real production data
- credentials
- private keys
- access tokens
- production backups

Use `.env.example` only for non-secret configuration names and safe example values.

## Agent Communication

When completing a coding task, report:

- what changed;
- which files changed;
- what was tested;
- any migration/configuration steps required;
- any remaining risks or follow-up work.

Do not claim a change is deployed or production-safe unless it was explicitly verified.
