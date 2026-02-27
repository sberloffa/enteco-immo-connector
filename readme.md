### === Enteco Immo Connector ===
Contributors: enteco \
Tags: immobilien, real estate, justimmo, onoffice, import \
Requires at least: 6.0 \
Tested up to: 6.7 \
Requires PHP: 8.1 \
Stable tag: 1.1.0 \
License: GPLv2 or later \
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Import von Immobiliendaten aus Justimmo und OnOffice nach WordPress. Internes Datenmodell: OpenImmo 1.2.7c.

## == Beschreibung ==

**Enteco Immo Connector** verbindet WordPress mit führenden Immobilien-Software-Plattformen.

**FREE-Version enthält:**

* Custom Post Types `eic_property` (Immobilien) und `eic_agent` (Makler)
* Provider-Support: Justimmo **oder** OnOffice (einer aktiv)
* **Automatischer Import einmal täglich via WP-Cron** (voreingestellt) oder manuell per Admin-Button
* Import-Modus in den Einstellungen jederzeit umschaltbar (automatisch ↔ manuell)
* Objektlimit: max. 50 Objekte (publish + draft)
* Native Field Engine (WordPress postmeta, keine 3rd-party-Abhängigkeit)
* Basisfelder: Preise, Flächen, Adresse, Geo-Koordinaten, 10 boolesche Ausstattungsmerkmale
* Titelbild wird lokal als WordPress-Attachment gespeichert
* Taxonomien: Objektart, Vermarktungsart, Nutzungsart, Zustand
* WP.org-konform: keine Lizenzprüfung, keine externen Calls außer Provider-API

**PRO-Addon (kommerziell, separates Plugin):**

* Unlimitierte Objekte
* Mehrere Provider parallel
* Webhooks für Echtzeit-Synchronisation
* Delta-Update / Änderungserkennung
* Vollständiges OpenImmo-Feldset
* ACF- und MetaBox-Field-Engine
* REST API (`eic/v1`)
* PageBuilder-Integrationen (Bricks, Elementor, Beaver, Breakdance)
* Import-Log mit History

## == Installation ==

1. Plugin-ZIP ins WordPress-Admin hochladen oder in `/wp-content/plugins/` entpacken.
2. Composer-Abhängigkeiten installieren: `composer install --no-dev --optimize-autoloader`
3. Plugin aktivieren.
4. Im Admin unter **Immo Connector → Einstellungen** Provider, Zugangsdaten und Import-Modus eintragen.
5. Bei Modus „Automatisch": der erste Import findet täglich zur Zeit der Plugin-Aktivierung statt.
6. Bei Modus „Manuell" oder sofortiger Erstimport: unter **Immo Connector → Import** den Import per Knopfdruck starten.

## == Frequently Asked Questions ==

= Welche PHP-Version wird benötigt? =
PHP 8.1 oder höher.

= Kann ich Justimmo UND OnOffice gleichzeitig nutzen? =
In der FREE-Version ist genau ein Provider aktiv. Das PRO-Addon ermöglicht mehrere Provider parallel.

= Werden Bilder lokal gespeichert? =
Das Titelbild wird immer lokal als WordPress-Attachment gespeichert. Galerie-Bilder sind eine PRO-Funktion.

= Gibt es automatische Importe? =
Ja – die FREE-Version unterstützt einen automatischen Import **einmal täglich** via WP-Cron. Der Modus (automatisch/manuell) ist in den Einstellungen wählbar. Webhooks und Delta-Updates sind PRO-Features.

= Wann genau wird der automatische Import ausgeführt? =
Das Cron-Event wird bei der Plugin-Aktivierung geplant und läuft täglich zur selben Uhrzeit. Den nächsten geplanten Zeitpunkt siehst du unter **Immo Connector → Import**.

= Kann ich trotz automatischem Modus auch manuell importieren? =
Ja. Der Button „Import jetzt starten" unter **Immo Connector → Import** funktioniert unabhängig vom eingestellten Modus jederzeit.

= Was passiert beim Deaktivieren des Plugins? =
Das Cron-Event wird entfernt. Beim Reaktivieren wird es automatisch neu geplant.

== Screenshots ==

1. Dashboard-Übersicht
2. Einstellungsseite mit Provider-Auswahl und Import-Modus
3. Import-Seite mit Modus-Anzeige und manuellem Trigger

== Changelog ==

= 1.1.0 =
* Automatischer Tagesimport via WP-Cron hinzugefügt (Standard: aktiviert)
* Import-Modus (automatisch/manuell) in den Einstellungen wählbar
* Import-Seite zeigt aktuellen Modus und nächsten geplanten Lauf

= 1.0.0 =
* Erstveröffentlichung

== Upgrade Notice ==

= 1.1.0 =
Neuer Import-Modus: automatisch täglich via WP-Cron (voreingestellt). Nach dem Plugin-Update einmal deaktivieren und reaktivieren, damit das Cron-Event geplant wird.

= 1.0.0 =
Erstveröffentlichung.
