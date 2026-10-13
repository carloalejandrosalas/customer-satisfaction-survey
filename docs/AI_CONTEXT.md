# AI agent context

This document records application behavior visible in the checked-in code. It is not a database contract: there is no schema dump, DDL, or migration history in the repository. Verify all schema details against the deployed database before writing migrations or changing persistence logic.

For the standalone table and column inventory, see [DATABASE_STRUCTURE.md](DATABASE_STRUCTURE.md).

## Project structure and request flow

- The application is built on the vendored CodeIgniter framework. The front controller is `index.php`; application code lives in `application/`.
- `application/config/routes.php` sets `login` as the default controller. With CodeIgniter's default routing, application endpoints follow `index.php/<controller>/<method>/<arguments>`.
- `application/config/autoload.php` loads the database and session libraries and the URL helper.
- `Login` authenticates through `Usuario_model`, starts a session with the `username` key on success, and destroys the session on logout. The `Cliente`, `Producto`, `Empleado`, `Usuario`, `Bienvenido`, and `Prueba` controllers check that session value before serving protected pages.
- Controllers use CodeIgniter's query builder via models. The corresponding model files are under `application/models/`; views are under `application/views/`.
- The project has no application-level test command or database migration workflow evident in the repository. `system/` is framework code; prefer changing `application/` rather than the framework.

## Code-derived table inventory

The field names below are used or expected by the application code. SQL types, nullability, primary keys, unique constraints, indexes, defaults, and relationships are not declared in this repository.

### `cliente` — actively queried and written

| Column | Evidence in application | Notes |
| --- | --- | --- |
| `ap_cliente` | Insert and update in `application/models/cliente_model.php` | Customer paternal surname. |
| `am_cliente` | Insert and update in `application/models/cliente_model.php` | Customer maternal surname. |
| `nombre_cliente` | Insert and update in `application/models/cliente_model.php` | Customer name. |
| `domicilio` | Insert and update in `application/models/cliente_model.php` | Address. |
| `numero` | Insert, lookup, update predicate, and deactivation predicate in `application/models/cliente_model.php` | Used as the customer identifier in code; its database type and uniqueness are unverified. |
| `status` | Active-list filter and deactivation update in `application/models/cliente_model.php` | Active rows are selected with `status = 1`; deactivation writes `0`. Creation does not explicitly set it. |

### `producto` — partly implemented

| Column | Evidence in application | Notes |
| --- | --- | --- |
| `nombre_producto` | Insert in `application/models/producto_model.php` | Product name. |
| `monto_producto` | Insert in `application/models/producto_model.php` | Product price; numeric type/precision are not established by code. |
| `foto_producto` | Insert in `application/models/producto_model.php` | Image value/path. The view prefixes it with `fotos/`. |
| `descripcion_producto` | Insert in `application/models/producto_model.php` | Product description. |
| `codigo_barras` | Lookup in `application/models/producto_model.php`; displayed by `application/controllers/producto.php` | Used to find/display a product; uniqueness and generation are not implemented here. |
| `status` | Active-list filter in `application/models/producto_model.php` | Listing selects `status = 1`; product creation does not explicitly set it. |

Product creation is implemented in the model/controller. Product `baja()` and `modificacion()` controller methods are empty even though the listing view renders action links.

### `usuario` — login lookup only

| Column | Evidence in application | Notes |
| --- | --- | --- |
| `username` | Equality filter in `application/models/usuario_model.php` | Username used to find an account. |
| `pass` | Equality filter in `application/models/usuario_model.php` | Login passes `md5($password)` to the model. The stored representation is not otherwise defined here; verify existing data before any authentication change. |

No account creation, password update, or account deactivation operation is implemented in this model.

### Employee-shaped fields — unconfirmed table

`application/controllers/empleado.php` maps the following result keys: `ap_empleado`, `am_empleado`, `nombre_empleado`, `numero`, and `domicilio`. However, `application/models/empleado_model.php` contains empty methods and does not query or write a table. These names suggest an employee record shape but do **not** confirm that an `empleado` table exists or that these columns are present in the database.

### Database settings and limits

`application/config/database.php` selects the `default` MySQLi connection and names the database `telcom`. This is configuration, not a schema definition. The models use `SELECT *` for customer and product reads, so runtime records may include additional columns not identifiable from the source.

No foreign-key relationships are expressed in the application code. Do not infer that `cliente.numero` or `producto.codigo_barras` is a primary key, or infer database column types from HTML input controls or PHP variable names.

## Existing conventions and behavior

- Keep the existing Spanish controller, model, method, and database identifiers when making localized changes.
- Controller/model names follow CodeIgniter's conventional mapping, e.g. `Producto` / `Producto_model` and `application/models/producto_model.php`.
- Customer records are listed only when `status = 1`; customer deactivation is a soft update to `status = 0`.
- Product records are listed only when `status = 1`; no product deactivation operation is currently implemented.
- The login model counts matching rows in `usuario` using `username` and a supplied `pass` value. The controller supplies the MD5 digest of the submitted password.
- Customer and product forms/controllers uppercase submitted strings. Avoid changing case normalization or identifier semantics without checking existing production data.
- Login and application state depend on CodeIgniter sessions. Protected controllers redirect unauthenticated users to `login`.

## Guidance for changes

1. Read the relevant controller, model, and view together before changing user-visible behavior.
2. Treat this context's inventory as observed field usage, not a complete or canonical schema.
3. Do not fabricate SQL schema, seed credentials, relations, or defaults. Obtain them from a trusted database schema/source before making database changes.
4. Keep environment-specific database configuration out of documentation and version control.
5. Preserve compatibility with the bundled legacy framework and verify PHP syntax. If a database is available, manually exercise the routes affected by the change.
