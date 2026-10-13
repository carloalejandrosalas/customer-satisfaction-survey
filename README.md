# Inventory and customer management system

A small PHP application built on CodeIgniter 3. It provides a login flow and partial CRUD screens for customers and products. The repository also contains employee-related stubs; the employee workflow and several product actions are not implemented.

## Requirements

- A PHP runtime compatible with the CodeIgniter version bundled in `system/`.
- MySQL or MariaDB with the PHP MySQLi extension.
- A web server configured to serve this repository and pass application requests to `index.php`.
- A database containing the tables and columns expected by the application.

The default route opens the login page. CodeIgniter and the application are in the repository root and `application/`; there is no separate frontend build.

## Local configuration

1. Copy `.env.example` to `.env` and replace both example passwords with local development values. `.env` is gitignored; do not use these example values outside local development.
2. Start MariaDB and Adminer with `docker compose up -d`. MariaDB is available on `127.0.0.1:3306`; Adminer is at <http://localhost:8080>. In Adminer, use server `mariadb`, database `telcom`, and the `MARIADB_USER` / `MARIADB_PASSWORD` values from `.env`.
3. Configure `application/config/database.php` for local access: use hostname `127.0.0.1`, the configured MariaDB username/password, and database `telcom`. CodeIgniter does not load the Compose `.env` automatically. Do not commit local credentials.
4. Provision a compatible application schema. The Compose setup creates an empty database only; no schema dump, DDL, migrations, or seed data are included. See [docs/DATABASE_STRUCTURE.md](docs/DATABASE_STRUCTURE.md) for the code-derived table and column inventory, including unknown schema details.
5. Configure the web server's document root and rewrite behavior for CodeIgniter. Browse to the application through `index.php/login` (or the equivalent URL for your server configuration).
6. Sign in with an account present in the `usuario` table. No seed data or default credentials are provided.

The database and Adminer ports can be changed with `DB_PORT` and `ADMINER_PORT` in `.env`. To stop the services without removing database data, run `docker compose down`. The named `mariadb_data` volume persists across restarts.

## Application areas

- **Login:** `application/controllers/login.php`, `application/models/usuario_model.php`.
- **Customers:** `application/controllers/cliente.php`, `application/models/cliente_model.php`.
- **Products:** `application/controllers/producto.php`, `application/models/producto_model.php`.
- **Employees:** controller and model exist, but the employee model methods are empty.

Customer deactivation and active-list filtering use a `status` column. Product listing also filters by `status`; the product controller's delete and modification actions are currently empty. Confirm actual database behavior and schema before relying on assumptions about defaults or constraints.

## Development and tests

There is no project-specific test suite or build script evident in the repository. For PHP changes, use `php -l path/to/changed-file.php` for syntax validation and manually test the affected route against a configured database.

## Further documentation

- [AI agent context](docs/AI_CONTEXT.md) — architecture, route behavior, and the code-derived table inventory.
- [Database structure](docs/DATABASE_STRUCTURE.md) — table and column usage inferred from the current application code.
- [Docker Compose](compose.yaml) — local MariaDB and Adminer services.
- [CodeIgniter user guide](user_guide/index.html) — framework documentation bundled with the repository.
