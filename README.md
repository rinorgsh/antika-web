# Antika — Site web

Site vitrine du restaurant **Antika Molenveld** (Zemst, Belgique) — cuisine albanaise & méditerranéenne, lounge, bar et événements.

Refonte moderne : design _dark luxe_, carrousel d'accueil, carte trilingue, page événements avec simulateur de devis et réservation en ligne ZenChef.

## Stack

- **Laravel 13** (PHP 8.3)
- **Inertia 2** + **Vue 3**
- **Tailwind CSS 3** + **Vite**
- Trilingue **NL / FR / EN** (i18n maison via fichiers `lang/`)

## Fonctionnalités

- Carrousel d'accueil plein écran (défilement auto, Ken Burns, swipe mobile)
- Carte complète par catégories avec photos (entrées, pâtes, grill, mer, spécialités albanaises, accompagnements, burgers, enfants, Sofra)
- Page **Événements & location de salle** + lien vers le simulateur de devis
- Réservation en ligne **ZenChef** (widget SDK)
- Page contact avec horaires et carte Google Maps
- Sélecteur de langue NL / FR / EN
- 100 % responsive

## Installation locale

```bash
# Dépendances
composer install
npm install

# Environnement
cp .env.example .env
php artisan key:generate

# Base de données (SQLite par défaut)
touch database/database.sqlite
php artisan migrate

# Lancer (back + front)
composer run dev
```

Le site est alors accessible sur http://localhost:8000.

## Configuration

Les coordonnées, liens externes (ZenChef, Takeaway, simulateur), images du carrousel et chiffres clés sont centralisés dans **`config/antika.php`**.

Variables `.env` optionnelles :

```
ANTIKA_RESERVE_URL=
ANTIKA_TAKEAWAY_URL=
ANTIKA_SIMULATOR_URL=
ANTIKA_INSTAGRAM_URL=
ANTIKA_FACEBOOK_URL=
```

## Contenu

- **Traductions** : `lang/{nl,fr,en}/site.php` (textes, carte, descriptions).
- **Images** : `public/images/` (`hero/`, `dishes/`, `menu/`, `events/`).

## Structure (front)

```
resources/js/
├── Components/   (Eyebrow, HeroCarousel, MenuCategory, LanguageSwitcher…)
├── Layouts/      (AppLayout — header + footer)
├── Pages/        (Home, Menu, Events, Contact)
├── i18n.js       (helper $t)
└── reveal.js     (animations au scroll)
```
