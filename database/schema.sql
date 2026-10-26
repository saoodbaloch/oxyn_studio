-- OXYN Studios — admin dashboard database schema
--
-- HOW TO USE THIS FILE (see SETUP.md for full screenshots/steps):
--   1. In cPanel, create a MySQL database and a database user, and add
--      that user to the database with "All Privileges".
--   2. Open phpMyAdmin, select your new (empty) database.
--   3. Click "Import", choose this file, click "Go".
--   4. Set the DB_HOST / DB_NAME / DB_USER / DB_PASSWORD environment
--      variables on your hosting to match what you created in step 1.
--   5. Visit yoursite.com/admin — because admin_users is still empty,
--      you'll land on a one-time "Create your admin account" screen.
--
-- Safe to re-run: every statement only creates a table if it does not
-- already exist, so importing this twice will not erase your content.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    reset_code_hash VARCHAR(255) NULL DEFAULT NULL,
    reset_expires_at DATETIME NULL DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(220) NOT NULL UNIQUE,
    title VARCHAR(220) NOT NULL,
    excerpt VARCHAR(400) NOT NULL DEFAULT '',
    content MEDIUMTEXT NOT NULL,
    cover_image VARCHAR(255) NOT NULL DEFAULT '',
    category VARCHAR(80) NOT NULL DEFAULT 'Video Editing',
    author_name VARCHAR(120) NOT NULL DEFAULT 'OXYN Studios',
    author_avatar VARCHAR(255) NOT NULL DEFAULT '',
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    published_at DATETIME NULL DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    service VARCHAR(120) NOT NULL DEFAULT '',
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Starter blog posts so the site looks the same as before the dashboard
-- existed. Edit or delete these from the dashboard any time.
INSERT IGNORE INTO blog_posts (slug, title, excerpt, content, cover_image, category, author_name, is_published, published_at) VALUES
('best-way-to-reduce-sharpness-1', 'Best Way to Reduce Sharpness', 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to ...', 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to walk through the exact settings that make handheld and action-cam shots look clean on every screen size.\n\n## Why aliasing happens\n\nAliasing shows up when fine detail in a shot outruns your export''s resolution and shutter settings. Action cameras are especially prone to it because of their wide lenses and high frame rates.\n\n- Match your shutter angle to your frame rate\n- Apply a light sharpen pass only after downscaling, never before\n- Export at the platform''s native resolution instead of letting it rescale for you\n\n## The quick fix\n\nA small amount of gaussian softening before your sharpen filter often looks cleaner than skipping sharpening altogether.', 'images/blog/blog-01.gif', 'Video Editing', 'OXYN Studios', 1, '2026-06-17 09:00:00'),
('best-way-to-reduce-sharpness-2', 'Best Way to Reduce Sharpness', 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to ...', 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to walk through the exact settings that make handheld and action-cam shots look clean on every screen size.\n\n## Why aliasing happens\n\nAliasing shows up when fine detail in a shot outruns your export''s resolution and shutter settings.\n\n- Match your shutter angle to your frame rate\n- Apply a light sharpen pass only after downscaling\n- Export at the platform''s native resolution', 'images/blog/blog-02.gif', 'Video Editing', 'OXYN Studios', 1, '2026-06-16 09:00:00'),
('best-way-to-reduce-sharpness-3', 'Best Way to Reduce Sharpness', 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to ...', 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to walk through the exact settings that make handheld and action-cam shots look clean on every screen size.\n\n## Why aliasing happens\n\nAliasing shows up when fine detail in a shot outruns your export''s resolution and shutter settings.\n\n- Match your shutter angle to your frame rate\n- Apply a light sharpen pass only after downscaling\n- Export at the platform''s native resolution', 'images/blog/blog-03.jpg', 'Video Editing', 'OXYN Studios', 1, '2026-06-15 09:00:00');
