# Contributing

Thanks for contributing to Hoptown Rental.

## Development Setup
1. Start the stack:
   ```bash
   docker compose up -d
   ```
2. Open `http://localhost:8080`, finish WP installer, activate **Hoptown Rental**.

## Branching
- Work on a feature branch and open a PR into `main`.
- Keep changes focused and small.

## Commit Messages (Required)
We use **Conventional Commits** for automated releases.

Examples:
- `feat: add delivery note to bookings`
- `fix: handle empty pickup time`
- `chore: update dependencies`

Breaking changes:
- `feat!: change booking workflow`
- or add `BREAKING CHANGE:` in the commit body.

## Releases
Pushes to `main` trigger **semantic-release**. A release is created only when there is at least one `feat:` or `fix:` commit since the last release.

## Code Style
- Keep PHP formatting consistent with existing code.
- Prefer centralized meta access via repository/service layers.
- Avoid direct `get_post_meta`/`update_post_meta` outside repositories.

## Tests
No automated test suite yet. Validate manually:
- New booking flow
- Booking email notifications
- Admin editing of bookings/inflatables

## Template Overrides
To override templates, place copies in your theme:
- `your-theme/hoptown-rental/archive-hoptown_inflatable.php`
- `your-theme/hoptown-rental/single-hoptown_inflatable.php`
