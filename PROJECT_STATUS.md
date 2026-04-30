# Terminal Learning System — Project Status

## What's Been Built

### Core Infrastructure
- **Laravel 10** with PHP 8.1 compatibility
- **Filament v3** admin panel at `/admin` (brand: "Command Center")
- **Livewire 3** + **Alpine.js** for reactive terminal UI
- **Spatie Laravel-Permission** with teams enabled
- **PostgreSQL-ready** (currently using SQLite for development)
- **Redis-ready** via Predis (configured for cache/queue/session)

### Database Schema (12 tables)
| Table | Purpose |
|-------|---------|
| `users` | Extended with `is_child`, `date_of_birth`, `avatar`, `parent_id`, `current_team_id` |
| `teams` | Family teams — parents create, children join |
| `team_user` | Pivot: team memberships |
| `team_settings` | Per-team configuration (JSON values) |
| `guardianships` | Hard parent-child links with `is_primary` flag |
| `activities` | Polymorphic activity log (reading, assessment, achievement, xp_gain) |
| `notifications` | Team-aware notifications with UUID primary keys |
| `story_pages` | Story engine content (slug, title, content[], choices[], type) |
| `achievements` | Unlockable achievements with criteria JSON |
| `achievement_user` | Pivot with `unlocked_at` timestamp |
| `user_states` | Per-user gamification state (xp, level, choices, streak) |
| `roles/permissions` | Spatie RBAC with `team_id` support |

### RBAC Model
| Role | Permissions |
|------|-------------|
| `super-admin` | All permissions |
| `team-admin` | manage team, view team, manage children, view children, manage activities, view stats, manage account |
| `player` | play games, view stats (own), manage account (own) |

### Terminal Frontend (Livewire + Alpine.js)
- **CRT monitor effects**: scanlines, flicker, screen glow
- **Boot sequence**: BIOS-style animated boot with typing effect
- **Command processor**: `help`, `back`, `clear`, `play`, `stats`, `logout`, number navigation
- **Typing animation**: Character-by-character reveal via Alpine.js
- **Command history**: Up/Down arrow key recall
- **Side buttons**: MEDIA (left), LORE (left), USER (right), STATS (right)
- **Off-canvas panels**: Slide-in panels for stats, account, backstory, multimedia
- **Color scheme**: Amber terminal on black (`#ffb000` on `#0a0a0a`)
- **Font**: IBM Plex Mono with Courier New fallback

### Filament Admin Resources
- **Users** — CRUD with role assignment, is_child toggle
- **Teams** — CRUD with member count
- **Story Pages** — CRUD for story/lore content
- **Activities** — View activity log with type filters

### Services
- **StoryEngine** — Loads pages, injects user state, resolves choices, returns menus by role
- **XpService** — Level thresholds, XP addition with level-up events
- **AchievementService** — Criteria-based unlock checking

### Auth Flows
- **Parent Registration** — Creates account + personal team + `team-admin` role
- **Login** — Standard email/password
- **Child creation** — Available to `team-admin` members (TODO: UI implementation)

### Seeders
- `RolesAndPermissionsSeeder` — Creates roles and permissions
- `StoryContentSeeder` — Bootstraps welcome, menu-play, menu-help pages
- `DemoDataSeeder` — Demo parent + child with activities and user state

---

## File Structure

```
website/
├── app/
│   ├── Events/UserLeveledUp.php
│   ├── Filament/Admin/Resources/
│   │   ├── UserResource.php + Pages/
│   │   ├── TeamResource.php + Pages/
│   │   ├── StoryPageResource.php + Pages/
│   │   └── ActivityResource.php + Pages/
│   ├── Livewire/
│   │   ├── Terminal.php          # Main terminal component
│   │   ├── BootSequence.php      # Animated boot
│   │   ├── SideButtons.php       # Edge buttons
│   │   ├── OffCanvasPanel.php    # Slide-in panels
│   │   └── Auth/
│   │       ├── Login.php
│   │       └── ParentRegistration.php
│   ├── Models/
│   │   ├── User.php              # HasRoles, teams, children, parents
│   │   ├── Team.php              # Settings helper
│   │   ├── TeamSetting.php
│   │   ├── Guardianship.php
│   │   ├── Activity.php          # MorphTo subject
│   │   ├── Notification.php      # HasUuids
│   │   ├── StoryPage.php         # Injects user state
│   │   ├── Achievement.php
│   │   └── UserState.php         # XP, level, streak
│   ├── Services/
│   │   ├── StoryEngine.php
│   │   ├── XpService.php
│   │   └── AchievementService.php
│   └── Providers/Filament/AdminPanelProvider.php
├── database/migrations/          # 14 migrations
├── database/seeders/
│   ├── RolesAndPermissionsSeeder.php
│   ├── StoryContentSeeder.php
│   └── DemoDataSeeder.php
├── resources/
│   ├── css/terminal.css          # CRT effects, side buttons, panels
│   ├── js/app.js                 # Alpine.js terminal controller
│   └── views/
│       ├── components/layout.blade.php
│       ├── app.blade.php         # Terminal entry point
│       ├── livewire/
│       │   ├── terminal.blade.php
│       │   ├── boot-sequence.blade.php
│       │   ├── side-buttons.blade.php
│       │   ├── off-canvas-panel.blade.php
│       │   └── auth/
│       │       ├── login.blade.php
│       │       └── parent-registration.blade.php
│       └── filament/admin/logo.blade.php
└── routes/web.php
```

---

## Next Steps

1. **PostgreSQL credentials** — Update `.env` with your Postgres username/password when provided
2. **Child account creation UI** — Livewire component for parents to add children
3. **Co-parent invites** — Invite flow for secondary parents
4. **Story engine expansion** — More story pages, quiz logic, game modules
5. **Real-time features** — WebSockets for multiplayer activities (optional)
6. **Media storage** — File uploads for multimedia panel content
7. **Email notifications** — Password resets, invites, achievement unlocks

---

## Quick Start

```bash
cd /home/marshall/Code/zforce/website

# Development server
php artisan serve

# Filament admin
# Visit http://localhost:8000/admin
# Login with demo: parent@example.com / password

# Run tests (when written)
php artisan test
```

## Switching to PostgreSQL

When you provide credentials, update `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=your-host
DB_PORT=5432
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

Then run:
```bash
php artisan migrate:fresh --seed
```

## Redis

Redis is configured in `.env`:
```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```
