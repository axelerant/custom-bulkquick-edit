<?php
/*
	Copyright 2013 Michael Cannon (email: mc@aihr.us)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Aihrus Framework settings helper class
 *
 * Based upon http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */


if ( class_exists( 'Aihrus_Settings' ) )
	return;


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


	public function __construct() {}


	public static function load_options() {
		static::sections();
		static::settings();
	}


	public static function sections() {
		static::$sections['reset'] = esc_html__( 'Reset', 'custom-bulkquick-edit' );
		static::$sections['about'] = esc_html__( 'About', 'custom-bulkquick-edit' );
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
				'title' => esc_html__( 'Export Settings', 'custom-bulkquick-edit' ),
				'type' => 'readonly',
				'desc' => esc_html__( 'These are your current settings in a serialized format. Copy the contents to make a backup of your settings.', 'custom-bulkquick-edit' ),
				'std' => $serialized_options,
				'widget' => 0,
			);
		}

		static::$settings['import'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Import Settings', 'custom-bulkquick-edit' ),
			'type' => 'textarea',
			'desc' => esc_html__( 'Paste new serialized settings here to overwrite your current configuration.', 'custom-bulkquick-edit' ),
			'widget' => 0,
		);

		$desc = esc_html__( 'Delete all %s data and options from database on plugin deletion', 'custom-bulkquick-edit' );

		static::$settings['delete_data'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Remove Plugin Data on Deletion?', 'custom-bulkquick-edit' ),
			'type' => 'checkbox',
			'class' => 'warning',
			'desc' => sprintf( $desc, static::NAME ),
			'widget' => 0,
		);

		static::$settings['reset_defaults'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Reset to Defaults?', 'custom-bulkquick-edit' ),
			'type' => 'checkbox',
			'class' => 'warning',
			'desc' => esc_html__( 'Check this box to reset options to their defaults', 'custom-bulkquick-edit' ),
		);
	}


	public static function get_defaults( $mode = null, $old_version = null ) {
		if ( empty( static::$defaults ) )
			static::settings();

		$do_backwards = false;
		if ( 'backwards' == $mode ) {
			if ( ! empty( $old_version ) )
				$do_backwards = true;
		}

		foreach ( static::$settings as $id => $parts ) {
			$std = isset( $parts['std'] ) ? $parts['std'] : '';
			if ( $do_backwards ) {
				$version = ! empty( $parts['backwards']['version'] ) ? $parts['backwards']['version'] : false;
				if ( ! empty( $version ) ) {
					if ( $old_version < $version )
						$std = $parts['backwards']['std'];
				}
			}

			static::$defaults[ $id ] = $std;
		}

		return static::$defaults;
	}


	public static function get_settings() {
		if ( empty( static::$settings ) )
			static::settings();

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

		if ( preg_match( '#(_expand_begin|_expand_end)#', $id ) )
			return;

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

		static::$defaults[$id] = $std;

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

		if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[$page] ) )
			return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[$page] ) || ! isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;

			echo '<table id=' . $section['id'] . ' class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}


	public static function display_section() {}


	public static function display_about() {
		$name = str_replace( ' Settings', '', static::NAME );
		$text = __( '<img class="alignright size-medium" src="%1$s/media/michael-cannon-red-square-300x2251.jpg" alt="Michael in Red Square, Moscow, Russia" width="300" height="225" /><a href="%2$s">%3$s</a> is by <a href="%4$s">Michael Cannon</a>. He\'s <a href="%5$s">Peichi’s</a> smiling man, an adventurous <a href="%6$s" target="_blank">water-rat</a>, <a href="%7$s">chief people officer</a>, <a href="%8$s">cyclist</a>, <a href="%9$s">full stack developer</a>, <a href="%10$s">poet</a>, <a href="%11$s">WWOOF’er</a> and <a href="%12$s">world traveler</a>.', 'custom-bulkquick-edit' );

		echo '<div id="about" style="width: 70%; min-height: 225px;"><p>';
		echo sprintf(
			$text,
			static::$plugin_path,
			esc_url( static::$plugin_url ),
			$name,
			esc_url( 'http://aihr.us/resume/' ),
			esc_url( 'http://peimic.com/t/peichi-liu/' ),
			esc_url( 'http://www.chinesehoroscope.org/chinese_zodiac/rat/' ),
			esc_url( 'http://axelerant.com/who-we-are' ),
			esc_url( 'http://peimic.com/c/biking/' ),
			esc_url( 'http://aihr.us/about-aihrus/' ),
			esc_url( 'http://peimic.com/t/poetry/' ),
			esc_url( 'http://peimic.com/t/WWOOF/' ),
			esc_url( 'http://peimic.com/c/travel/' )
		);
		echo '</p></div>';
	}


	public static function display_page( $disable_donate = false ) {
		echo '<div class="wrap">
			<div class="icon32" id="icon-options-general"></div>
			<h2>' . static::NAME . '</h2>';

		echo '<form action="options.php" method="post">';

		settings_fields( static::ID );

		echo '<div id="' . static::ID . '">
			<ul>';

		foreach ( static::$sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';

		echo '</ul>';

		self::do_settings_sections( static::ID );

		echo '
			<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . esc_html__( 'Save Changes', 'custom-bulkquick-edit' ) . '" /></p>
			</form>
		</div>
		';

		if ( ! $disable_donate ) {
			echo '<p>' .
				sprintf(
				__( 'If you like this plugin, please <a href="%1$s" title="Donate for Good Karma"><img src="%2$s" border="0" alt="Donate for Good Karma" /></a> or <a href="%3$s" title="purchase premium WordPress plugins from Aihrus ">purchase the Premium version</a> to help fund further development and <a href="%4$s" title="Support forums">support</a>.', 'custom-bulkquick-edit' ),
				esc_url( 'http://aihr.us/about-aihrus/donate/' ),
				esc_url( 'https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' ),
				esc_url( 'http://aihr.us/store/' ),
				esc_url( 'https://aihrus.zendesk.com/home' )
			) .
				'</p>';
		}

		echo '<p class="copyright">' .
			sprintf(
			__( 'Copyright &copy;%1$s <a href="%2$s">Aihrus</a>.', 'custom-bulkquick-edit' ),
			date( 'Y' ),
			esc_url( 'http://aihr.us' )
		) .
			'</p>';

		echo '</div>';

		add_action( 'admin_footer', array( static::$class, 'section_scripts' ) );
	}


	public static function display_setting( $args = array(), $do_echo = true, $input = null ) {
		$content = '';

		extract( $args );

		if ( is_null( $input ) )
			$options = get_option( static::ID );
		else {
			$options      = array();
			$options[$id] = $input;
		}

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$field_class = '';
		if ( ! empty( $class ) )
			$field_class = ' ' . $class;

		// desc isn't escaped because it's might contain allowed html
		$choices     = array_map( 'esc_attr', $choices );
		$field_class = esc_attr( $field_class );
		$id          = esc_attr( $id );
		$field_value = esc_attr( $options[$id] );
		$std         = esc_attr( $std );

		switch ( $type ) {
			case 'checkbox':
				$content .= '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="1" ' . checked( $field_value, 1, false ) . ' /> ';

				if ( ! empty( $desc ) )
					$content .= '<label for="' . $id . '"><span class="description">' . $desc . '</span></label>';

				if ( $show_code )
					$content .= '<br /><code>' . $id . '</code>';
				break;

			case 'file':
				$content .= '<input class="regular-text' . $field_class . '" type="file" id="' . $id . '" name="' . static::ID . '[' . $id . ']" />';

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'heading':
				$content .= '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
				break;

			case 'hidden':
				$content .= '<input type="hidden" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="' . $field_value . '" />';

				break;

			case 'password':
				$content .= '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="' . $field_value . '" />';

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'radio':
				$i             = 1;
				$count_choices = count( $choices );
				foreach ( $choices as $value => $label ) {
					$content .= '<input class="radio' . $field_class . '" type="radio" name="' . static::ID . '[' . $id . ']" id="' . $id . $i . '" value="' . $value . '" ' . checked( $field_value, $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';

					if ( $i < $count_choices )
						$content .= '<br />';

					$i++;
				}

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				if ( $show_code )
					$content .= '<br /><code>' . $id . '</code>';
				break;

			case 'readonly':
				$content .= '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="' . static::ID . '[' . $id . ']" value="' . $field_value . '" readonly="readonly" />';

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'select':
				$content .= '<select class="select' . $field_class . '" id="' . $id . '" name="' . static::ID . '[' . $id . ']">';

				foreach ( $choices as $value => $label )
					$content .= '<option value="' . $value . '"' . selected( $field_value, $value, false ) . '>' . $label . '</option>';

				$content .= '</select>';

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				if ( $show_code )
					$content .= '<br /><code>' . $id . '</code>';
				break;

			case 'text':
				$content .= '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="' . static::ID . '[' . $id . ']" placeholder="' . $std . '" value="' . $field_value . '" />';

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				if ( $show_code )
					$content .= '<br /><code>' . $id . '</code>';
				break;

			case 'textarea':
				$content .= '<textarea class="' . $field_class . '" id="' . $id . '" name="' . static::ID . '[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . $field_value . '</textarea>';

				if ( ! empty( $desc ) )
					$content .= '<br /><span class="description">' . $desc . '</span>';

				if ( $show_code )
					$content .= '<br /><code>' . $id . '</code>';
				break;

			default:
				break;
		}

		if ( ! $do_echo )
			return $content;

		echo $content;
	}


	public static function register_settings() {
		register_setting( static::ID, static::ID, array( static::$class, 'validate_settings' ) );

		foreach ( static::$sections as $slug => $title ) {
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( static::$class, 'display_about' ), static::ID );
			else
				add_settings_section( $slug, $title, array( static::$class, 'display_section' ), static::ID );
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
		wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function validate_settings( $input, $options = null, $do_errors = false ) {
		$errors = array();

		if ( is_null( $options ) ) {
			$options  = self::get_settings();
			$defaults = static::get_defaults();

			if ( is_admin() ) {
				if ( ! empty( $input['reset_defaults'] ) ) {
					foreach ( $defaults as $id => $std )
						$input[$id] = $std;

					unset( $input['reset_defaults'] );

					$input['resetted'] = true;
				}

				if ( ! empty( $input['import'] ) && $_SESSION['export'] != $input['import'] ) {
					$import       = $input['import'];
					$unserialized = unserialize( $import );
					if ( is_array( $unserialized ) ) {
						foreach ( $unserialized as $id => $std )
							$input[$id] = $std;
					}
				}
			}
		}

		foreach ( $options as $id => $parts ) {
			$default     = $parts['std'];
			$type        = $parts['type'];
			$validations = ! empty( $parts['validate'] ) ? $parts['validate'] : array();
			if ( ! empty( $validations ) )
				$validations = explode( ',', $validations );

			if ( ! isset( $input[ $id ] ) ) {
				if ( 'checkbox' != $type )
					$input[ $id ] = $default;
				else
					$input[ $id ] = 0;
			}

			if ( $default == $input[ $id ] && ! in_array( 'required', $validations ) )
				continue;

			if ( 'checkbox' == $type ) {
				if ( self::is_true( $input[ $id ] ) )
					$input[ $id ] = 1;
				else
					$input[ $id ] = 0;
			} elseif ( in_array( $type, array( 'radio', 'select' ) ) ) {
				// single choices only
				$keys = array_keys( $parts['choices'] );

				if ( ! in_array( $input[ $id ], $keys ) ) {
					if ( self::is_true( $input[ $id ] ) )
						$input[ $id ] = 1;
					else
						$input[ $id ] = 0;
				}
			}

			if ( ! empty( $validations ) ) {
				foreach ( $validations as $validate )
					self::validators( $validate, $id, $input, $default, $errors );
			}
		}

		unset( $input['export'] );
		unset( $input['import'] );

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
				if ( '' !== $input[ $id ] )
					$input[ $id ] = $validate( $input[ $id ] );
				else
					$input[ $id ] = $default;
				break;

			case 'ids':
				$input[ $id ] = self::validate_ids( $input[ $id ], $default );
				break;

			case 'is_true':
				$input[ $id ] = self::is_true( $input[ $id ] );
				break;

			case 'min1':
				$input[ $id ] = intval( $input[ $id ] );
				if ( 0 >= $input[ $id ] )
					$input[ $id ] = $default;
				break;

			case 'nozero':
				$input[ $id ] = intval( $input[ $id ] );
				if ( 0 === $input[ $id ] )
					$input[ $id ] = $default;
				break;

			case 'order':
				$input[ $id ] = self::validate_order( $input[ $id ], $default );
				break;

			case 'required':
				if ( empty( $input[ $id ] ) )
					$errors[ $id ] = esc_html__( 'Required', 'custom-bulkquick-edit' );
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
				foreach ( $options as $key => $value )
					$options[ $key ] = trim( $value );

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


	public static function validate_ids( $input, $default ) {
		if ( preg_match( '#^\d+(,\s?\d+)*$#', $input ) )
			return preg_replace( '#\s#', '', $input );

		return $default;
	}


	public static function validate_order( $input, $default ) {
		if ( preg_match( '#^desc|asc$#i', $input ) )
			return $input;

		return $default;
	}


	public static function validate_slugs( $input, $default ) {
		if ( preg_match( '#^[\w-]+(,\s?[\w-]+)*$#', $input ) )
			return preg_replace( '#\s#', '', $input );

		return $default;
	}


	public static function validate_slug( $input, $default ) {
		if ( preg_match( '#^[\w-]+$#', $input ) )
			return $input;

		return $default;
	}


	public static function validate_term( $input, $default ) {
		if ( preg_match( '#^\w+$#', $input ) )
			return $input;

		return $default;
	}


	/**
	 * Let values like true, 'true', 1, 'on', and 'yes' to be true. Else, false
	 */
	public static function is_true( $value = null, $return_boolean = true ) {
		if ( true === $value || 'true' == strtolower( $value ) || 1 == $value || 'on' == strtolower( $value ) || 'yes' == strtolower( $value ) ) {
			if ( $return_boolean )
				return true;
			else
				return 1;
		} else {
			if ( $return_boolean )
				return false;
			else
				return 0;
		}
	}


	public static function validate_terms( $input, $default ) {
		if ( preg_match( '#^(([\w- ]+)(,\s?)?)+$#', $input ) )
			return preg_replace( '#,\s*$#', '', $input );

		return $default;
	}


	public static function validate_url( $input, $default ) {
		if ( filter_var( $input, FILTER_VALIDATE_URL ) )
			return $input;

		return $default;
	}


	public static function get_scripts() {
		foreach ( static::$scripts as $script )
			echo $script;
	}


	public static function get_styles() {
		foreach ( static::$styles as $style )
			echo $style;
	}


}


?>
