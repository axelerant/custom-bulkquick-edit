<?php
/**
 * Aihrus Framework
 * Copyright (C) 2015 Axelerant
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */


/**
 * Aihrus Framework settings helper class
 *
 * Based upon http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */
require_once ABSPATH . 'wp-admin/includes/template.php';

if ( class_exists( 'Aihrus_Settings' ) ) {
	return;
}


abstract class Aihrus_Settings {
	public static $default = array(
		'backwards' => array(
			'version' => null, // below this version number, use std
			'std' => null,
		),
		'choices' => array(), // key => value
		'class' => null, // warning, etc.
		'desc' => null,
		'id' => null,
		'section' => 'general',
		'show_code' => false,
		'std' => null, // default key or value
		'suggest' => false, // attempt for auto-suggest on inputs
		'title' => null,
		'type' => 'text', // textarea, checkbox, radio, select, hidden, heading, password, expand_begin, expand_end
		'validate' => null, // required, term, slug, slugs, ids, order, single paramater PHP functions
		'widget' => 1, // show in widget options, 0 off
	);

	private static $settings_saved = false;

	public static $suggest_id = 0;

	public function __construct() {}


	public static function load_options() {
		static::sections();
		static::settings();
	}


	public static function sections() {
		static::$sections['reset'] = esc_html__( 'Reset' );
		static::$sections['about'] = esc_html__( 'About' );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function settings() {
		// Reset section defaults
		$options = get_option( static::ID );
		if ( ! empty( $options ) ) {
			$serialized_options = serialize( $options );
			$_SESSION['export'] = $serialized_options;

			static::$settings['export'] = array(
				'section' => 'reset',
				'title' => esc_html__( 'Export Settings' ),
				'type' => 'readonly',
				'desc' => esc_html__( 'These are your current settings in a serialized format. Copy the contents to make a backup of your settings.' ),
				'std' => $serialized_options,
				'widget' => 0,
				'show_code' => false,
			);
		}

		static::$settings['import'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Import Settings' ),
			'type' => 'textarea',
			'desc' => esc_html__( 'Paste new serialized settings here to overwrite your current configuration.' ),
			'widget' => 0,
			'show_code' => false,
		);

		$desc = esc_html__( 'Delete all %s data and options from database on plugin deletion. Even if this option isn\'t checked, WordPress will still give a data deletion warning.' );

		static::$settings['delete_data'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Remove Plugin Data on Deletion?' ),
			'type' => 'checkbox',
			'class' => 'warning',
			'desc' => sprintf( $desc, static::NAME ),
			'widget' => 0,
			'show_code' => false,
		);

		static::$settings['reset_defaults'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Reset to Defaults?' ),
			'type' => 'checkbox',
			'class' => 'warning',
			'desc' => esc_html__( 'Check this box to reset options to their defaults.' ),
			'show_code' => false,
		);
	}


	public static function get_defaults( $mode = null, $old_version = null ) {
		if ( empty( static::$defaults ) ) {
			static::settings();
		}

		$do_backwards = false;
		if ( 'backwards' == $mode ) {
			if ( ! empty( $old_version ) ) {
				$do_backwards = true;
			}
		}

		foreach ( static::$settings as $id => $parts ) {
			$std = isset( $parts['std'] ) ? $parts['std'] : '';
			if ( $do_backwards ) {
				$version = ! empty( $parts['backwards']['version'] ) ? $parts['backwards']['version'] : false;
				if ( ! empty( $version ) ) {
					if ( $old_version < $version ) {
						$std = $parts['backwards']['std'];
					}
				}
			}

			static::$defaults[ $id ] = $std;
		}

		return static::$defaults;
	}


	public static function get_settings() {
		if ( empty( static::$settings ) ) {
			static::settings();
		}

		return static::$settings;
	}


	public static function initialize_settings( $version = null ) {
		$defaults = static::get_defaults( 'backwards' );
		$current  = get_option( static::ID );
		$current  = wp_parse_args( $current, $defaults );

		$current['admin_notices'] = $version;
		$current['version']       = static::$version;

		update_option( static::ID, $current );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function create_setting( $args = array() ) {
		extract( $args );

		if ( preg_match( '#(_expand_begin|_expand_end)#', $id ) ) {
			return;
		}

		$field_args = array(
			'choices' => $choices,
			'class' => $class,
			'desc' => $desc,
			'id' => $id,
			'label_for' => $id,
			'show_code' => $show_code,
			'std' => $std,
			'suggest' => $suggest,
			'type' => $type,
		);

		static::$defaults[ $id ] = $std;

		add_settings_field( $id, $title, array( static::$class, 'display_setting' ), static::ID, $section, $field_args );
	}


	public static function section_scripts() {
		echo '
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$( "#' . static::ID . '" ).tabs();
		// This will make the "warning" checkbox class really stand out when checked.
		$(".warning").change(function() {
			if ($(this).is(":checked"))
				$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
			else
				$(this).parent().css("background", "inherit").css("color", "inherit").css("fontWeight", "inherit");
		});
	});
</script>
';
	}


	public static function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}

			echo '<table id=' . $section['id'] . ' class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}


	public static function display_section() {}


	public static function display_about() {
		$text  = __( '<img class="size-medium" src="%5$s" alt="Axelerant 2015 Retreat in Goa" width="640" height="327" /><p>We at Axelerant have transformed ourselves from being a simple Drupal development company into a thriving incubator for products and services related to DevOps, Drupal, ecommerce, project development, release management, WordPress, and 24/7 support. Inside Axelerant, we focus on talent that’s giving, open, passionate, process oriented, and self­directed. Our clients tend to be design agencies, media publishers, and other IT organizations.</p><h2>Vision</h2><p>Axelerant, making happiness possible</p><h2>Mission</h2><p>We’re an incubator for innovative products and services created to make the world a happier place.</p><h2>Core Values</h2><ul><li><b>Passion</b> – Our passion is so strong, we’re self­directed to make the difficult easy.</li><li><b>Openness</b> – We’re so honest and painstaking in our discussions that there are no questions left, and standards are created.</li><li><b>Giving</b> – We’re excited to share our results to inspire all to surpass them.</li></ul><p>Read more about…</p><ul><li><a href="%1$s">Axelerant Team Members</a></li><li><a href="%2$s">Drupal Give</a></li><li><a href="%3$s">How We Work</a></li><li><a href="%4$s">Testimonials</a></li><li><a href="%6$s">Careers</a></li></ul>' );

		echo '<div id="about" style="width: 70%; min-height: 225px;"><p>';
		echo sprintf(
			$text,
			esc_url( '//axelerant.com/about-axelerant/axelerant-team-members/' ),
			esc_url( '//www.axelerant.com/drupalgive' ),
			esc_url( '//axelerant.com/about-axelerant/how-we-work/' ),
			esc_url( '//axelerant.com/about-axelerant/testimonials/' ),
			esc_url( '//axelerant.com/wp-content/uploads/2015/02/IGP7228-2015-01-22-at-05-18-02.jpg' ),
			esc_url( '//axelerant.com/careers/' )
		);
		echo '</p></div>';
	}


	public static function display_page( $disable_donate = false ) {
		echo '<div class="wrap">
			<div class="icon32" id="icon-options-general"></div>
			<h2>' . static::NAME . '</h2>';

		echo '<form action="options.php" method="post">';

		settings_errors( static::ID );
		settings_fields( static::ID );

		echo '<div id="' . static::ID . '">
			<ul>';

		foreach ( static::$sections as $section_slug => $section ) {
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		}

		echo '</ul>';

		self::do_settings_sections( static::ID );

		echo '
			<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . esc_html__( 'Save Changes' ) . '" /></p>
			</form>
		</div>
		';

		if ( ! $disable_donate ) {
			echo '<p>' .
				sprintf(
				__( 'If you like this plugin, please <a href="%1$s" title="Donate for Good Karma"><img src="%2$s" border="0" alt="Donate for Good Karma" /></a> or <a href="%3$s" title="purchase premium WordPress plugins from Axelerant ">purchase the Premium version</a> to help fund further development and <a href="%4$s" title="Support forums">support</a>.' ),
				esc_url( '//axelerant.com/about-axelerant/donate/' ),
				esc_url( 'https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' ),
				esc_url( '//axelerant.com/store/' ),
				esc_url( 'https://nodedesk.zendesk.com' )
			) .
				'</p>';
		}

		echo '<p class="copyright">' .
			sprintf(
			__( 'Copyright &copy;%1$s <a href="%2$s">Axlerant</a>.' ),
			date( 'Y' ),
			esc_url( '//axelerant.com' )
		) .
			'</p>';

		echo '</div>';

		add_action( 'admin_footer', array( static::$class, 'section_scripts' ) );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function display_setting( $args = array(), $do_echo = true, $input = null ) {
		$content = '';

		extract( $args );

		$maxlength   = ! empty( $maxlength ) ? 'maxlength="' . $maxlength . '"' : null;
		$placeholder = ! empty( $placeholder ) ? $placeholder : $std;

		if ( is_null( $input ) ) {
			$options = get_option( static::ID );
		} else {
			$options      = array();
			$options[ $id ] = $input;
		}

		if ( ! isset( $options[ $id ] ) && 'checkbox' != $type ) {
			$options[ $id ] = $std;
		} elseif ( ! isset( $options[ $id ] ) ) {
			$options[ $id ] = 0;
		}

		$field_class = '';
		if ( ! empty( $class ) ) {
			$field_class = ' ' . $class;
		}

		// desc isn't escaped because it's might contain allowed html
		$choices     = array_map( 'esc_attr', $choices );
		$field_class = esc_attr( $field_class );
		$id          = esc_attr( $id );
		$field_value = esc_attr( $options[ $id ] );
		$std         = esc_attr( $std );

		switch ( $type ) {
			case 'checkbox':
				$content .= '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="1" ' . checked( $field_value, 1, false ) . ' /> ';

				if ( ! empty( $desc ) ) {
					$content .= '<label for="' . $id . '"><span class="description">' . $desc . '</span></label>';
				}
				break;

			case 'file':
				$content .= '<input class="regular-text' . $field_class . '" type="file" id="' . $id . '" name="' . static::ID . '[' . $id . ']" />';

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}

				break;

			case 'heading':
				$content .= '</td></tr><tr valign="top"><td colspan="2"><h3>' . $desc . '</h3>';
				break;

			case 'hidden':
				$content .= '<input type="hidden" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="' . $field_value . '" />';

				break;

			case 'password':
				$content .= '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="' . $field_value . '" />';

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}

				break;

			case 'radio':
				$i             = 1;
				$count_choices = count( $choices );
				foreach ( $choices as $value => $label ) {
					$content .= '<input class="radio' . $field_class . '" type="radio" name="' . static::ID . '[' . $id . ']" id="' . $id . $i . '" value="' . $value . '" ' . checked( $field_value, $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';

					if ( $i < $count_choices ) {
						$content .= '<br />';
					}

					$i++;
				}

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}
				break;

			case 'readonly':
				$content .= '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="' . $field_value . '" readonly="readonly" />';

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}
				break;

			case 'rich_editor':
				$field_value = $options[ $id ];

				ob_start();
				wp_editor( $field_value, static::ID . '[' . $id . ']', array( 'textarea_name' => static::ID . '[' . $id . ']' ) );
				$content = ob_get_clean();

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}
				break;

			case 'select':
				$content .= '<select class="select' . $field_class . '" id="' . $id . '" name="' . static::ID . '[' . $id . ']">';

				foreach ( $choices as $value => $label ) {
					$content .= '<option value="' . $value . '"' . selected( $field_value, $value, false ) . '>' . $label . '</option>';
				}

				$content .= '</select>';

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}
				break;

			case 'text':
				$suggest_id = 'suggest_' . self::$suggest_id++;
				$content   .= '<input class="regular-text' . $field_class . ' ' . $suggest_id . '" type="text" id="' . $id . '" name="' . static::ID . '[' . $id . ']" placeholder="' . $placeholder . '" value="' . $field_value . '" ' . $maxlength . ' />';

				if ( ! empty( $suggest ) ) {
					$content .= static::get_suggest( $id, $suggest_id );
				}

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}
				break;

			case 'textarea':
				$content .= '<textarea class="' . $field_class . '" id="' . $id . '" name="' . static::ID . '[' . $id . ']" placeholder="' . $placeholder . '" ' . $maxlength . ' rows="5" cols="30">' . $field_value . '</textarea>';

				if ( ! empty( $desc ) ) {
					$content .= '<br /><span class="description">' . $desc . '</span>';
				}
				break;

			case 'content':
				$content .= $desc . '</td></tr>';
				break;

			default:
				break;
		}

		if ( ! $do_echo ) {
			return $content;
		}

		echo $content;
	}


	public static function register_settings() {
		register_setting( static::ID, static::ID, array( static::$class, 'validate_settings' ) );

		foreach ( static::$sections as $slug => $title ) {
			if ( 'about' == $slug ) {
				add_settings_section( $slug, $title, array( static::$class, 'display_about' ), static::ID );
			} else {
				add_settings_section( $slug, $title, array( static::$class, 'display_section' ), static::ID );
			}
		}

		foreach ( static::$settings as $id => $setting ) {
			$setting['id'] = $id;
			static::create_setting( $setting );
		}
	}


	public static function scripts() {
		wp_enqueue_script( 'jquery-ui-tabs' );
	}


	public static function styles() {
		wp_register_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'jquery-style' );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function validate_settings( $input, $options = null, $do_errors = false ) {
		$null_options = false;
		if ( is_null( $options ) ) {
			$null_options = true;

			$defaults = static::get_defaults();
			$options  = self::get_settings();

			if ( is_admin() ) {
				if ( ! empty( $input['reset_defaults'] ) ) {
					foreach ( $defaults as $id => $std ) {
						$input[ $id ] = $std;
					}

					unset( $input['reset_defaults'] );

					$input['resetted'] = true;
				}

				if ( ! empty( $input['import'] ) && $_SESSION['export'] != $input['import'] ) {
					$import       = $input['import'];
					$unserialized = unserialize( $import );
					if ( is_array( $unserialized ) ) {
						foreach ( $unserialized as $id => $std ) {
							$input[ $id ] = $std;
						}
					}
				}
			}
		}

		return self::do_validate_settings( $input, $options, $do_errors, $null_options );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function do_validate_settings( $input, $options = null, $do_errors = false, $null_options = true ) {
		$errors = array();

		foreach ( $options as $id => $parts ) {
			$default     = $parts['std'];
			$type        = $parts['type'];
			$validations = ! empty( $parts['validate'] ) ? $parts['validate'] : array();
			if ( ! empty( $validations ) ) {
				$validations = explode( ',', $validations );
			}

			if ( ! isset( $input[ $id ] ) ) {
				if ( 'checkbox' != $type ) {
					$input[ $id ] = $default;
				} else {
					$input[ $id ] = 0;
				}
			}

			if ( $default == $input[ $id ] && ! in_array( 'required', $validations ) ) {
				continue;
			}

			if ( 'checkbox' == $type ) {
				if ( self::is_true( $input[ $id ] ) ) {
					$input[ $id ] = 1;
				} else {
					$input[ $id ] = 0;
				}
			} elseif ( in_array( $type, array( 'radio', 'select' ) ) ) {
				// single choices only
				$keys = array_keys( $parts['choices'] );

				if ( ! in_array( $input[ $id ], $keys ) ) {
					if ( self::is_true( $input[ $id ] ) ) {
						$input[ $id ] = 1;
					} else {
						$input[ $id ] = 0;
					}
				}
			}

			if ( ! empty( $validations ) ) {
				foreach ( $validations as $validate ) {
					self::validators( $validate, $id, $input, $default, $errors );
				}
			}
		}

		unset( $input['export'] );
		unset( $input['import'] );

		$hide_update_notice = false;
		if ( isset( static::$hide_update_notice ) && ! empty( static::$hide_update_notice ) ) {
			$hide_update_notice = true;
		}

		if ( $null_options && empty( $errors ) && ! $hide_update_notice && ! empty( $_REQUEST['option_page'] ) ) {
			add_settings_error( static::ID, 'settings_updated', esc_html__( 'Settings saved.' ), 'updated' );

			if ( empty( self::$settings_saved ) ) {
				self::$settings_saved = true;
				set_transient( 'settings_errors', get_settings_errors(), 30 );
			}
		}

		if ( empty( $do_errors ) ) {
			$validated = $input;
		} else {
			$validated = array(
				'input' => $input,
				'errors' => $errors,
			);
		}

		return $validated;
	}


	public static function validators( $validate, $id, &$input, $default, &$errors ) {
		switch ( $validate ) {
			case 'absint':
			case 'intval':
				if ( '' !== $input[ $id ] ) {
					$input[ $id ] = $validate( $input[ $id ] );
				} else {
					$input[ $id ] = $default;
				}
				break;

			case 'email':
				$input[ $id ] = self::validate_email( $input[ $id ], $default );
				break;

			case 'ids':
				$input[ $id ] = self::validate_ids( $input[ $id ], $default );
				break;

			case 'is_true':
				$input[ $id ] = self::is_true( $input[ $id ] );
				break;

			case 'min1':
				$input[ $id ] = intval( $input[ $id ] );
				if ( 0 >= $input[ $id ] ) {
					$input[ $id ] = $default;
				}
				break;

			case 'nozero':
				$input[ $id ] = intval( $input[ $id ] );
				if ( 0 === $input[ $id ] ) {
					$input[ $id ] = $default;
				}
				break;

			case 'order':
				$input[ $id ] = self::validate_order( $input[ $id ], $default );
				break;

			case 'required':
				if ( empty( $input[ $id ] ) ) {
					$errors[ $id ] = esc_html__( 'Required' );
				}
				break;

			case 'slash_sanitize_title':
				$temp = explode( '/', $input[ $id ] );
				$temp = array_map( 'sanitize_title', $temp );
				$temp = implode( '/', $temp );

				$input[ $id ] = $temp;
				break;

			case 'slug':
				$input[ $id ] = self::validate_slug( $input[ $id ], $default );
				$input[ $id ] = strtolower( $input[ $id ] );
				break;

			case 'slugs':
				$input[ $id ] = self::validate_slugs( $input[ $id ], $default );
				$input[ $id ] = strtolower( $input[ $id ] );
				break;

			case 'term':
				$input[ $id ] = self::validate_term( $input[ $id ], $default );
				$input[ $id ] = strtolower( $input[ $id ] );
				break;

			case 'terms':
				$input[ $id ] = self::validate_terms( $input[ $id ], $default );
				break;

			case 'trim':
				$options = explode( "\n", $input[ $id ] );
				foreach ( $options as $key => $value ) {
					$options[ $key ] = trim( $value );
				}

				$input[ $id ] = implode( "\n", $options );
				break;

			case 'url':
				$input[ $id ] = self::validate_url( $input[ $id ], $default );
				break;

			default:
				$input[ $id ] = $validate( $input[ $id ] );
				break;
		}
	}


	public static function validate_ids( $input, $default = false ) {
		if ( preg_match( '#^\d+(,\s?\d+)*$#', $input ) ) {
			return preg_replace( '#\s#', '', $input );
		}

		return $default;
	}


	public static function validate_order( $input, $default = false ) {
		if ( preg_match( '#^desc|asc$#i', $input ) ) {
			return $input;
		}

		return $default;
	}


	public static function validate_slugs( $input, $default = false ) {
		if ( preg_match( '#^[\w-]+(,\s?[\w-]+)*$#', $input ) ) {
			return preg_replace( '#\s#', '', $input );
		}

		return $default;
	}


	public static function validate_slug( $input, $default = false ) {
		if ( preg_match( '#^[\w-]+$#', $input ) ) {
			return $input;
		}

		return $default;
	}


	public static function validate_term( $input, $default = false ) {
		if ( preg_match( '#^\w+$#', $input ) ) {
			return $input;
		}

		return $default;
	}


	/**
	 * Let values like true, 'true', 1, 'on', and 'yes' to be true. Else, false
	 */
	public static function is_true( $value = null, $return_boolean = true ) {
		if ( true === $value || 'true' == strtolower( $value ) || 1 == $value || 'on' == strtolower( $value ) || 'yes' == strtolower( $value ) ) {
			if ( $return_boolean ) {
				return true;
			} else {
				return 1;
			}
		} else {
			if ( $return_boolean ) {
				return false;
			} else {
				return 0;
			}
		}
	}


	public static function validate_email( $input, $default = false ) {
		if ( filter_var( $input, FILTER_VALIDATE_EMAIL ) ) {
			return $input;
		}

		return $default;
	}


	public static function validate_terms( $input, $default = false ) {
		if ( preg_match( '#^(([\w- ]+)(,\s?)?)+$#', $input ) ) {
			return preg_replace( '#,\s*$#', '', $input );
		}

		return $default;
	}


	public static function validate_url( $input, $default = false ) {
		if ( filter_var( $input, FILTER_VALIDATE_URL ) ) {
			return $input;
		}

		return $default;
	}


	public static function get_scripts() {
		foreach ( static::$scripts as $script ) {
			echo $script;
		}
	}


	public static function get_styles() {
		foreach ( static::$styles as $style ) {
			echo $style;
		}
	}


	/**
	 * Let values like false, 'false', 0, 'off', and 'no' to be true. Else, false
	 */
	public static function is_false( $value = null, $return_boolean = false ) {
		if ( false === $value || 'false' == strtolower( $value ) || 0 == $value || 'off' == strtolower( $value ) || 'no' == strtolower( $value ) ) {
			if ( $return_boolean ) {
				return true;
			} else {
				return 1;
			}
		} else {
			if ( $return_boolean ) {
				return false;
			} else {
				return 0;
			}
		}
	}


	public static function get_suggest( $id, $suggest_id ) {
		wp_enqueue_script( 'suggest' );

		switch ( $id ) {
			case 'category':
				$taxonomy = 'category';
				break;

			case 'tags':
				$taxonomy = 'post_tag';
				break;
		}

		$ajax_url   = site_url() . '/wp-admin/admin-ajax.php';
		$suggest_js = "suggest( '{$ajax_url}?action=ajax-tag-search&tax={$taxonomy}', { delay: 500, minchars: 2, multiple: true, multipleSep: ', ' } )";

		$scripts = <<<EOD
<script type="text/javascript">
jQuery(document).ready( function() {
	jQuery( '.{$suggest_id}' ).{$suggest_js};
});
</script>
EOD;

		return $scripts;
	}


	public static function get_sections() {
		if ( empty( static::$sections ) ) {
			static::sections();
		}

		return static::$sections;
	}


}


?>
