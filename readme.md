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
* Manueller Import per Admin-Button
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
4. Im Admin unter **Immo Connector → Einstellungen** Provider und Zugangsdaten eintragen.
5. Unter **Immo Connector → Import** den ersten Import per Knopfdruck starten.

## == Frequently Asked Questions ==

= Welche PHP-Version wird benötigt? =
PHP 8.1 oder höher.

= Kann ich Justimmo UND OnOffice gleichzeitig nutzen? =
In der FREE-Version ist genau ein Provider aktiv. Das PRO-Addon ermöglicht mehrere Provider parallel.

= Werden Bilder lokal gespeichert? =
Das Titelbild wird immer lokal als WordPress-Attachment gespeichert. Galerie-Bilder sind eine PRO-Funktion.

= Gibt es automatische Importe? =
In der FREE-Version sind nur manuelle Importe möglich. Automatische Importe (konfigurierbares Intervall, Delta-Updates) sind ein PRO-Feature.

= Wie starte ich einen Import? =
Unter **Immo Connector → Import** den Button „Import jetzt starten" klicken.

= Was passiert beim Deaktivieren des Plugins? =
Alle Daten bleiben erhalten. Beim Reaktivieren funktioniert alles wie zuvor.

== Screenshots ==

1. Dashboard-Übersicht
2. Einstellungsseite mit Provider-Auswahl
3. Import-Seite mit manuellem Trigger

== Changelog ==

= 1.1.0 =
* Extension Hooks für PRO-Addon: `eic/after_load_dependencies`, `eic/field_engines`, `eic/providers`, `eic_object_limit`
* Field Engine wird aus Option `eic_field_engine` aufgelöst (filterbar via `eic/field_engines`)
* Engine-Wahl nach Onboarding serverseitig gesperrt
* Plugin bootstrappt via `plugins_loaded` Priority 1 für korrekte Hook-Reihenfolge

= 1.0.0 =
* Erstveröffentlichung

== Upgrade Notice ==

= 1.1.0 =
Wichtige Verbesserungen für PRO-Kompatibilität. Kein Breaking Change für bestehende Installationen.

= 1.0.0 =
Erstveröffentlichung.
