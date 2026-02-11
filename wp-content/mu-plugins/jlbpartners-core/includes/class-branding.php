<?php
/**
 * Branding Management Class
 *
 * Handles centralized branding settings for all components.
 *
 * @package JLBPartners_Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JLB Partners Core Branding class.
 */
class JLBPartners_Core_Branding {

	/**
	 * Color defaults.
	 *
	 * @var array
	 */
	private $color_defaults = array(
		'primary'                => '#00a400',
		'primary_100'            => '#77B962',
		'primary_200'            => '#A3D096',
		'primary_300'            => '#D1E7CB',
		'accent'                 => '#4AD400',
		'accent_100'             => '#97DD6E',
		'accent_200'             => '#B9E79E',
		'accent_300'             => '#DCF3CF',
		'primary_dark'           => '#004500',
		'primary_dark_100'       => '#54734A',
		'primary_dark_200'       => '#8DA186',
		'primary_dark_300'       => '#C6D0C2',
		'secondary'              => '#18387B',
		'secondary_100'          => '#586998',
		'secondary_200'          => '#8F9BBB',
		'secondary_300'          => '#C7CDDD',
		'secondary_light'        => '#00BBF2',
		'secondary_light_100'    => '#7FCAF2',
		'secondary_light_200'    => '#A9DBF5',
		'secondary_light_300'    => '#D4EDFB',
		'info'                   => '#BAE8F0',
		'info_100'               => '#D3EDF3',
		'info_200'               => '#E0F2F6',
		'info_300'               => '#F0F9FB',
		'neutral_white'          => '#FFFFFF',
		'neutral_black'          => '#000000',
		'neutral_100'            => '#404041',
		'neutral_200'            => '#7F7F80',
		'neutral_300'            => '#BFBFBF',
		'tertiary'               => '#C58E1A',
		'tertiary_100'           => '#D4AB54',
		'tertiary_200'           => '#E2C68C',
		'tertiary_300'           => '#F0E3C6',
		'neutral_warm'           => '#F0E9D6',
		'neutral_warm_100'       => '#F4EFE0',
		'neutral_warm_200'       => '#F7F4EA',
		'neutral_warm_300'       => '#FBF9F5',
		'neutral_deep'           => '#5C4937',
		'neutral_deep_100'       => '#857769',
		'neutral_deep_200'       => '#ADA49B',
		'neutral_deep_300'       => '#D6D1CD',
	);

	/**
	 * Font mapping.
	 *
	 * @var array
	 */
	private $font_map = array(
		'larken'           => array(
			'family' => 'Larken, sans-serif',
			'url'    => 'https://fonts.googleapis.com/css2?family=Larken:wght@300;400;500;600;700;800;900&display=swap',
		),
		'geist'            => array(
			'family' => 'Geist, sans-serif',
			'url'    => 'https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800;900&display=swap',
		),
		'libre-baskerville' => array(
			'family' => 'Libre Baskerville, serif',
			'url'    => 'https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&display=swap',
		),
		'archivo'          => array(
			'family' => 'Archivo, sans-serif',
			'url'    => 'https://fonts.googleapis.com/css2?family=Archivo:wght@300;400;500;600;700;800;900&display=swap',
		),
		'system'           => array(
			'family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
			'url'    => '',
		),
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_branding_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_branding_styles' ) );
	}

	/**
	 * Get color value.
	 *
	 * @param string $color_key Color key.
	 * @return string Hex color code.
	 */
	public function get_color( $color_key ) {
		$default = isset( $this->color_defaults[ $color_key ] ) ? $this->color_defaults[ $color_key ] : '';
		return get_option( 'jlbpartners_' . $color_key, $default );
	}

	/**
	 * Get font info.
	 *
	 * @param string $font_type Font type (primary, secondary, tertiary, alt).
	 * @return array Font family and URL.
	 */
	public function get_font( $font_type ) {
		$font_key = get_option( 'jlbpartners_font_' . $font_type, 'system' );
		
		if ( isset( $this->font_map[ $font_key ] ) ) {
			return $this->font_map[ $font_key ];
		}

		return $this->font_map['system'];
	}

	/**
	 * Enqueue branding styles and fonts.
	 */
	public function enqueue_branding_styles() {
		// Placeholder - color and font system disabled for now.
	}

	/**
	 * Get all colors as array.
	 *
	 * @return array All colors.
	 */
	public function get_all_colors() {
		$colors = array();
		foreach ( $this->color_defaults as $color_key => $default_value ) {
			$colors[ $color_key ] = $this->get_color( $color_key );
		}
		return $colors;
	}
}
