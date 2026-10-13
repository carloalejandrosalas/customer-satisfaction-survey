# Agent instructions

- Read [README.md](README.md) for setup and [docs/AI_CONTEXT.md](docs/AI_CONTEXT.md) for architecture, routes, and the code-derived data model.
- This is a legacy CodeIgniter PHP application. Keep application changes under `application/` and avoid changing the vendored `system/` framework unless explicitly required.
- The repository does not contain database DDL or migrations. Treat documented columns as code observations, not a complete schema; do not invent column types, constraints, defaults, indexes, or foreign keys.
- Preserve the existing Spanish identifiers and CodeIgniter controller/model conventions when extending the app.
- Keep environment-specific database configuration and credentials out of documentation and commits. Verify database assumptions against the target environment before changing persistence behavior.
- When changing application behavior, run the relevant PHP syntax checks and manually exercise the affected route when a configured database is available. There is no evident project test command.
