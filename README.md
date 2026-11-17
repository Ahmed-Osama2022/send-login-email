# Current Time Mailer

A small Laravel 12 application that emails the current time and records the
moment of every send in a `times` table. Built as a reference for the
command → queued job → mailable pipeline.

- [Laravel 12](https://laravel.com/docs/12.x) on PHP 8.2+
- Queue: `database` driver (SQLite for storage)
- Mail: any Laravel mailer; Brevo's SMTP relay is pre-wired in `.env`
- Frontend: Tailwind CSS **v3** compiled by Vite, packages managed with **bun**

---

## Overview

```
php artisan mail:send-current-time you@example.com
        │
        ▼
SendCurrentTimeEmail (ShouldQueue)          rows land in `jobs`
        │
        ▼  worker picks it up
$sentAt = now()
Time::create([...])                         one row per attempt in `times`
        │
        ▼
CurrentTimeMail                             resources/views/emails/current-time.blade.php
        │
        ▼
Mail::to(...)->send(...)                    SMTP 250 = accepted for delivery
```

The timestamp is captured **once** inside the job and reused for both the
database row and the email body, so the two can never disagree.

---

## Requirements

| Tool     | Version                                        |
| -------- | ---------------------------------------------- |
| PHP      | 8.2+ with `pdo_sqlite`                         |
| Composer | 2.x                                            |
| Bun      | 1.0+ (used instead of npm)                     |
| Node     | 20+ (Vite is still a Node tool under the hood) |

---

## Quick start

```bash
# 1. PHP dependencies
composer install

# 2. JavaScript dependencies (bun, not npm)
bun install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Database (creates database/database.sqlite + tables)
touch database/database.sqlite
php artisan migrate

# 5. Frontend assets
bun run build          # production build
# or: bun run dev      # Vite dev server with HMR
```

Serve the app:

```bash
php artisan serve                       # http://127.0.0.1:8000
php artisan queue:work                  # required for queued sends
```

`composer run dev` also exists, but its script calls `npm run dev`; if you stay
on bun, run the three processes yourself (`serve`, `queue:work`, `bun run dev`).

---

## Configuration

Relevant `.env` keys:

```dotenv
APP_NAME="Send-login-Email"
APP_TIMEZONE=Africa/Cairo      # drives now() and the rendered email

DB_CONNECTION=sqlite
QUEUE_CONNECTION=database

MAIL_MAILER=log                # log | smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_SCHEME=null               # 587 → STARTTLS negotiated automatically
MAIL_USERNAME=your-login@smtp-brevo.com
MAIL_PASSWORD=                 # Brevo SMTP key (xsmtpsib-...), not the dashboard password
MAIL_FROM_ADDRESS="you@verified-domain.test"
MAIL_FROM_NAME="${APP_NAME}"
```

- `MAIL_MAILER=log` writes the full message to `storage/logs/laravel.log` and
  needs no credentials. Use it for local development.
- `MAIL_MAILER=smtp` performs a real SMTP handshake. For Brevo, `MAIL_USERNAME`
  is the SMTP login and `MAIL_PASSWORD` is the **SMTP key** from
  _Transactional → Settings → SMTP & API → SMTP_, not your account password and
  not the API v3 key (`xkeysib-...`).
- `MAIL_FROM_ADDRESS` must be a verified sender at the provider, otherwise the
  relay accepts then bounces the message.

---

## Usage

```bash
# Queue a send (a worker must be running)
php artisan mail:send-current-time you@example.com

# Same, with a personalised greeting
php artisan mail:send-current-time you@example.com --name="Ahmed"

# Send immediately in the current process, no worker needed
php artisan mail:send-current-time you@example.com --sync

# Keep the queue alive
php artisan queue:work

# Re-run failed jobs after fixing credentials
php artisan queue:failed
php artisan queue:retry all
```

The address is validated before anything is dispatched. An invalid address
prints an error and dispatches nothing.

---

## How it works

| File                                                           | Responsibility                                                             |
| -------------------------------------------------------------- | -------------------------------------------------------------------------- |
| `app/Console/Commands/SendCurrentTimeEmail.php`                | Validates the address, dispatches the job (or runs it with `--sync`)       |
| `app/Jobs/SendCurrentTimeEmail.php`                            | `ShouldQueue`; stamps `now()`, inserts the `times` row, sends the mailable |
| `app/Mail/CurrentTimeMail.php`                                 | Subject `Current time: …`, renders the Blade view                          |
| `resources/views/emails/current-time.blade.php`                | HTML body with formatted time, timezone and ISO-8601                       |
| `app/Models/Time.php`                                          | Eloquent model for the `times` table (`time` cast to `datetime`)           |
| `database/migrations/2026_11_17_000000_create_times_table.php` | Schema for the audit table                                                 |

### Job behaviour

- `public int $tries = 3` — three attempts before the job is moved to
  `failed_jobs`.
- `backoff()` → `[10, 30, 60]` seconds between attempts.
- The `times` row is written **before** the transport is called, so a row means
  "an attempt started", not "the message was delivered".

### `times` schema

| Column                      | Type             | Notes                               |
| --------------------------- | ---------------- | ----------------------------------- |
| `id`                        | bigint           | Primary key                         |
| `email`                     | varchar, indexed | Recipient                           |
| `time`                      | timestamp        | The instant captured before sending |
| `timezone`                  | varchar(64)      | Zone name, e.g. `Africa/Cairo`      |
| `created_at` / `updated_at` | timestamp        | Eloquent timestamps                 |

---

## Verifying a send

| Layer                     | How to check                                                   |
| ------------------------- | -------------------------------------------------------------- |
| App rendered & handed off | `grep -A3 "^To:" storage/logs/laravel.log` (log mailer only)   |
| Queue finished            | `php artisan queue:failed` should be empty; `jobs` table empty |
| Relay accepted (250)      | No exception; a rejection throws and lands in `failed_jobs`    |
| Delivered to the inbox    | Provider dashboard → Transactional → Logs (Brevo)              |

A `times` row alone does **not** prove delivery. A bounce or an auth failure
still leaves a row behind, and retries create one row per attempt.

`Mail::fake()` in tests asserts intent only — it never touches a transport.

---

## Testing

```bash
php artisan test
# or a single file
php artisan test tests/Feature/ExampleTest.php
```

The shipped tests are the Laravel examples; there is no coverage of the mail
flow yet. A useful addition would be a feature test using `Queue::fake()` plus
assertions on the dispatched job, and a second one using `Mail::fake()` to
assert `CurrentTimeMail` was sent with the expected timestamp.

---

## Frontend Docs. Page

- Tailwind CSS **v3** through PostCSS (`tailwind.config.js`, `postcss.config.js`),
  not the v4 `@tailwindcss/vite` plugin.
- Source: `resources/css/app.css` (`@tailwind base/components/utilities`).
- Entry: `vite.config.js` → `resources/css/app.css`, `resources/js/app.js`.
- Build: `bun run build` → `public/build/manifest.json`.
- The welcome page (`resources/views/welcome.blade.php`) is a document-style
  layout using only Tailwind utilities; no JavaScript.

Changing a Blade file while `bun run dev` is running triggers a browser reload
via `laravel-vite-plugin`'s `refresh: true`.

---

## Troubleshooting

| Symptom                                                  | Cause                                                         | Fix                                                                          |
| -------------------------------------------------------- | ------------------------------------------------------------- | ---------------------------------------------------------------------------- |
| `INFO Job queued`, nothing arrives, log has a full email | Worker booted while `MAIL_MAILER=log`                         | Restart the worker: `php artisan queue:restart` (or kill it and start again) |
| `525 5.7.1 Unauthorized IP address`                      | Brevo SMTP key restricted to specific IPs                     | Add your public IP in the key's _Authorized IPs_, or lift the restriction    |
| `535 Authentication failed`                              | Wrong SMTP key, or `xkeysib-…` API key used as password       | Regenerate the SMTP key and align `MAIL_USERNAME`                            |
| Connection timeout / refused                             | Port filtered by ISP or firewall                              | Try 587, 2525, or 465 with `MAIL_SCHEME=smtps`                               |
| `FAIL` in the worker with a `TransportException`         | Real SMTP error, surfaced only after leaving the `log` driver | Read `failed_jobs.exception`, fix, `queue:retry all`                         |
| Accepted by the relay but no inbox delivery              | Unverified `MAIL_FROM_ADDRESS` or spam filtering              | Verify the sender; check the provider's transactional log                    |
| `.env` edits ignored                                     | `config:cache` was run, or the process started earlier        | `php artisan config:clear`, restart long-running processes                   |

Any `.env` change is boot-time input: workers and `serve` must be restarted
before they see it. `php artisan config:show mail` prints what a fresh process
would load.

---

## Project layout

```
app/
  Console/Commands/SendCurrentTimeEmail.php
  Jobs/SendCurrentTimeEmail.php
  Mail/CurrentTimeMail.php
  Models/Time.php
database/
  migrations/2026_11_17_000000_create_times_table.php
  database.sqlite
resources/
  css/app.css
  views/emails/current-time.blade.php
  views/welcome.blade.php
routes/web.php
tailwind.config.js
postcss.config.js
vite.config.js
```

Code style: `.editorconfig` mandates **2-space** indentation, LF line endings
and a final newline. `laravel/pint` is installed but formatting is expected to
match `.editorconfig` rather than a Pint preset.

---

## License

Released under the [MIT license](https://opensource.org/licenses/MIT).
