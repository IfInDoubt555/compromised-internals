# Compromised Internals

> A community-focused rally racing platform built with Laravel.

Compromised Internals is a media-rich blog and rally event hub. It’s built from the ground up with Laravel, Vite, and Tailwind CSS, and designed to connect enthusiasts, share history, promote mental health awareness, and build a vibrant digital garage for everything rally.

---

## Tech Stack

- **Framework**: Laravel 11  
- **Styling**: Tailwind CSS 3.x  
- **Build Tool**: Vite  
- **Backend**: MySQL + PHP 8.x  
- **Local Dev**: Laragon  

---

## Features

- Blog system with categories, tags, search, and comments  
- Interactive rally calendar with event pages  
- Shop page for apparel and digital items  
- Rally history archive of events, drivers, and cars  
- Support for mental health donations through sales  
- Authentication, image uploads, and full CRUD functionality  

---

## Roadmap

- [x] Add auto/manual slug generation via reusable Blade component  
- [x] Stripe/PayPal checkout integration  
- [ ] Rally driver & game profile pages  
- [ ] Community media uploads with moderation tools  
- [ ] Frontend polish and accessibility cleanup  
- [ ] Peer-to-peer rally parts marketplace  

---

## Project Vision

Compromised Internals is more than a blog — it’s a rally racing platform.  
It blends passion, purpose, and performance with a mission to support mental health causes in the motorsports community.

---

## License

All rights reserved. This repository is not open source.

---

## Local Development

```bash
# Install dependencies
composer install
npm install

# Run local server
php artisan serve

# Compile assets
npm run dev