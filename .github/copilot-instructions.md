## Purpose
Short, actionable guidance for AI coding agents working on this Laravel monolith. Focus on what teams and agents must know to be productive immediately.

### Global Rules – Relaxed Version (Thesis Project)

1. Database Safety (Mode: Always On, Glob: `database/**`, `migrations/**`)
- Do not perform any operation that deletes the entire database without permission.
- Do not `DROP` or `TRUNCATE` any table without explicit approval in chat or PR.
- Creating or modifying tables for development purposes is allowed (use migrations).

2. No Duplication (Mode: Always On, Glob: `src/**`, `app/**`)
- Do not create new logic that duplicates existing functionality.
- If similar or identical logic already exists, modify and improve the existing code instead of writing new code.
- Always reuse existing helpers, functions, or classes where possible.

📌 Optional Notes (Recommended)
- Use clear and descriptive variable and function names.
- Avoid overly complex logic if it can be simplified.
- Make changes only to parts relevant to the request.
- Format code according to Laravel/PSR-12 or the project’s coding standard before final commits.

---

### Global Rules (enforced by humans/CI)
- Database Safety (always): do not run destructive DB operations without human approval. Avoid `DROP`/`TRUNCATE` or any script that wipes production data. Glob: `database/**`, `migrations/**`.
- No Duplication (always): do not introduce new logic that duplicates existing code under `app/**` or `resources/**`. Prefer extending or improving `app/Helpers/*`, `app/Services/*`, or existing Models/Controllers.

### Big picture (how the app is organized)
This is a Laravel monolith. Key directories:
  - `app/Models` — Eloquent models (e.g. `Product.php`).
  - `app/Http/Controllers` — HTTP controllers (route handlers).
  - `app/Livewire` — Livewire components for interactive UI.
  - `app/Services` — business logic helpers (look here before adding logic).
  - `app/Helpers` — utility helpers (see `CodeGeneratorHelper.php`, `TableHelpers.php`, `ImageHelper.php`).
  - `resources/views` — Blade views.
  - `routes/web.php`, `routes/api.php` — entry points for HTTP routes.
  - `database/migrations`, `database/seeders`, `database/factories` — schema and test data.
  - `public/`, `vite.config.js`, `resources/js` — frontend build with Vite.

### Typical request flow (read before editing code)
Request -> `routes/*.php` -> Controller in `app/Http/Controllers` -> Service / Model (`app/Services`, `app/Models`) -> View/Response (Blade / JSON) -> Frontend (Livewire / JS).

### Developer workflows and commands (Windows / cmd.exe examples)
Use the repo's scripts and `start-servers.bat` when available. Common setup:

```
composer install
npm ci
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev        # starts Vite dev server
start-servers.bat  # repo-provided helper for local servers
```

Run tests:
```
php artisan test
vendor\bin\phpunit
```

Build assets for production: `npm run build` (Vite).

### Project-specific conventions
- Follow PSR-12 / Laravel style. Look at `app/Helpers` for reusable helpers; prefer those over adding new helper functions.
- Livewire components live in `app/Livewire` and map to Blade views; update both when changing UI behavior.
- Database migrations are the source of truth for schema. Modify migrations only for development branches; for production schema changes create a new migration.

### Integration points & dependencies to be aware of
- Composer packages: Livewire, Spatie packages, DomPDF, Guzzle, Pusher (see `composer.json`).
- Frontend: Vite + npm packages in `package.json` and `vite.config.js`.
- External services/configs: check `config/*.php` (e.g., `config/reverb.php`) and `.env` for API keys.

### How to make changes (AI agent checklist)
1. Search for similar logic (controllers, services, helpers). If found, update existing code instead of adding new files.
2. If touching DB schema, open a PR and request explicit human approval; never modify production DB directly.
3. Add or update tests under `tests/Feature` for behavior changes.
4. Run `php artisan test` and `npm run dev` (or `npm run build`) locally before submitting a PR.

### Examples (where to change things)
- Add an API endpoint: update `routes/api.php` -> create/modify `app/Http/Controllers/*` -> use `app/Services/*` for heavy logic -> return JSON resource.
- Add a UI page: add Blade in `resources/views`, wire Livewire component in `app/Livewire`, register route in `routes/web.php`.

### Thesis Commit Message Template
Use this format to make change tracking easier and to assist with writing the Implementation chapter of your thesis.

```
[TYPE] Short description

Detailed description:
- What changed
- Why
- Impact / migration notes

References: ISSUE/PR/Thesis section
```

---
Additional enforcement artifacts added to this repo:
- `.githooks/pre-commit` — local hook to scan staged changes for `DROP`/`TRUNCATE` and an allowlist `.githooks/allow-sql-approval.txt`.
- `.github/workflows/db-safety.yml` — CI check that fails PRs containing destructive SQL unless allowed by the allowlist.

If something above is unclear or you want stricter enforcement (different keywords, scan scope, or automated PR labels), tell me which rules to codify and I will update hooks/CI.
