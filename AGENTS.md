# Agents

SpartanSound is a Spring 2017 CMPE 272 class site: one long PHP page, a MySQL catalog, a text-file login, and a cookie of recently opened products. Read [README.md](README.md) for the course context. This file is how to change the code without turning it into a different project.

## Architecture

`index.php` includes `includes/dbconnect.php` and then the section files in page order. `dbconnect.php` loads `.env`, opens mysqli, and leaves `$conn` in scope. Queries live in `includes/db.php`. Pure helpers (cookies, search SQL, keyword tokens, credential lines, path checks) live in `includes/lib.php`.

Routable scripts:

| URL | Role |
| --- | --- |
| `index.php` | The whole public page |
| `viewproduct.php?id=` | One product, increments `hits`, updates the `lastids` cookie |
| `includes/loginvalidation.php` | POST login against `txt/credentials.txt` |
| `secure.php` | Session-gated list of those usernames |
| `includes/logout.php` | Ends the session |
| `includes/adduser.php` | POST insert into `users` |
| `includes/expose.php` | Comma-separated first names, `text/plain` |
| `includes/curls.php` | Local first names plus the 2017 classmate URLs |

The other files in `includes/` are HTML fragments. They expect to be included from `index.php` after the database is connected.

Login state is `$_SESSION['user']`. It is not the `users` table. The table is the Lab 3 directory. The text file is the Lab 2 password list.

Product memory is the `lastids` cookie (order of opens, with repeats) plus the `products.hits` column (global opens). "Your top viewed" counts repeats in the cookie. "Global top viewed" sorts by `hits`.

## Conventions

- New PHP uses `declare(strict_types=1)` and PSR-12. `composer lint` checks `includes/lib.php`, `includes/db.php`, `includes/dbconnect.php`, `router.php`, and `tests/`.
- The 2017 templates mix HTML and PHP and use the theme's original formatting. Run `php -l` on them. Do not reindent `css/`, `js/`, or `fonts/`.
- Escape text with `spartan_e()` when it goes into HTML.
- Query through `spartan_select`, `spartan_execute`, or `mysqli::execute_query`. Bind request values. Cast product ids with `(int)` before they reach SQL.
- Resolve files from `spartan_root()` or `__DIR__`. The working directory is not stable between Apache and `php -S`.
- Settings are `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`, and `DB_CONNECT_RETRIES`. Load them with `spartan_load_dotenv()` / `spartan_env()`. Existing process environment wins over `.env`.
- Keep the BootstrapMade credit in the footer.

## Commands

```bash
composer install
composer lint          # PSR-12 on the maintained PHP
composer test          # unit tests, no database
php tests/load-schema.php
composer test:integration

php -S localhost:8080 router.php
docker compose up --build
```

`php -d display_errors=1` is useful locally. The Docker image logs errors and does not display them.

## Gotchas

- The database password belongs in the environment. `includes/dbconnect.php` used to hardcode one; that value is still in git history. Do not put it back into the tree. A unit test fails the build if the retired string shows up in a tracked text file.
- `txt/credentials.txt` is the lab's password file, in plain text, including `admin` / `admin`. It is demo data. Do not add real passwords. `router.php` and `.htaccess` both block it over HTTP; git still contains it.
- Serve the site with `router.php` or Apache. A bare `php -S localhost:8080` will serve `txt/credentials.txt`.
- `sql/schema.sql` drops `users` and `products`. The rows are a reconstruction so the pages run, not a dump of the 2017 database. Images that are not referenced (for example `img/jamming.jpg` and `img/saleTable.jpg`) stayed in `img/` because they shipped with the original project.
- The `lastids` cookie does not record a second consecutive open of the same product. It does record that product again after you open something else. The list is capped at 100 ids.
- Personal "Hits" on the old page were easy to misread. The column for your own history is "Your views" (cookie counts). The global table is still "Hits".
- Classmate URLs in `spartan_peer_urls()` are the original `http://` hosts. They usually fail. `spartan_curl_texts()` waits about three seconds in parallel and drops HTML error pages.
- jQuery is 1.8.3 on purpose. The theme uses `$(window).load` and the old Isotope API. A newer jQuery will break the scroll and the product grid.
- `js/classie.js` is loaded and unused. It came with the theme.
- There is no CSRF protection and no password hashing. That matches the assignment. Add either one only if the task is specifically about that.
- Modal links (Login, Add, Search) use `href="#"`. The scroll handler on `index.php` has to ignore `data-toggle="modal"`, or the click throws on `$('#').offset()` and the dialog never opens.
- News markup uses the `portfolioContainer` class, and so does the product grid. Isotope selects both. That is original.
- Docker applies `DB_PASSWORD` only when the database volume is created. `docker compose down -v` is how you change it or reseed.
- `txt/contacts.txt` still has the contact lines published on the class site, including a personal email. That is page content.
- Do not add a license file. The theme's own license comment lives in `css/style.css` and `css/font-awesome.css`.
