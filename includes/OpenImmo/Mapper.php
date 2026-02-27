<?php
/**
 * Maps provider-specific raw data into the normalized OpenImmo schema.
 * Every provider client produces raw arrays; this class normalizes them.
 */

declare( strict_types=1 );

namespace Enteco\ImmoConnector\OpenImmo;

class Mapper {

	// ─────────────────────────────────────────────────────────────────────────
	// Justimmo
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * @param array<string, mixed> $raw
	 * @return array<string, mixed>
	 */
	public static function from_justimmo( array $raw ): array {
		$n = Schema::empty_property();

		$n['api_source']      = 'justimmo';
		$n['api_source_id']   = (string) ( $raw['id'] ?? '' );
		$n['objektnr_extern'] = (string) ( $raw['number'] ?? '' );
		$n['title']           = sanitize_text_field( (string) ( $raw['title'] ?? '' ) );
		$n['description']     = wp_kses_post( (string) ( $raw['description'] ?? '' ) );

		$n['vermarktungsart'] = isset( $raw['forRent'] ) && $raw['forRent'] ? 'miete' : 'kauf';
		$n['objektart']       = sanitize_text_field( (string) ( $raw['realtyType']['label'] ?? '' ) );

		$n['kaufpreis']  = self::to_float( $raw['purchasePrice'] ?? null );
		$n['kaltmiete']  = self::to_float( $raw['rentNet'] ?? null );
		$n['warmmiete']  = self::to_float( $raw['rentGross'] ?? null );
		$n['waehrung']   = sanitize_text_field( (string) ( $raw['currency'] ?? 'EUR' ) );

		$n['wohnflaeche']         = self::to_float( $raw['livingArea'] ?? null );
		$n['nutzflaeche']         = self::to_float( $raw['usableArea'] ?? null );
		$n['grundstuecksflaeche'] = self::to_float( $raw['plotArea'] ?? null );
		$n['anzahl_zimmer']       = self::to_float( $raw['rooms'] ?? null );

		$addr             = $raw['address'] ?? [];
		$n['strasse']     = sanitize_text_field( (string) ( $addr['street'] ?? '' ) );
		$n['hausnummer']  = sanitize_text_field( (string) ( $addr['buildingNumber'] ?? '' ) );
		$n['plz']         = sanitize_text_field( (string) ( $addr['zipCode'] ?? '' ) );
		$n['ort']         = sanitize_text_field( (string) ( $addr['city'] ?? '' ) );
		$n['land']        = sanitize_text_field( (string) ( $addr['country'] ?? '' ) );
		$n['lat']         = self::to_float( $addr['latitude'] ?? null );
		$n['lng']         = self::to_float( $addr['longitude'] ?? null );

		$f = $raw['features'] ?? [];
		$n['features']['balkon']        = (bool) ( $f['balcony'] ?? false );
		$n['features']['garage']        = (bool) ( $f['garage'] ?? false );
		$n['features']['garten']        = (bool) ( $f['garden'] ?? false );
		$n['features']['aufzug']        = (bool) ( $f['lift'] ?? false );
		$n['features']['barrierefrei']  = (bool) ( $f['accessible'] ?? false );
		$n['features']['keller']        = (bool) ( $f['cellar'] ?? false );
		$n['features']['einbaukueche']  = (bool) ( $f['builtInKitchen'] ?? false );
		$n['features']['stellplatz']    = (bool) ( $f['parkingSpace'] ?? false );
		$n['features']['terrasse']      = (bool) ( $f['terrace'] ?? false );
		$n['features']['haustiere']     = (bool) ( $f['petsAllowed'] ?? false );

		$n['titelbild_url']       = esc_url_raw( (string) ( $raw['primaryImageUrl'] ?? '' ) );
		$n['agent_api_source']    = 'justimmo';
		$n['agent_api_source_id'] = (string) ( $raw['agent']['id'] ?? '' );

		return $n;
	}

	/**
	 * @param array<string, mixed> $raw
	 * @return array<string, mixed>
	 */
	public static function agent_from_justimmo( array $raw ): array {
		$n = Schema::empty_agent();

		$n['api_source']    = 'justimmo';
		$n['api_source_id'] = (string) ( $raw['id'] ?? '' );
		$n['name']          = sanitize_text_field( trim(
			( $raw['firstName'] ?? '' ) . ' ' . ( $raw['lastName'] ?? '' )
		) );
		$n['email']         = sanitize_email( (string) ( $raw['email'] ?? '' ) );
		$n['telefon']       = sanitize_text_field( (string) ( $raw['phone'] ?? '' ) );

		return $n;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// OnOffice
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * @param array<string, mixed> $raw
	 * @return array<string, mixed>
	 */
	public static function from_onoffice( array $raw ): array {
		$n = Schema::empty_property();
		// onOffice wraps field values under 'elements'
		$e = $raw['elements'] ?? $raw;

		$n['api_source']      = 'onoffice';
		$n['api_source_id']   = (string) ( $e['Id'] ?? $e['id'] ?? '' );
		$n['objektnr_extern'] = sanitize_text_field( (string) ( $e['objektnr_extern'] ?? '' ) );
		$n['title']           = sanitize_text_field( (string) ( $e['objekttitel'] ?? '' ) );
		$n['description']     = wp_kses_post( (string) ( $e['freitext_objektbeschreibung'] ?? '' ) );

		$vma = strtolower( (string) ( $e['vermarktungsart'] ?? '' ) );
		$n['vermarktungsart'] = str_contains( $vma, 'miete' ) ? 'miete' : 'kauf';
		$n['objektart']       = sanitize_text_field( (string) ( $e['objektart'] ?? '' ) );
		$n['nutzungsart']     = sanitize_text_field( (string) ( $e['nutzungsart'] ?? '' ) );
		$n['zustand']         = sanitize_text_field( (string) ( $e['zustand'] ?? '' ) );

		$n['kaufpreis']  = self::to_float( $e['kaufpreis'] ?? null );
		$n['kaltmiete']  = self::to_float( $e['kaltmiete'] ?? null );
		$n['warmmiete']  = self::to_float( $e['warmmiete'] ?? null );
		$n['waehrung']   = sanitize_text_field( (string) ( $e['waehrung'] ?? 'EUR' ) );

		$n['wohnflaeche']         = self::to_float( $e['wohnflaeche'] ?? null );
		$n['nutzflaeche']         = self::to_float( $e['nutzflaeche'] ?? null );
		$n['grundstuecksflaeche'] = self::to_float( $e['grundstuecksflaeche'] ?? null );
		$n['anzahl_zimmer']       = self::to_float( $e['anzahl_zimmer'] ?? null );

		$n['strasse']    = sanitize_text_field( (string) ( $e['strasse'] ?? '' ) );
		$n['hausnummer'] = sanitize_text_field( (string) ( $e['hausnummer'] ?? '' ) );
		$n['plz']        = sanitize_text_field( (string) ( $e['plz'] ?? '' ) );
		$n['ort']        = sanitize_text_field( (string) ( $e['ort'] ?? '' ) );
		$n['land']       = sanitize_text_field( (string) ( $e['land'] ?? '' ) );
		$n['lat']        = self::to_float( $e['breitengrad'] ?? $e['lat'] ?? null );
		$n['lng']        = self::to_float( $e['laengengrad'] ?? $e['lng'] ?? null );

		$n['features']['balkon']        = (bool) ( $e['balkon'] ?? false );
		$n['features']['garage']        = (bool) ( $e['garage'] ?? false );
		$n['features']['garten']        = (bool) ( $e['garten'] ?? false );
		$n['features']['aufzug']        = (bool) ( $e['fahrstuhl'] ?? false );
		$n['features']['barrierefrei']  = (bool) ( $e['barrierefrei'] ?? false );
		$n['features']['keller']        = (bool) ( $e['keller'] ?? false );
		$n['features']['einbaukueche']  = (bool) ( $e['einbaukueche'] ?? false );
		$n['features']['stellplatz']    = ! empty( $e['stellplatz_art'] );
		$n['features']['terrasse']      = (bool) ( $e['terrasse'] ?? false );
		$n['features']['haustiere']     = (bool) ( $e['haustiere'] ?? false );

		$n['titelbild_url'] = esc_url_raw( (string) ( $e['titelbild'] ?? '' ) );

		return $n;
	}

	/**
	 * @param array<string, mixed> $raw
	 * @return array<string, mixed>
	 */
	public static function agent_from_onoffice( array $raw ): array {
		$n = Schema::empty_agent();
		$e = $raw['elements'] ?? $raw;

		$n['api_source']    = 'onoffice';
		$n['api_source_id'] = (string) ( $e['KdNr'] ?? $e['id'] ?? '' );
		$n['name']          = sanitize_text_field(
			trim( ( $e['Vorname'] ?? '' ) . ' ' . ( $e['Name'] ?? '' ) )
		);
		$n['email']         = sanitize_email( (string) ( $e['Email'] ?? '' ) );
		$n['telefon']       = sanitize_text_field( (string) ( $e['Telefon1'] ?? '' ) );

		return $n;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// Helpers
	// ─────────────────────────────────────────────────────────────────────────

	private static function to_float( mixed $value ): ?float {
		if ( $value === null || $value === '' ) {
			return null;
		}
		return (float) str_replace( ',', '.', (string) $value );
	}
}
