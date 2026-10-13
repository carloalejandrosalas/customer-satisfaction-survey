# Database structure (code-derived)

This is the structure the application code currently expects, extracted from its models and controller usage. It is **not** a verified snapshot of the live database: no schema dump, migrations, or DDL are present in the repository. The actual database may contain additional columns or differ in types and constraints.

The configured connection in `application/config/database.php` uses MySQLi and names the database `telcom`. Connection configuration does not establish the schema.

## Confirmed table usage

“Confirmed” here means the application explicitly queries or writes the table name and listed columns; it does not confirm the physical database schema.

### `cliente`

| Column | Observed use |
| --- | --- |
| `ap_cliente` | Inserted and updated as the paternal surname. |
| `am_cliente` | Inserted and updated as the maternal surname. |
| `nombre_cliente` | Inserted and updated as the customer name. |
| `domicilio` | Inserted and updated as the address. |
| `numero` | Inserted, looked up, and used as the predicate for update/deactivation. The code treats it as the customer identifier. |
| `status` | Active listing filters on `1`; deactivation updates to `0`. Insert does not explicitly set it. |

Evidence: `application/models/cliente_model.php`; the controller and views display `numero` as the customer's phone number.

### `producto`

| Column | Observed use |
| --- | --- |
| `nombre_producto` | Inserted as the product name. |
| `monto_producto` | Inserted as the product price. |
| `foto_producto` | Inserted as the image value/path; the listing view reads it below `fotos/`. |
| `descripcion_producto` | Inserted as the product description. |
| `codigo_barras` | Used in product lookup and displayed in the listing. The code does not generate it. |
| `status` | Active listing filters on `1`. Insert does not explicitly set it. |

Evidence: `application/models/producto_model.php` and `application/controllers/producto.php`.

### `usuario`

| Column | Observed use |
| --- | --- |
| `username` | Equality filter in the login lookup. |
| `pass` | Equality filter in the login lookup. The controller passes `md5($password)` as the value to compare. |

Evidence: `application/models/usuario_model.php` and `application/controllers/login.php`.

## Unconfirmed employee data

The employee controller expects query result keys `ap_empleado`, `am_empleado`, `nombre_empleado`, `numero`, and `domicilio`. However, `application/models/empleado_model.php` has empty methods and does not issue any database queries. Therefore, neither an `empleado` table nor those columns can be confirmed from current database access code.

## Schema details not available

The application source does not define:

- Column data types, lengths, precision, character sets, or collations.
- Nullability, default values, generated values, or auto-increment behavior.
- Primary keys, unique constraints, indexes, or foreign keys.
- Whether a physical `empleado` table exists or whether tables contain additional fields.

In particular, `cliente.numero` and `producto.codigo_barras` are used as lookup values, but the code does not prove that either is a primary key or unique. Do not use this document alone to create or alter a production schema. Verify against a database metadata export or authoritative DDL first.

## Source map

- Customer data access: `application/models/cliente_model.php`
- Product data access: `application/models/producto_model.php`
- Login data access: `application/models/usuario_model.php`
- Employee result mapping and stub model: `application/controllers/empleado.php`, `application/models/empleado_model.php`
- Database connection settings: `application/config/database.php`
