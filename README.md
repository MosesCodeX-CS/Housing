# 🏠 NyumbaFind — Kenya's Trusted Rental Marketplace

> **"Find your next home in minutes, not weeks."**
>
> NyumbaFind is a hyperlocal rental housing platform built for Kenya — connecting tenants with verified landlords and caretakers, eliminating fake listings, and making the house-hunting experience as simple as a WhatsApp message.

---

## 🧭 Vision

To become the **default housing network in Kenya** — the way Uber became the default ride — where tenants trust us to find real houses and landlords trust us to fill vacancies fast.

We start in **one estate**, dominate it completely, then expand.

---

## 🎯 The Problem We're Solving

| Pain Point | Reality in Kenya Today |
|---|---|
| Fake listings | Agents post houses that don't exist to collect "viewing fees" |
| Outdated vacancies | A house listed as available was rented 2 months ago |
| Transport waste | Tenants travel across Nairobi to view a house, only to find it gone |
| No trust signals | No way to know if a listing, caretaker, or agent is legitimate |
| Fragmented information | Listings spread across random WhatsApp groups, OLX, Facebook |

---

## 💡 What We're Building

A **full-stack rental marketplace** with:

- ✅ Verified, photo/video-backed listings
- ✅ Real-time vacancy status (vacant / occupied)
- ✅ Direct WhatsApp contact with caretakers
- ✅ Admin verification pipeline to kill fake listings
- ✅ Tenant search by estate, price, and house type
- ✅ Landlord/caretaker self-service portal
- ✅ Reviews and ratings system
- ✅ M-Pesa integration (Phase 2+)

---

## 👥 User Types

| Role | What They Do |
|---|---|
| **Tenant** | Searches for houses, views listings, contacts caretakers |
| **Landlord / Caretaker** | Uploads vacancies, updates availability, receives inquiries |
| **Agent** | Lists multiple properties, manages portfolio |
| **Admin** | Verifies listings, removes scams, manages platform health |

---

## 🗺️ Platform Architecture

```
                        INTERNET

          ┌─────────────────────────────────┐
          │         Public Web App           │
          │    (Search + Browse Listings)    │
          └───────────────┬─────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
   ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
   │ Tenant App  │ │  Landlord   │ │    Admin    │
   │  (Web/PWA)  │ │  Dashboard  │ │  Dashboard  │
   └──────┬──────┘ └──────┬──────┘ └──────┬──────┘
          └───────────────┼───────────────┘
                          ▼
             ┌────────────────────────┐
             │      Laravel API       │
             │  (Auth, Listings,      │
             │   Search, Notifs,      │
             │   Verification)        │
             └────────────┬───────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
   ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
   │  PostgreSQL │ │   Storage   │ │  3rd Party  │
   │  Database   │ │  (Media)    │ │    APIs     │
   └─────────────┘ └─────────────┘ └─────────────┘
```

---

## 🛠️ Tech Stack

### Web (Primary — Ship First)

| Layer | Technology | Why |
|---|---|---|
| **Backend API** | Laravel 11 (PHP) | Robust, fast to build, great ecosystem in Kenya dev market |
| **Database** | PostgreSQL | Relational data, great for listings + spatial queries |
| **Frontend** | Blade + Alpine.js (MVP) → React/Next.js (Scale) | Blade gets you live fast; React for SPA later |
| **Auth** | Laravel Sanctum + Phone OTP | Phone-number-first for Kenya market |
| **Media Storage** | Cloudinary or AWS S3 | Video/photo uploads for listings |
| **Maps** | Google Maps API | Estate pin-drops, location search |
| **Search** | Laravel Scout + Meilisearch | Fast full-text search by estate, price, type |
| **Notifications** | Africa's Talking (SMS) + WhatsApp Business API | Kenya-native reach |
| **Payments** | M-Pesa Daraja API (Phase 2) | Promoted listings, verified subscriptions |
| **Hosting** | Railway / DigitalOcean / Hetzner | VPS for Laravel backend |
| **Queue** | Laravel Horizon + Redis | Background jobs (media processing, notifications) |

### Mobile (Phase 2 — After Web Validation)

| Layer | Technology | Why |
|---|---|---|
| **Android** | Kotlin (Jetpack Compose) | Native Android, dominant in Kenya |
| **iOS (optional)** | Flutter | Cross-platform if iOS market justifies it |
| **API Communication** | Retrofit (Android) / Dio (Flutter) | REST API calls to Laravel backend |
| **Auth** | Firebase Phone Auth | Handles OTP cleanly on mobile |
| **Push Notifications** | Firebase Cloud Messaging (FCM) | Free, reliable |
| **Maps** | Google Maps SDK | Native map experience |
| **Offline** | Room Database (Android) | Cache recent searches |

### DevOps & Tools

| Tool | Purpose |
|---|---|
| **GitHub Actions** | CI/CD pipelines |
| **Docker** | Local development environment |
| **Laravel Telescope** | Debugging & request monitoring |
| **Sentry** | Error tracking |
| **Postman** | API documentation + testing |

---

## 📂 Project Structure

```
nyumbafind/
├── backend/                  # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Auth/
│   │   │   │   ├── ListingController.php
│   │   │   │   ├── SearchController.php
│   │   │   │   ├── InquiryController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   └── Admin/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Listing.php
│   │   │   ├── Inquiry.php
│   │   │   ├── Review.php
│   │   │   ├── Report.php
│   │   │   └── Estate.php
│   │   └── Services/
│   │       ├── MpesaService.php
│   │       ├── WhatsAppService.php
│   │       ├── SmsService.php
│   │       └── VerificationService.php
│   ├── database/
│   │   └── migrations/
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   └── tests/
│
├── frontend/                 # React / Next.js (Phase 2) or Blade views (MVP)
│   ├── pages/
│   │   ├── index.jsx         # Home / search
│   │   ├── listings/
│   │   ├── landlord/
│   │   └── admin/
│   └── components/
│
├── mobile/                   # Kotlin Android App (Phase 2)
│   └── app/
│       └── src/main/
│           ├── java/
│           │   └── ke/nyumbafind/
│           │       ├── ui/
│           │       ├── data/
│           │       ├── domain/
│           │       └── utils/
│           └── res/
│
├── docs/                     # Architecture, API docs, wireframes
│   ├── architecture.md
│   ├── api.md
│   ├── database-erd.md
│   └── user-flows.md
│
└── docker-compose.yml
```

---

## 🗃️ Core Database Schema (ERD Overview)

```
users
  id, name, phone, email, role (tenant|landlord|caretaker|agent|admin),
  verified_at, avatar, created_at

estates
  id, name, county, latitude, longitude, slug

listings
  id, user_id, estate_id, title, description, type (bedsitter|1br|2br|3br),
  price, deposit, amenities (JSON), status (vacant|occupied|pending|suspended),
  verified_at, verified_by, featured, views_count, created_at

listing_media
  id, listing_id, type (photo|video), url, is_primary, order

inquiries
  id, listing_id, tenant_id, message, status (pending|responded|closed),
  whatsapp_opened_at, created_at

reviews
  id, listing_id, user_id, rating (1-5), comment, created_at

reports
  id, listing_id, reporter_id, reason, details, status (open|resolved),
  resolved_at, resolved_by

verification_logs
  id, listing_id, admin_id, action (approved|rejected|suspended), notes, created_at
```

---

## 🔄 Core User Flows

### Tenant Flow
```
Opens app → Selects estate → Filters by price/type →
Views listing → Sees photos/video → Clicks "Chat Caretaker" →
WhatsApp opens → Views in person → Leaves review
```

### Landlord / Caretaker Flow
```
Registers → Adds listing (photos, price, amenities) →
Submitted for verification → Admin approves →
Listing goes live → Receives tenant inquiries →
Updates vacancy status when rented
```

### Admin Verification Flow
```
New listing submitted →
Admin reviews media + details →
Calls caretaker to confirm (optional) →
Approves (verified badge) or Rejects (with reason) →
Listing visible / Notified of rejection
```

### Fraud Reporting Flow
```
User clicks "Report Listing" →
Selects reason (fake / overpriced / no longer available / other) →
Admin receives alert →
Admin investigates → Suspends or clears listing
```

---

## 🚀 MVP Feature Scope (Phase 1 — Web)

### Must Have (Launch)
- [ ] Phone number registration + OTP
- [ ] Estate-based listing search
- [ ] Filter by: price range, house type, amenities
- [ ] Listing detail page with photos + map
- [ ] "Chat on WhatsApp" button
- [ ] Landlord listing upload (photos, details, price)
- [ ] Vacancy status toggle (vacant / occupied)
- [ ] Admin dashboard: verify / approve / reject listings
- [ ] Report fake listing button
- [ ] Verified badge on approved listings

### Should Have (Shortly After Launch)
- [ ] Video upload support
- [ ] Tenant saved searches (watchlist)
- [ ] Review + rating system
- [ ] SMS / WhatsApp notification on inquiry
- [ ] Email digest for landlords

### Nice to Have (Phase 2)
- [ ] M-Pesa: promoted listing payments
- [ ] AI-powered listing recommendations
- [ ] PWA installable on mobile
- [ ] Android native app (Kotlin)
- [ ] Predictive vacancy alerts

---

## 📈 Growth Strategy

### The Marketplace Flywheel
```
More landlords upload vacancies
        ↓
More real listings available
        ↓
Tenants find the platform useful
        ↓
More tenant traffic + inquiries
        ↓
Faster occupancy for landlords
        ↓
More landlords join
```

### Acquisition Channels
- **TikTok** — apartment tours, "cheapest bedsitters in Rongai" content
- **WhatsApp groups** — post estate-specific vacancies
- **Facebook estate groups** — organic posting in community groups
- **Referrals** — tenant/landlord invite rewards
- **Posters** — near matatu stages in target estates
- **Campus ambassadors** — university hostels and nearby rentals

---

## 💰 Monetization (After Growth, Not Before)

| Revenue Stream | Description |
|---|---|
| Promoted listings | Landlords pay to appear at the top of search |
| Verified landlord subscription | Monthly fee for verified badge + analytics |
| Tenant screening | Background/ID verification service |
| Moving service referrals | Commission from partner movers |
| Wi-Fi / internet partner referrals | ISP partnerships for connected houses |
| Rent collection (Phase 3) | M-Pesa rent payment processing fee |

**Rule: Do NOT charge until the marketplace has strong listing density and active daily users.**

---

## 🏁 90-Day Execution Plan

| Period | Goal | Key Activities |
|---|---|---|
| **Week 1–2** | Validate | Interview 20 tenants, 10 caretakers, 5 agents in one estate |
| **Week 2–3** | Data | Collect 50 real listings into a Google Sheet / Airtable |
| **Week 3–4** | Supply | Manually onboard landlords, walk estates |
| **Month 2** | Build | Launch MVP web app, onboard caretakers to self-upload |
| **Month 2–3** | Grow | TikTok content, WhatsApp groups, first 100 active users |
| **Month 3–4** | Retain | Track metrics, improve trust system, estate #2 |

---

## 📊 Key Metrics to Track

| Metric | What It Tells You |
|---|---|
| Active verified listings | Marketplace supply health |
| Tenant searches / day | Demand signal |
| Inquiry rate (views → WhatsApp taps) | Listing quality |
| Vacancy fill speed | Core value delivery |
| Fake listing reports | Trust health |
| Repeat landlord uploads | Retention |
| Tenant return visits | Product-market fit |

---

## 🔐 Trust & Safety System

This is our **biggest competitive moat**. Competitors can copy features — they cannot copy trust.

- **Verified badge** — only listings physically confirmed by admin
- **Last updated timestamp** — tenants know when vacancy was last confirmed
- **Video verification** — short video walkthroughs required for verified status
- **Report system** — any user can flag a listing, admin reviews within 24h
- **Scam detection** — patterns like unusually low prices trigger admin review queue
- **Listing suspension** — confirmed fake listings are suspended immediately
- **Caretaker ratings** — tenants rate responsiveness after inquiry

---

## 🌍 Expansion Roadmap

```
Phase 1: One estate (e.g. Rongai / Roysambu / Juja)
    ↓ (active listings, daily usage, trusted)
Phase 2: 3–5 nearby estates
    ↓
Phase 3: One full town / sub-county
    ↓
Phase 4: Nairobi-wide
    ↓
Phase 5: Other Kenyan cities (Mombasa, Kisumu, Nakuru, Eldoret)
    ↓
Phase 6: East Africa regional expansion
```

---

## 🧱 What Makes Us Different (The Moat)

| Factor | Us | Competitors |
|---|---|---|
| Listing verification | ✅ Physical + video verified | ❌ Self-reported only |
| Caretaker relationships | ✅ Direct, ongoing | ❌ One-time listing |
| Vacancy accuracy | ✅ Caretaker-updated in real-time | ❌ Stale, months-old |
| Estate density | ✅ Hyperlocal, exhaustive | ❌ Scattered |
| Trust signal | ✅ Verified badges + ratings | ❌ None |
| Local knowledge | ✅ Built on ground research | ❌ Generic |

---

## 📌 Contributing

This project is in early development. If you're joining the team:

1. Read `docs/architecture.md` before writing any code
2. Read `docs/api.md` for endpoint conventions
3. Check `docs/database-erd.md` for schema before creating migrations
4. All PRs must reference an issue
5. Feature branches only — never commit directly to `main`
6. Minimum: unit tests for all service classes

---

## 📁 Documentation Index

| Doc | Contents |
|---|---|
| [`docs/architecture.md`](docs/architecture.md) | Full system design, service map |
| [`docs/api.md`](docs/api.md) | API endpoints, auth, request/response examples |
| [`docs/database-erd.md`](docs/database-erd.md) | Full ERD with relationships |
| [`docs/user-flows.md`](docs/user-flows.md) | Step-by-step flows for each user type |
| [`docs/trust-system.md`](docs/trust-system.md) | Verification pipeline, fraud detection |

---

## 📞 Contact

Built in Kenya 🇰🇪 for Kenyans.

> *"The best rental platform isn't the one with the most features — it's the one people trust."*

---

*NyumbaFind — Real Houses. Real Caretakers. Real Fast.*
