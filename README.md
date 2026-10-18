# Customer Satisfaction Survey

An admin platform for managing customers, products and customer satisfaction surveys. Built with Laravel 13, Livewire 4, Flux UI (free) and Tailwind CSS 4. The whole interface is in English.

## Features

- **Dashboard**: totals and trends for users, customers, products and categories over a selectable range (24h, 7d, 30d, 12m), a chart of new records, and recent activity.
- **Users**: CRUD, roles, admin password reset (forces a password change on next sign in).
- **Customers, Products, Categories**: CRUD with search, sorting and pagination (10 rows per page). Products support image uploads. Categories with products cannot be deleted.
- **Profile**: edit your own details and change your password.
- **Settings**: per-user theme (light, dark, system) and sidebar preference.
- **Activity Log**: audit trail of model changes, sign ins, sign outs, failed sign ins and password changes. Visible to administrators, supervisors and technicians only.
- **Surveys**: not implemented yet (the tables exist; the page is a placeholder).

All main entities use soft deletes.

## Roles

`ADMIN`, `SUPERVISOR`, `TECHNICIAN`, `OPERATOR` (`App\RoleEnum`, stored as the case name). Only the activity log is role-restricted for now.

## Requirements

PHP 8.5, Composer, Node with pnpm, and SQLite (default) or MariaDB/MySQL.

## Setup

```bash
composer setup            # installs dependencies, creates .env, key, migrates, builds assets
php artisan migrate:fresh --seed
php artisan storage:link  # product images
composer dev              # serves the app and Vite
```

The seeder creates an admin user from `DEFAULT_ADMIN_NAME`, `DEFAULT_ADMIN_EMAIL` and `DEFAULT_ADMIN_PASSWORD` (defaults: `ADMIN`, `admin@localhost.com`, `password`), plus sample users, categories, products and customers. Change the defaults outside local development.

Optional MariaDB and Adminer via Docker: copy `.env.docker.example`, set the credentials, then `docker compose up -d` and point `DB_*` in `.env` at it.

## Testing and formatting

```bash
php artisan test --compact
vendor/bin/pint --dirty
```

`ExampleTest` currently fails because `/` redirects to the login page.

## Structure

- `app/Livewire/<Module>/Index.php`: one full-page Livewire component per module, using the `WithSearchAndSort` trait (`app/Livewire/Concerns`).
- `app/Livewire/Forms/`: Livewire form objects holding validation and persistence per entity.
- `app/Models/Concerns/Auditable.php`: adds activity logging to a model (spatie/laravel-activitylog).
- `resources/views/livewire/`: component views; `resources/views/components/`: reusable `x-page`, `x-page-header` and `x-common.delete-confirmation`.
- `config/navigation.php`: sidebar links (optional `can` gate per link).
- `routes/web.php`: guest login; authenticated routes, with the password-change middleware (`EnsurePasswordIsChanged`) on everything except profile and logout.

More detail for contributors and AI agents: [docs/AI_CONTEXT.md](docs/AI_CONTEXT.md) and [docs/DATABASE_STRUCTURE.md](docs/DATABASE_STRUCTURE.md).
