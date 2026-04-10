# Music-mood-reccomender

A full-stack music mood recommender built with PHP, MySQL, HTML, CSS, and vanilla JavaScript.

## Features

- User registration and login with PHP sessions
- Password hashing with `password_hash`
- Mood-based song recommendations fetched dynamically with `fetch`
- Built-in HTML5 audio player with play, pause, previous, and next controls
- Admin panel for adding, viewing, and deleting songs
- Responsive dark-mode UI with a modern card layout

## File Structure

- `index.php` - homepage
- `login.php` - user login
- `register.php` - user registration
- `dashboard.php` - user dashboard and mood selector
- `recommend.php` - JSON endpoint for mood-based recommendations
- `logout.php` - user logout
- `admin/admin.php` - admin login and dashboard
- `admin/add_song.php` - add new songs
- `admin/delete_song.php` - delete songs
- `admin/logout.php` - admin logout
- `admin/db/connection.php` - MySQL connection
- `assets/css/style.css` - frontend styles
- `assets/js/app.js` - recommendation and player logic
- `database/schema.sql` - MySQL schema and seed data

## Setup

1. Create the MySQL database and tables using `database/schema.sql`.
2. Configure database credentials with environment variables if needed:
	- `DB_HOST`
	- `DB_PORT`
	- `DB_NAME`
	- `DB_USER`
	- `DB_PASS`
3. Open the project in a PHP server root and visit `index.php`.
4. Create an admin user by inserting a row into `users` with `is_admin = 1`.

## Free Host Setup

This project works well on a free PHP/MySQL host such as InfinityFree.

1. In InfinityFree, create/select your hosting account and domain and confirm your web root is `htdocs`.
2. In the InfinityFree control panel, create a MySQL database and note:
   - database host
   - database name
   - database user
   - database password
3. Open phpMyAdmin from InfinityFree and import `/home/runner/work/Music-mood-reccomender/Music-mood-reccomender/database/schema.sql`.
4. Upload all project files from `/home/runner/work/Music-mood-reccomender/Music-mood-reccomender` into `htdocs` (or your host root if different).
5. Configure production database credentials:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   If environment variables are not available on your host, update fallback values in [admin/db/connection.php](admin/db/connection.php).
6. Open your public domain and test:
   - register
   - login
   - mood recommendation fetch
7. Create one admin account:
   - register a normal user first
   - run `/home/runner/work/Music-mood-reccomender/Music-mood-reccomender/database/make_admin.sql` in phpMyAdmin to promote that user (`is_admin = 1`)
   - log in at `admin/admin.php`

If your host gives you a database host name, user name, password, and database name, put those values in [admin/db/connection.php](admin/db/connection.php).

On mobile, the site should load from the public URL provided by the host, not from a local file.

## Database Tables

- `users`: stores usernames, emails, hashed passwords, and admin status
- `songs`: stores title, artist, mood, and audio URL for recommendations

## Notes

- Audio can be stored as an external URL or uploaded file.
- The app expects the database name `music_mood_recommender` by default.
