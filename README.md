# SignageCloud – Digital Signage SaaS Platform

Cloud-based digital signage management for restaurants, cafes, and retail. Manage all your TV screens remotely from one dashboard.

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Apache with `mod_rewrite` enabled
- (Optional) PHPMailer via Composer for SMTP email

## Installation

### Shared Hosting (cPanel)

1. Upload all files to your `public_html` directory (or a subdomain folder)
2. Create a MySQL database and user in cPanel
3. Visit `https://yourdomain.com/install` to run the web installer
4. Follow the 6-step setup wizard:
   - Test database connection
   - Create tables
   - Create admin account
   - Configure SMTP email
   - Add Stripe API keys
   - Finish & lock installer

### VPS / Dedicated Server

```bash
# Clone or upload files
cd /var/www/html

# Set permissions
chmod 755 storage/
chmod 755 public/uploads/

# Run installer
# Visit https://yourdomain.com/install in your browser
```

## Directory Structure

```
signagecloud/
├── app/
│   ├── controllers/     # All controllers
│   ├── helpers/         # Core, Database, Helpers, Services, StorageService
│   ├── models/          # User, Channel, Media, Slide, Plan, Subscription...
│   └── views/           # All Blade-style PHP templates
│       ├── layouts/     # default, auth, admin, marketing, installer
│       ├── auth/        # login, register, forgot-password, reset-password
│       ├── dashboard/   # index, profile
│       ├── channels/    # index, create, show, edit
│       ├── media/       # index
│       ├── billing/     # index
│       ├── support/     # index, create, show
│       ├── display/     # player, offline, empty (no layout — raw HTML)
│       ├── admin/       # dashboard, customers, plans, tickets, revenue, settings
│       ├── marketing/   # home, features, pricing, industries, faq, contact
│       ├── errors/      # 404
│       └── installer/   # index
├── config/
│   └── app.php          # Constants, error reporting, timezone
├── install/
│   └── schema.sql       # Full database schema + seed data
├── public/
│   ├── index.php        # Front controller + full route table
│   ├── .htaccess        # mod_rewrite rules
│   └── uploads/         # Local file storage (or use S3/R2)
├── storage/
│   └── logs/            # PHP error logs
├── .htaccess            # Root redirect to public/
├── .env                 # Written by installer (DB credentials)
└── install/install.lock # Written on completion, blocks re-install
```

## Features

- **Multi-channel management** – One channel per TV screen
- **Drag & drop slides** – Upload images, drag to reorder
- **Smooth transitions** – Fade, Slide, Zoom, Crossfade
- **Auto refresh** – Screens reload content automatically (5–60 min)
- **Stripe billing** – Checkout Sessions, Billing Portal, Webhooks
- **Storage drivers** – Local disk, AWS S3, or Cloudflare R2
- **Web installer** – No command-line setup required
- **Admin panel** – Manage customers, plans, tickets, revenue, settings
- **Support tickets** – Built-in customer support system
- **QR code generator** – One per channel for easy TV setup

## Plans (Default)

| Plan    | Screens | Price (AUD/mo) |
|---------|---------|----------------|
| Starter | 1       | $75            |
| Growth  | 3       | $99            |
| Pro     | 10      | $119           |

Edit plans at Admin → Plans after installation.

## Stripe Setup

1. Create products/prices in your Stripe dashboard
2. Copy the Price IDs (e.g. `price_xxxxx`) into Admin → Plans for each plan
3. Set your webhook endpoint in Stripe to: `https://yourdomain.com/billing/webhook`
4. Events to enable: `checkout.session.completed`, `customer.subscription.updated`, `customer.subscription.deleted`, `invoice.payment_succeeded`, `invoice.payment_failed`

## Storage Drivers

Configure at Admin → Settings → Storage.

**Local** – Files stored in `public/uploads/`. Simple, no extra config.

**AWS S3** – Requires Bucket, Region, Access Key, Secret Key, and public CDN URL.

**Cloudflare R2** – Requires Bucket, Account ID, Access Key, Secret Key, and public R2.dev URL.

## Security

- CSRF tokens on all forms
- XSS prevention via `Helpers::e()` everywhere
- SQL injection prevention via PDO prepared statements
- bcrypt password hashing
- Rate limiting on login and registration
- Session-based auth with secure session names
- Install lock file prevents re-running installer

## License

Commercial use. All rights reserved.
