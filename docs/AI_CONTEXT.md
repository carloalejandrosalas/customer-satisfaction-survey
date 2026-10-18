# AI agent context

Customer satisfaction survey admin app. Stack: Laravel 13, Livewire 4 (class components with separate Blade views), Flux UI 2 (free edition), Tailwind CSS 4, Pest 5, spatie/laravel-activitylog 5. Generic Laravel/Boost rules live in `AGENTS.md` and `CLAUDE.md` (generated; do not edit by hand).

For the schema, see [DATABASE_STRUCTURE.md](DATABASE_STRUCTURE.md).

## Architecture

- Each module (Users, Customers, Products, Categories, ActivityLog) is a full-page Livewire component at `app/Livewire/<Module>/Index.php` with a view in `resources/views/livewire/<module>/index.blade.php`. Create/edit happens in a Flux modal on the index page.
- Validation and saving live in form objects in `app/Livewire/Forms/`.
- `App\Livewire\Concerns\WithSearchAndSort` provides search, whitelisted sorting (`sortableColumns()`) and URL-bound pagination. Lists are fixed at 10 rows per page on purpose; users are expected to search instead.
- Layouts: `layouts::app` (default, sidebar) and `layouts::guest` (login). The layout prints `$title` only if set; components set it with `#[Title]`.
- Sidebar links come from `config/navigation.php`; a link may declare `'can' => '<gate>'` to hide it. `active` uses route patterns such as `users.*`.
- Reuse `x-page`, `x-page-header` and `x-common.delete-confirmation` (anonymous Blade components) for new screens.

## Auth and access

- Login is a Livewire component (`App\Livewire\Auth\Login`); logout is `LogoutController`.
- `EnsurePasswordIsChanged` redirects users with `change_password = true` to `/profile`. New users and admin password resets set this flag.
- `RoleEnum` and `QuestionTypeEnum` (in `app/`) are non-backed enums. The DB stores the case **name** as a string and `User` has no enum cast, so `$user->role` is a string (e.g. `'ADMIN'`). Use `Rule::in(array_column(RoleEnum::cases(), 'name'))`, not `Rule::enum`. In factories and tests assign `RoleEnum::X->name`; enum objects break JSON encoding (including the audit log).
- Gate `view-activity-log` (in `AppServiceProvider`) allows ADMIN, SUPERVISOR and TECHNICIAN. No other module has role restrictions or policies yet.
- Users cannot delete themselves.

## Audit logging

- Models using `App\Models\Concerns\Auditable` (User, Customer, Product, Category) log created, updated and deleted events, dirty attributes only, excluding `password`, `remember_token` and `settings`. Log name is the snake-case plural of the model.
- `AppServiceProvider` listens to `Login`, `Logout` and `Failed` and logs events `login`, `logout`, `login_failed` in the `auth` log. Password changes log `password_changed` (own) and `password_reset` (admin).
- Add `Auditable` to new models that should be tracked. Never log secrets.

## Settings and theme

Per-user preferences are stored in the `users.settings` JSON column and read with `$user->setting($key, $default)`. The layout seeds Flux's `localStorage` keys (`flux.appearance`, `flux-sidebar-collapsed-desktop`) from them. Tailwind 4 needs the `@custom-variant dark` line in `resources/css/app.css` for the class-based dark mode.

## Business rules

- Categories cannot be deleted while they have products.
- Unique validation ignores soft-deleted rows (`->whereNull('deleted_at')`).
- Product images go to the `public` disk (run `php artisan storage:link`); updating replaces the old file.
- `Customer::name` is a computed accessor (first + last name).

## Conventions

- Create files with `php artisan make:*`, pass `--no-interaction`, and run `vendor/bin/pint --dirty --format agent` after PHP changes.
- Every feature gets Pest feature tests in `tests/Feature` (use `RefreshDatabase`; use `->fresh()` on factory models when checking `role`).
- Run `pnpm run build` after CSS or class changes; run `php artisan view:clear` after deleting components.
- Flux free has no chart component; the dashboard chart is plain CSS bars. Adding a chart library needs approval.
- Do not change dependencies without approval.

## Status

Done: Users, Customers, Products, Categories, Profile, Settings, Activity Log, Dashboard, Login.
Pending: Surveys (migration exists, `Survey` model is empty, `/surveys` is a placeholder; the migration `down()` only drops `surveys`), policies and role checks across modules, survey widgets on the dashboard. `ExampleTest` fails (302 vs 200) and predates this work.
