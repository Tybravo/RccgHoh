# RccgHoh (RCCG Habitation of Hope Website)

Official website for RCCG Habitation of Hope International Ministry, showcasing the mission, programs, gallery, and a working contact form.

## Features

- Responsive, static site (HTML/CSS/JS)
- Image/video gallery assets
- Contact form submission via `forms/contact.php`
  - AJAX submission handled by `assets/vendor/php-email-form/validate.js`
  - SMTP sending supported via PHPMailer (`class.phpmailer.php`, `class.smtp.php`)

## Project Structure

- `index.html` — main site page
- `assets/` — template assets (CSS, JS, vendor libraries, images, videos)
- `js/` — additional JavaScript files used by the site
- `forms/`
  - `contact.php` — contact form endpoint (returns `OK` on success)
  - `mail-config.php` — SMTP configuration (do not commit real passwords)

## Requirements

- For browsing only (no contact form): any static web server or direct file open
- For contact form to work locally: a PHP server (recommended: WAMP on Windows)
- For reliable email delivery: SMTP credentials for a mailbox on your domain (cPanel Email Account)

## Run Locally (Windows + WAMP)

1. Install WampServer 64-bit: https://www.wampserver.com/en/
2. Start WAMP and wait until the tray icon turns green.
3. Copy this project folder into WAMP’s web root:
   - `C:\wamp64\www\RccgHoh\`
4. Open the site:
   - `http://localhost/RccgHoh/`

## Configure Contact Form Email (SMTP)

The contact form posts to `forms/contact.php`. For best deliverability, the project sends emails using SMTP via PHPMailer.

1. Get SMTP settings from cPanel:
   - cPanel → Email Accounts → (your mailbox) → Connect Devices / Set Up Mail Client
   - Copy Outgoing Server (SMTP) details: host, port, encryption (SSL/TLS), username, password
2. Update `forms/mail-config.php`:
   - `transport`: `smtp`
   - `host`: your SMTP hostname (often `mail.yourdomain.com` or your domain)
   - `port`: typically `465` (ssl) or `587` (tls)
   - `encryption`: `ssl` or `tls`
   - `username`: full email address (e.g. `info@yourdomain.com`)
   - `password`: mailbox password

### Notes on “From” and “Reply-To”

- Most SMTP servers require the **From email** to match the authenticated mailbox.
- The project sets:
  - From: your mailbox (configured in `mail-config.php`)
  - Reply-To: the sender’s email entered in the form
- This ensures you can reply directly to the sender while maintaining good deliverability.

## Deploy to cPanel

1. Upload the project contents into:
   - `/home/<cpanel-user>/public_html/`
2. Ensure these files exist on the server root (same level as `index.html`):
   - `class.phpmailer.php`
   - `class.smtp.php`
3. Confirm `forms/mail-config.php` is configured with correct SMTP values.
4. Test the contact form from the live site.

## Troubleshooting

- Contact form “Loading” never ends:
  - Confirm `index.html` contains `<div class="error-message"></div>` inside the form status area.
  - Check the browser Console and Network tab for the `forms/contact.php` response.
- Contact form returns an error message:
  - Re-check SMTP host/port/encryption/username/password.
  - Try switching host between `yourdomain.com` and `mail.yourdomain.com`.
  - Verify the mailbox exists and password is correct in cPanel.
- Emails go to spam:
  - Use a domain mailbox as the From address.
  - Ensure your domain has valid SPF/DKIM/DMARC in DNS (often configurable in cPanel).

## Security

- Do not commit real SMTP passwords to Git.
- Keep `forms/mail-config.php` credentials private (use environment-specific values).

## License

This repository contains website/template assets and third-party vendor files. Verify and comply with any upstream template/vendor licenses before redistribution.
