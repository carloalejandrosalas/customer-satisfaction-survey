# Database structure

Defined by the migrations in `database/migrations`. SQLite is the default (tests use in-memory SQLite); MariaDB is supported. Tables marked (SD) use soft deletes (`deleted_at`). All tables have `id` and timestamps unless noted.

## users (SD)

`name`, `email` (unique), `email_verified_at`, `password`, `state`, `city`, `address`, `role` (enum of `RoleEnum` case names: ADMIN, SUPERVISOR, OPERATOR, TECHNICIAN), `phone_number`, `change_password` (bool, default false), `settings` (JSON, nullable: `theme`, sidebar preference), `remember_token`.

Also standard Laravel tables: `password_reset_tokens`, `sessions`, `cache`, `jobs`.

## categories (SD)

`name`, `description`.

## products (SD)

`category_id` (FK categories), `name`, `price` (decimal 10,2), `image` (nullable path on the `public` disk), `description`, `sku` (indexed), `upc` (nullable, indexed), `status` (bool, default true).

## customers (SD)

`first_name`, `last_name`, `state`, `city`, `residence_address`, `phone_number` (indexed), `email_address` (indexed), `status` (bool, default true). `name` is an accessor, not a column.

## activity_log

From spatie/laravel-activitylog: `log_name`, `description`, `subject` (morph), `event`, `causer` (morph), `attribute_changes` (JSON), `properties` (JSON). Used events: `created`, `updated`, `deleted`, `login`, `logout`, `login_failed`, `password_changed`, `password_reset`.

## Survey tables (migration only, no models or UI yet)

- `surveys`: `category_id`, `creator_id` (user), `name`, `description`, `status`.
- `surveys_customers`: `survey_id`, `customer_id`, `assigned_user_id`.
- `survey_questions`: `survey_id`, `text`, `type` (enum of `QuestionTypeEnum`: YES_NO, POINTS, TEXT).
- `survey_answers`: `survey_customer_id`, `survey_question_id`, `value`.

None of the survey columns have foreign key constraints yet (plain indexed ids), and the migration's `down()` only drops `surveys`.
