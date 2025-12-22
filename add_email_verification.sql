-- Add email_verified column to users table
ALTER TABLE `users` 
ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `role`,
ADD COLUMN `verification_token` VARCHAR(64) NULL AFTER `email_verified`;

-- Create index for faster verification lookups
ALTER TABLE `users` 
ADD INDEX `idx_verification_token` (`verification_token`);
