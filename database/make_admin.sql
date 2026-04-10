-- Promote an existing registered user to admin.
-- Replace the placeholder email with the account you want to promote.
UPDATE users
SET is_admin = 1
WHERE email = 'replace-with-user-email@example.com'
LIMIT 1;
