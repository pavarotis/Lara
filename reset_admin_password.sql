-- Reset password for admin@larashop.test
-- New password will be: password
-- This uses Laravel's bcrypt hash for 'password'

UPDATE `users` 
SET `password` = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE `email` = 'admin@larashop.test';

-- Note: The hash above is for the password 'password'
-- If you want a different password, you'll need to generate a new bcrypt hash
