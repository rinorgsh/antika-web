# Centralisation Antika — mise en production

Tout est désormais servi par ce site (antikaresto.com) avec un seul admin (`/admin`) :

| Avant | Maintenant |
|---|---|
| Carte QR sur GitHub Pages (`menu.antika-resto.ovh/menu.pdf/`), publiée par commit | `antikaresto.com/carte/`, publiée instantanément depuis l'admin |
| Simulateur sur `baba-event.on-forge.com/simulateur` + 2e admin | `antikaresto.com/events/simulator`, admin « Location de salle » |
| `menu-antika.on-forge.com` (envoi de PDF) | supprimé (inutile depuis l'admin Carte) |
| — | Pages d'annonces `/events/wedding`, `/birthday`, `/communion`, `/corporate` |
| — | Google Analytics 4 / Ads + bandeau cookies, provenance (gclid) enregistrée sur chaque devis |

Les anciennes adresses restent valables :
- `menu.antika-resto.ovh/menu.pdf/…` → redirige vers `antikaresto.com/carte/…` (QR imprimés) ;
- `baba-event.on-forge.com/*` → redirige vers `antikaresto.com/events/simulator` (variable `MOVED_TO`).

## Ordre des opérations

### 0. Base de données dédiée (avant tout déploiement)
Le site partageait la base `laravel` avec d'autres sites du serveur. Les nouvelles tables
(`customers`, `quotes`, `venues`…) y risqueraient une collision. On copie la base vers une base propre :

```bash
ssh forge@174.138.7.51
mysqldump -u forge -p laravel > ~/db-backups/laravel-avant-antika-$(date +%F).sql
mysql -u forge -p -e "CREATE DATABASE antika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u forge -p antika < ~/db-backups/laravel-avant-antika-$(date +%F).sql
```
Puis, dans Forge > antika > Environment : `DB_DATABASE=antika`.

### 1. Déployer antika-website
Variables à ajouter (Forge > Environment) : voir `.env.example` (ANTIKA_EVENTS_NOTIFY, LEGACY_SIM_DB_DATABASE, MAIL_*).
Le script de déploiement doit contenir `php artisan migrate --force`, puis `php artisan images:thumbs`
(miniatures WebP du simulateur, servies ensuite directement par nginx).

### 2. Reprendre les données de l'ancien simulateur
```bash
cd /home/forge/antika.on-forge.com/current
php artisan events:import-legacy \
  --images=/home/forge/baba-event.on-forge.com/current/public \
  --storage=/home/forge/baba-event.on-forge.com/current/storage/app/public \
  --with-users
```
Lecture seule sur l'ancienne base. Relançable avec `--force` (écrase les tables du simulateur).

### 3. Publier la carte une première fois
Admin > Carte restaurant > « Publier la carte ». (Avant cela, la copie GitHub du 13/09 est servie.)

### 4. Basculer le domaine du QR code
1. Forge > antika > Domains : ajouter l'alias `menu.antika-resto.ovh`.
2. OVH > antika-resto.ovh > Zone DNS : remplacer le CNAME `menu → antika-resto.github.io.` par
   `menu  A  174.138.7.51` (baisser le TTL la veille si possible).
3. Quand `dig +short menu.antika-resto.ovh` renvoie 174.138.7.51 : Forge > SSL > Let's Encrypt
   en incluant `antikaresto.com, www.antikaresto.com, menu.antika-resto.ovh`.
4. Tester avec un téléphone : scanner le QR imprimé.

À faire hors service (le matin) : entre le changement DNS et le certificat, le QR peut afficher
un avertissement HTTPS quelques minutes.

### 5. Rediriger l'ancien simulateur
Déployer baba-event (routes de redirection), puis Forge > baba-event > Environment : `MOVED_TO=https://antikaresto.com`.

### 6. Ménage (une fois tout vérifié)
- Forge : supprimer le site `menu-antika.on-forge.com`.
- GitHub `antika-resto/menu` : Settings > Pages > désactiver, puis archiver le dépôt.
- GitHub : révoquer le jeton `ANTIKA_GITHUB_TOKEN` (plus utilisé), le retirer du .env.
- Garder `baba-event.on-forge.com` quelques mois pour les redirections, puis le supprimer.

### 7. Google Ads
1. Créer la propriété GA4 et le compte Ads, puis les conversions :
   « Demande de devis » (principale, valeur dynamique), « Demande de rappel » (principale),
   « Clic téléphone » (principale), « Clic WhatsApp / e-mail » (secondaire).
   Activer les conversions améliorées (Enhanced conversions, via gtag) dans Google Ads.
2. Renseigner `ANTIKA_GA4_ID`, `ANTIKA_GADS_ID`, `ANTIKA_GADS_QUOTE_LABEL`, `ANTIKA_GADS_CALL_LABEL`,
   `ANTIKA_GADS_LEAD_LABEL`, `ANTIKA_GADS_CONTACT_LABEL`.
3. Annonces (le site est en néerlandais par défaut) : `/events/venue-hire`, `/events/wedding`, `/events/birthday`,
   `/events/communion`, `/events/corporate`, `/events/funeral` ; ajouter `?lang=fr` pour les campagnes FR.
4. Admin > Demandes de devis : colonne « Source » = Ads pour les demandes venues d'une annonce.

## À compléter dans l'admin
- Réglages devis > « Site : confiance et capacité » : capacité maximale, note et nombre d'avis Google, lien des avis,
  2–3 avis mis en avant, numéro WhatsApp.
- Traductions NL / EN du catalogue (types, salles, formules, plats, boissons, extras) : reprises en FR.
- Réglages devis : URL des conditions générales et de la politique de confidentialité (sinon case de consentement simple).
- Vérifier le taux de TVA (21 % par défaut) et les textes des pages d'annonces (`lang/*/landing.php`).
