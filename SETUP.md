# OXYN Studios — Full Hosting Setup Guide (Hostinger)

This covers everything end to end: connecting your own domain, uploading
the site with the correct folder structure, creating the database, and
connecting email — so both the public site and the `/admin` dashboard work.

You will need: your Hostinger hPanel login, and access to wherever you
bought your domain (GoDaddy, Namecheap, etc. — wherever you manage its DNS).

---

## Part 1 — Point your domain to Hostinger

If you haven't added your domain to your Hostinger hosting plan yet:

1. In hPanel, go to **Websites**, find your hosting plan, and either:
   - Click **Add website** and enter your domain name, or
   - If a website already exists on the plan, open it and go to **Domain**.
2. hPanel will show you either **nameservers** to use, or show a
   **"Domain not connected"** notice with a **Check guide** button — click it.
   It gives you exact values to use (these are unique to your account, so
   always copy them from your own hPanel, not from a tutorial).
3. Log in to wherever you bought the domain (your registrar).
4. Find **DNS / Nameserver settings** for that domain.
5. Replace the existing nameservers with the two Hostinger gave you, and
   save.
   - *Don't want to move full DNS control to Hostinger?* Instead, find the
     **A record** for `@` at your registrar and point it to the **website
     IP address** shown in hPanel (Websites → your site → Details). This
     keeps DNS at your registrar but sends web traffic to Hostinger.
6. Wait — this can take anywhere from a few minutes up to 24 hours to fully
   spread across the internet ("DNS propagation"). hPanel shows you the
   propagation status per region.

---

## Part 2 — Upload the site with the correct folder structure

This is the one part that's different from a normal WordPress upload, so
follow it carefully. The project has two kinds of folders:

- `public/` — everything a visitor's browser is allowed to load directly
  (this becomes your website's actual root folder on the server)
- `app/`, `database/`, `storage/` — code and data that must **never** be
  directly reachable by a browser, for security

On Hostinger, the folder a browser can reach is called **`public_html`**.
So `app/`, `database/`, and `storage/` need to sit **next to** `public_html`,
not inside it.

### Steps
1. In hPanel, open **Files → File Manager** for your domain.
2. You'll land inside `public_html`. Click the **breadcrumb** (folder path
   at the top) and go up **one level**, so you're looking at a folder that
   *contains* `public_html` (it's usually named after your domain).
3. Click **Upload**, and upload the project zip file right here (not inside
   `public_html`).
4. Right-click the uploaded zip → **Extract**. You'll get a folder like
   `oxyn-php-professionalfinal32/`.
5. Open that extracted folder. Inside it you'll see `app`, `database`,
   `public`, `storage`, `SETUP.md`, etc.
6. Move things into place:
   - Delete (or rename) the existing empty `public_html` folder.
   - Rename the extracted project's `public` folder to **`public_html`**.
   - Move `app`, `database`, `storage`, `SETUP.md`, and `README.md` out of
     the extracted folder so they sit **next to** `public_html` (same
     level), not inside it.
   - Delete the now-empty `oxyn-php-professionalfinal32` wrapper folder.

Your final layout, at that one-level-up location, should look like:
```
app/
database/
storage/
public_html/   ← this is the renamed "public" folder
SETUP.md
README.md
```

Most file managers (including Hostinger's) let you **drag and drop** files
between folders, or right-click → **Move**, so this is a few clicks, not
retyping anything.

7. Visit your domain in a browser. The homepage should load (it will show
   its built-in sample content, since the database isn't connected yet).

---

## Part 3 — Create the database

1. In hPanel, go to **Databases → Management** (or search "Databases" in
   the sidebar).
2. Enter a database name and a username + password for it, then create it.
   - Hostinger automatically prefixes both with something like
     `u123456789_` — that's normal, just copy the **full** name it shows
     you, prefix included.
3. Next to your new database, click **Enter phpMyAdmin**.
4. Click the **Import** tab, choose the `database/schema.sql` file from the
   project, and click **Go** / **Import**.
   - You should see a success message, and 3 sample blog posts will
     already be in there.

---

## Part 4 — Connect the database and email (one file, no hosting settings)

Most shared hosting plans (Hostinger's PHP hosting included) don't have a
settings screen for custom environment variables — that's usually only
offered for Node.js apps. So instead, there's **one file** to fill in:

1. In File Manager, go to `app/config/`.
2. Find **`env.local.example.php`**. Right-click → **Rename** it to
   **`env.local.php`**.
3. Right-click `env.local.php` → **Edit** (this opens Hostinger's code
   editor in the browser).
4. Replace each `CHANGE_ME` with your real values:
   - The `DB_*` values are exactly what you created in Part 3.
   - The `SMTP_*` values come from an email mailbox. If you don't have one
     yet: hPanel → **Emails** → create a mailbox (e.g.
     `hello@yourdomain.com`) → open it → **Configure/Connect devices** →
     it will show the SMTP host, port, and encryption to use.
5. Click **Save**.

That's it — no restart, no hosting panel settings. The site reads this file
automatically on every page load.

**Keep this file private** — it has real passwords in it. Never attach it
to a message or upload it anywhere public.

---

## Part 5 — Create your admin account

1. Visit `yourdomain.com/admin/setup.php`.
   - If you see **"Create your admin account"**, the database connected —
     enter the email and password you want to log in with.
   - If you see **"Database not connected yet"**, double-check the values
     you typed in Part 4 (typos in the database name/password are the most
     common cause) and try again.
2. After creating the account, you're logged straight into the dashboard.
3. From now on, log in any time at `yourdomain.com/admin/login.php`.

---

## What you can do from the dashboard

| Section       | What it does |
|---------------|--------------|
| **Dashboard** | Quick overview: post count, messages, unread count |
| **Blog Posts**| Add/edit/delete articles. Each gets its own page at `/blog-post.php?slug=...` and the 3 newest show on your homepage's Insights section |
| **Videos**    | Replace the showreel, the contact-section video, and add/remove/reorder your portfolio (Featured Work) clips |
| **Testimonials** | Replace any of the 3 client video testimonials |
| **Messages**  | Everyone who has filled out your contact form, even if the email didn't arrive |
| **Account**   | Change your login email or password |

### Writing a blog post
The content box uses simple formatting instead of a complex editor:
- Start a line with `## ` for a section heading
- Start a line with `- ` for a bullet point
- Wrap text in `**like this**` to make it **bold**
- Leave a blank line between paragraphs

### Forgot your dashboard password?
Click **"Forgot your password?"** on the login page — it emails a 6-digit
code using the same SMTP details from Part 4.

### If something looks broken
The site is built to **never go down** because of the dashboard — if the
database is ever unreachable, your homepage automatically falls back to
showing its original sample content instead of an error. Only `/admin`
itself needs the database to work.

---

## Security notes
- Only one admin account is supported (created once via `/admin/setup.php`;
  after that, the page redirects to login).
- Passwords are never stored in plain text.
- After 8 failed login attempts, logins pause for 60 seconds.
- Uploaded files are checked by type and size, and renamed randomly on save.
- `app/`, `database/`, and `storage/` sitting outside `public_html` (Part 2)
  is what keeps your database file, credentials, and source code from ever
  being directly downloadable by a visitor.
