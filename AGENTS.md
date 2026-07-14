# Project Instructions

See CLAUDE.md for the Laravel Boost guidelines (framework conventions, tooling, coding standards). This file holds project-specific instructions that supplement or override those guidelines.

## Testing

- Do not write, add, or modify automated tests in this project.
- Do not run the test suite (`php artisan test`, `phpunit`, etc.) as part of your workflow.

## Database Safety

- Never run `php artisan migrate` or `php artisan migrate:fresh`. This project's database holds live data and running migrations risks irreversible data loss.
- If a schema change requires a migration to be run, tell the user and let them run it themselves.

## Module Reference Docs

- Before working in the People/HR Experts module, read `docs/context/people-module.md` for how it's wired together and known gotchas.
