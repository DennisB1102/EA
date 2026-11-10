# EA Tool – Materialverwaltung für den Sanitätsdienst

Webanwendung zur Verwaltung von Einsatzmaterial, Medikamenten, Fahrzeugbeladungen und Medizingeräten – gebaut mit Laravel 12. Ausgangspunkt ist die Beladung eines **GW-San** (Beladeliste GA-San/GW-San NRW), das System ist aber für beliebige Fahrzeuge und Lagerorte nutzbar.

## Funktionen

### Material & Lager
- **Materialstamm** mit Kategorien (farbig), Artikelnummer, Einheit und Mindestbestand
- **Lagerorte** und **Bestände** pro Lagerort, optional mit Ablaufdatum / Charge
- **Ausgabe & Rücknahme** (Checkouts) mit Zweck, offene Ausgaben auf einen Blick
- **Prüfprotokolle** (Inspections) für Material

### Medikamente
- Eigene Medikamentenübersicht mit Chargenverwaltung
- Filter für **Unterschreitung des Mindestbestands** und **Ablauf in den nächsten 30 Tagen**
- **Entnahme per QR-Code** ohne Login (passwortgeschützt), Protokollierung wer wann wie viel entnommen hat

### Fahrzeuge & Beladung
- Fahrzeuge mit Funkrufname, Kennzeichen, TÜV-Datum und Foto
- Gliederung in **Ausrüstungsmodule** (Notfallrucksack, Medizingerät, Trage, Sonstiges) mit Soll- und Ist-Beladung
- **Medizingeräte als Einzelgeräte** mit Seriennummer sowie **MTK-/STK-Fälligkeit**; Geräte lassen sich Modulen zuweisen

### NFR-Prüfprotokolle (Außenprüfung)
- Prüfung eines Moduls direkt am Fahrzeug **per QR-Code, ohne Benutzerkonto** – nur mit einem gemeinsamen Prüfpasswort
- Positionen abhaken, Ist-Mengen und Bemerkungen erfassen, **digitale Unterschrift**
- Mängel bleiben offen, bis sie im System als behoben markiert werden
- Druckansicht der Protokolle; druckbare QR-Code-Übersicht unter `public/qrcodes/nfr_qrcodes.html`

### Dashboard
- Aktive Materialien, Unterschreitungen des Mindestbestands
- Abgelaufene und bald ablaufende Bestände (30 Tage)
- Fällige MTK/STK (30 Tage), offene Mängel aus NFR-Protokollen, letzte Ausgaben

### Administration
- Benutzerverwaltung mit Rollen (**admin**, **materialwart**, **user**) über `spatie/laravel-permission`
- Einstellbares QR-Prüfpasswort
- **Aktivitätsprotokoll** aller Änderungen über `spatie/laravel-activitylog`

## Technik

| Bereich   | Verwendet |
|-----------|-----------|
| Backend   | PHP 8.2+, Laravel 12, Laravel Breeze (Auth) |
| Frontend  | Blade, Tailwind CSS, Alpine.js, Vite |
| Datenbank | SQLite (Standard), MySQL/MariaDB möglich |
| Pakete    | spatie/laravel-permission, spatie/laravel-activitylog, barryvdh/laravel-dompdf |

## Installation

Voraussetzungen: PHP 8.2+, Composer, Node.js mit npm.

```bash
git clone https://github.com/DennisB1102/EA.git
cd EA
composer run setup
```

`composer run setup` installiert die Abhängigkeiten, legt die `.env` an, erzeugt den App-Key, führt die Migrationen aus und baut die Assets.

Danach Rollen und Admin-Konto anlegen und den Storage-Link für Fahrzeugbilder setzen:

```bash
php artisan db:seed
php artisan storage:link
```

Optional die Beispielbeladung eines GW-San einspielen (setzt ein Fahrzeug mit der ID 1 voraus):

```bash
php artisan db:seed --class=GwSanBeladungSeeder
```

### Entwicklung starten

```bash
composer run dev
```

Startet Webserver, Queue-Worker, Log-Ausgabe (Pail) und Vite parallel. Die App läuft dann unter http://localhost:8000.

### Tests

```bash
composer run test
```

## Konfiguration

Wichtige Werte in der `.env`:

| Variable          | Bedeutung |
|-------------------|-----------|
| `APP_NAME`        | Anzeigename, z. B. `"EA Tool"` |
| `APP_URL`         | Basis-URL – wird für die QR-Code-Links gebraucht |
| `APP_LOCALE`      | für deutsche Oberfläche `de` |
| `DB_CONNECTION`   | `sqlite` oder `mysql` |
| `NFR_QR_PASSWORD` | Start-Passwort für QR-Prüfung und Medikamentenentnahme (kann im Admin-Bereich überschrieben werden) |

## Erster Login & Sicherheit

Der Seeder legt einen Admin an: `admin@ea.local` – das Start-Passwort steht in `database/seeders/AdminUserSeeder.php`.

> **Vor dem produktiven Einsatz unbedingt:**
> - Admin-Passwort nach dem ersten Login ändern
> - `NFR_QR_PASSWORD` setzen bzw. unter *Admin → Einstellungen* ein eigenes QR-Passwort vergeben (sonst gilt der Standardwert aus `config/app.php`)
> - `APP_ENV=production` und `APP_DEBUG=false` setzen

## Deployment auf Shared Hosting

Die `.htaccess` im Projektstamm leitet alle Anfragen nach `public/` um. Damit kann die App auch in einem Unterordner (aktuell `/EA/`) betrieben werden, ohne den Document Root zu ändern. Bei einem anderen Ordnernamen die Pfadangabe in der `.htaccess` anpassen. Besser ist – wenn möglich – den Document Root direkt auf `public/` zu setzen.

## Rollen

| Rolle          | Rechte |
|----------------|--------|
| `admin`        | alles, inkl. Benutzer, Einstellungen, Aktivitätsprotokoll |
| `materialwart` | Material anlegen/bearbeiten, Bestände pflegen, Ausgaben, Prüfungen, Berichte |
| `user`         | Material und Bestände ansehen, Ausgaben/Rückgaben buchen |

## Projektstruktur (Auszug)

```
app/Http/Controllers/     Controller (Material, Medikamente, Fahrzeuge, NFR, QR …)
app/Models/               Eloquent-Modelle
database/migrations/      Datenbankschema
database/seeders/         Rollen, Admin, GW-San-Beladung
resources/views/          Blade-Templates
references/               Beladeliste GW-San NRW, Quartalscheck-Vorlage
public/qrcodes/           druckbare QR-Code-Übersicht
```

## Lizenz

Das Laravel-Grundgerüst steht unter der MIT-Lizenz. Für dieses Projekt ist noch keine eigene Lizenz festgelegt.
