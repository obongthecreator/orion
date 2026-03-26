# Orion Inventory Plugin

A WordPress plugin for managing inventory, sales, repairs, and finances for an electronics/tech store.

---

## Installation

1. Download **`orion-inventory.zip`** from the root of this repository (click the file, then **Download raw file**).
2. In WordPress go to **Plugins → Add New → Upload Plugin**, choose the zip, and click **Install Now**.
3. **Activate** the plugin.
4. Go to **Settings → Permalinks** and click **Save Changes** (this flushes the rewrite rules so the URLs below work).

---

## Page URLs

All plugin pages live under the `/orion/` prefix. Replace `https://yoursite.com` with your WordPress site URL.

> **No shortcodes are needed.** The plugin registers its own rewrite rules, so the pages are available at these URLs immediately after activation.

### Public page

| Page | URL |
|------|-----|
| Login | `https://yoursite.com/orion/login` |

### Authenticated pages (login required)

| Page | URL |
|------|-----|
| Home / Dashboard | `https://yoursite.com/orion/home` |
| Sales | `https://yoursite.com/orion/sales` |
| Sales History | `https://yoursite.com/orion/sales-history` |
| Repairs | `https://yoursite.com/orion/repairs` |
| Repairs History | `https://yoursite.com/orion/repairs-history` |
| Credit Sales | `https://yoursite.com/orion/credit-sales` |
| Credit History | `https://yoursite.com/orion/credit-history` |
| Import | `https://yoursite.com/orion/import` |
| Import History | `https://yoursite.com/orion/import-history` |
| Stock | `https://yoursite.com/orion/stock` |
| Stock History | `https://yoursite.com/orion/stock-history` |
| Product Summary | `https://yoursite.com/orion/product-summary` |
| Financial Summary | `https://yoursite.com/orion/financial-summary` |
| Financial History | `https://yoursite.com/orion/financial-history` |
| Profile | `https://yoursite.com/orion/profile` |

### Admin-only pages (admin or super_admin role required)

| Page | URL |
|------|-----|
| Analytics | `https://yoursite.com/orion/analytics` |
| Admin Panel | `https://yoursite.com/orion/admin-panel` |

---

## REST API base URL

All AJAX calls from the templates go through the WordPress REST API:

```
https://yoursite.com/wp-json/orion/v1/
```

---

## First-time setup

After activating the plugin, visit `/orion/login`. The plugin automatically creates a default **super_admin** account on first activation:

- **Username:** `admin`
- **Password:** `admin123`

> Change the password immediately after your first login via the **Profile** page (`/orion/profile`).