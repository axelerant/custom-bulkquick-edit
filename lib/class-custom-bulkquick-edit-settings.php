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
 * Custom Bulk/Quick Edit settings class
 *
 * Based upon http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */


class Custom_Bulkquick_Edit_Settings {
	const CONFIG = '__config__';
	const ENABLE = '__enable__';
	const ID     = 'custom-bulkquick-edit-settings';
	const RESET  = '__reset__';

	private static $post_types = array();

	public static $default  = array(
		'backwards' => array(
			'version' => '', // below this version number, use std
			'std' => '',
		),
		'choices' => array(), // key => value
		'class' => '',
		'desc' => '',
		'id' => 'default_field',
		'section' => 'general',
		'std' => '', // default key or value
		'title' => '',
		'type' => 'text', // textarea, checkbox, radio, select, hidden, heading, password, expand_begin, expand_end
		'validate' => '', // required, term, slug, slugs, ids, order, single paramater PHP functions
		'has_config' => 0, // enable configuration hide for certain types
	);
	public static $defaults = array();
	public static $sections = array();
	public static $settings = array();
	public static $version  = null;


	public function __construct() {
		if ( self::do_load() )
			add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'init', array( $this, 'init' ) );

		// restrict settings page to admins only
		if ( current_user_can( 'activate_plugins' ) )
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}


	public function init() {
		load_plugin_textdomain( 'custom-bulkquick-edit', false, '/custom-bulkquick-edit/languages/' );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function do_load() {
		$do_load = false;
		if ( ! empty( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'edit.php', 'options.php', 'plugins.php' ) ) ) {
			$do_load = true;
		} elseif ( ! empty( $_REQUEST['page'] ) && self::ID == $_REQUEST['page'] ) {
			$do_load = true;
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$do_load = true;
		}

		return $do_load;
	}


	public static function sections() {
		self::$post_types = Custom_Bulkquick_Edit::get_post_types();
		foreach ( self::$post_types as $post_type => $label ) {
			self::$sections[ $post_type ] = $label;
		}

		self::$sections['reset'] = esc_html__( 'Compatibility & Reset', 'custom-bulkquick-edit' );
		self::$sections['about'] = esc_html__( 'About Custom Bulk/Quick Edit', 'custom-bulkquick-edit' );

		self::$sections = apply_filters( 'cbqe_sections', self::$sections );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function settings() {
		// General
		self::$settings['general'] = array(
			'desc' => esc_html__( 'TBD', 'custom-bulkquick-edit' ),
			'type' => 'heading',
		);

		$as_types = array(
			'' => esc_html__( 'No', 'custom-bulkquick-edit' ),
			'checkbox' => esc_html__( 'As checkbox', 'custom-bulkquick-edit' ),
			'input' => esc_html__( 'As input field', 'custom-bulkquick-edit' ),
			'radio' => esc_html__( 'As radio', 'custom-bulkquick-edit' ),
			'select' => esc_html__( 'As select', 'custom-bulkquick-edit' ),
			'textarea' => esc_html__( 'As textarea', 'custom-bulkquick-edit' ),
		);
		$as_types = apply_filters( 'cbqe_settings_as_types', $as_types );

		$as_taxonomy = array(
			'' => esc_html__( 'No', 'custom-bulkquick-edit' ),
			'show_only' => esc_html__( 'No, but enable column view', 'custom-bulkquick-edit' ),
			'categories' => esc_html__( 'Like categories', 'custom-bulkquick-edit' ),
			'taxonomy' => esc_html__( 'Like tags', 'custom-bulkquick-edit' ),
		);
		$as_taxonomy = apply_filters( 'cbqe_settings_as_taxonomy', $as_taxonomy );

		$desc_conf    = esc_html__( 'This configuration section is for option-based inputs like checkbox. You may create options formatted like "the-key|Supremely, Pretty Values" seperated by newlines.', 'custom-bulkquick-edit' );
		$desc_edit    = esc_html__( 'Force making %1$s an editable taxonomy field like checked categories or free-text tags.', 'custom-bulkquick-edit' );
		$desc_excerpt = esc_html__( 'Enable editing of %1$s\' excerpt.', 'custom-bulkquick-edit' );
		$desc_remove  = esc_html__( 'During bulk editing, easily remove all of the %1$s\' prior relationships and add new.', 'custom-bulkquick-edit' );

		$title_conf    = esc_html__( '%s Configuration', 'custom-bulkquick-edit' );
		$title_edit    = esc_html__( 'Edit "%s" taxonomy?', 'custom-bulkquick-edit' );
		$title_enable  = esc_html__( 'Enable "%s"?', 'custom-bulkquick-edit' );
		$title_excerpt = esc_html__( 'Excerpt', 'custom-bulkquick-edit' );
		$title_remove  = esc_html__( 'Reset "%s" Relations?', 'custom-bulkquick-edit' );

		foreach ( self::$post_types as $post_type => $label ) {
			$call_api = false;

			$supports_excerpt = post_type_supports( $post_type, 'excerpt' );
			if ( $supports_excerpt ) {
				self::$settings[ $post_type . self::ENABLE . 'post_excerpt' ] = array(
					'section' => $post_type,
					'title' => sprintf( $title_enable, $title_excerpt ),
					'label' => $title_excerpt,
					'desc' => sprintf( $desc_excerpt, $label ),
					'type' => 'checkbox',
				);

				$call_api = true;
			}

			$taxonomy_name = array();
			$taxonomies    = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $taxonomies as $taxonomy ) {
				$name = $taxonomy->name;
				if ( 'post_format' == $name )
					continue;

				$tax_label       = $taxonomy->label;
				$taxonomy_name[] = $name;

				self::$settings[ $post_type . self::ENABLE . $name ] = array(
					'section' => $post_type,
					'title' => sprintf( $title_edit, $tax_label ),
					'label' => $tax_label,
					'desc' => sprintf( $desc_edit, $tax_label ),
					'type' => 'select',
					'choices' => $as_taxonomy,
				);

				self::$settings[ $post_type . self::ENABLE . $name . self::RESET ] = array(
					'section' => $post_type,
					'title' => sprintf( $title_remove, $tax_label ),
					'label' => sprintf( $title_remove, $tax_label ),
					'desc' => sprintf( $desc_remove, $tax_label ),
					'type' => 'checkbox',
				);

				$call_api = true;
			}

			$filter      = 'manage_' . $post_type . '_posts_columns';
			$fields      = array();
			$fields      = apply_filters( $filter, $fields );
			$filter_edit = 'manage_edit-' . $post_type . '_columns';
			$fields      = apply_filters( $filter_edit, $fields );
			if ( ! empty( $fields ) ) {
				// don't edit these common/static fields with this plugin
				unset( $fields['author'] );
				unset( $fields['cb'] );
				unset( $fields['date'] );
				unset( $fields['id'] );
				unset( $fields['post_excerpt'] );

				$doc = new DOMDocument();

				foreach ( $fields as $field => $label ) {
					$alt   = '';
					$title = '';

					// convert img to just alt/title tag
					if ( false !== stristr( $label, '<img' ) ) {
						$doc->loadHTML( $label );

						$xpath   = new DOMXPath( $doc );
						$results = $xpath->query( '//*[@alt]' );
						foreach ( $results as $node )
							$alt = $node->getAttribute( 'alt' );

						if ( empty( $alt ) ) {
							$results = $xpath->query( '//*[@title]' );
							foreach ( $results as $node )
								$title = $node->getAttribute( 'title' );

							if ( empty( $title ) )
								unset( $fields[ $field ] );
							else
								$fields[ $field ] = $title;
						} else {
							$fields[ $field ] = $alt;
						}
					}
				}
			}

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field => $label ) {
					if ( in_array( $field, $taxonomy_name ) )
						continue;

					self::$settings[ $post_type . self::ENABLE . $field ] = array(
						'section' => $post_type,
						'title' => sprintf( $title_enable, $label ),
						'label' => $label,
						'type' => 'select',
						'choices' => $as_types,
						'has_config' => 1,
					);

					$desc_conf_tmp = apply_filters( 'cbqe_settings_config_desc', $desc_conf, $post_type, $field );

					self::$settings[ $post_type . self::ENABLE . $field . self::CONFIG ] = array(
						'section' => $post_type,
						'title' => sprintf( $title_conf, $label ),
						'label' => $label,
						'desc' => $desc_conf_tmp,
						'type' => 'textarea',
						'validate' => 'trim',
					);
				}

				$call_api = true;
			}

			self::$settings = apply_filters( 'cbqe_settings_post_type', self::$settings, $post_type, $label );

			if ( $call_api ) {
				$action = 'manage_' . $post_type . '_posts_custom_column';
				if ( ! has_action( $action ) ) {
					add_action( $action, array( 'Custom_Bulkquick_Edit', 'manage_posts_custom_column' ), 199, 2 );
				} else {
					add_action( $action, array( 'Custom_Bulkquick_Edit', 'manage_posts_custom_column_precapture' ), 1, 2 );
					add_action( $action, array( 'Custom_Bulkquick_Edit', 'manage_posts_custom_column_capture' ), 199, 2 );
				}

				add_filter( $filter, array( 'Custom_Bulkquick_Edit', 'manage_posts_columns' ), 199 );
			} else {
				self::$settings[ $post_type . '_no_options' ] = array(
					'section' => $post_type,
					'desc' => esc_html__( 'No custom fields found', 'custom-bulkquick-edit' ),
					'type' => 'heading',
				);
			}
		}

		// Reset
		$options = get_option( self::ID );
		if ( ! empty( $options ) ) {
			$serialized_options = serialize( $options );
			$_SESSION['export'] = $serialized_options;

			self::$settings['export'] = array(
				'section' => 'reset',
				'title' => esc_html__( 'Export Settings', 'custom-bulkquick-edit' ),
				'type' => 'readonly',
				'desc' => esc_html__( 'These are your current settings in a serialized format. Copy the contents to make a backup of your settings.', 'custom-bulkquick-edit' ),
				'std' => $serialized_options,
			);
		}

		self::$settings['import'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Import Settings', 'custom-bulkquick-edit' ),
			'type' => 'textarea',
			'desc' => esc_html__( 'Paste new serialized settings here to overwrite your current configuration.', 'custom-bulkquick-edit' ),
		);

		self::$settings['delete_data'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Remove Plugin Data on Deletion?', 'custom-bulkquick-edit' ),
			'type' => 'checkbox',
			'class' => 'warning', // Custom class for CSS
			'desc' => esc_html__( 'Delete all Custom Bulk/Quick Edit data and options from database on plugin deletion', 'custom-bulkquick-edit' ),
		);

		self::$settings['reset_defaults'] = array(
			'section' => 'reset',
			'title' => esc_html__( 'Reset to Defaults?', 'custom-bulkquick-edit' ),
			'type' => 'checkbox',
			'class' => 'warning', // Custom class for CSS
			'desc' => esc_html__( 'Check this box to reset options to their defaults', 'custom-bulkquick-edit' ),
		);

		self::$settings = apply_filters( 'cbqe_settings', self::$settings );

		foreach ( self::$settings as $id => $parts )
			self::$settings[ $id ] = wp_parse_args( $parts, self::$default );
	}


	public static function get_defaults( $mode = null ) {
		if ( empty( self::$defaults ) )
			self::settings();

		$do_backwards = false;
		if ( 'backwards' == $mode ) {
			$old_version = cbqe_get_option( 'version' );
			if ( ! empty( $old_version ) )
				$do_backwards = true;
		}

		foreach ( self::$settings as $id => $parts ) {
			$std = isset( $parts['std'] ) ? $parts['std'] : '';
			if ( $do_backwards ) {
				$version = ! empty( $parts['backwards']['version'] ) ? $parts['backwards']['version'] : false;
				if ( ! empty( $version ) ) {
					if ( $old_version < $version )
						$std = $parts['backwards']['std'];
				}
			}

			self::$defaults[ $id ] = $std;
		}

		return self::$defaults;
	}


	public static function get_settings() {
		if ( empty( self::$settings ) )
			self::settings();

		return self::$settings;
	}


	public function admin_init() {
		self::sections();
		self::settings();

		$version       = cbqe_get_option( 'version' );
		self::$version = Custom_Bulkquick_Edit::VERSION;
		self::$version = apply_filters( 'cbqe_version', self::$version );

		if ( $version != self::$version )
			$this->initialize_settings();

		$this->register_settings();
	}


	public function admin_menu() {
		$admin_page = add_options_page( esc_html__( 'Custom Bulk/Quick Settings', 'custom-bulkquick-edit' ), esc_html__( 'Custom Bulk/Quick', 'custom-bulkquick-edit' ), 'manage_options', self::ID, array( 'Custom_Bulkquick_Edit_Settings', 'display_page' ) );

		add_action( 'admin_print_scripts-' . $admin_page, array( $this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( $this, 'styles' ) );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function create_setting( $args = array() ) {
		extract( $args );

		if ( preg_match( '#(_expand_begin|_expand_end)#', $id ) )
			return;

		$field_args = array(
			'type' => $type,
			'id' => $id,
			'desc' => $desc,
			'std' => $std,
			'choices' => $choices,
			'label_for' => $id,
			'class' => $class,
			'has_config' => $has_config,
		);

		self::$defaults[$id] = $std;

		add_settings_field( $id, $title, array( $this, 'display_setting' ), self::ID, $section, $field_args );
	}


	public static function display_page() {
		echo '<div class="wrap">
			<div class="icon32" id="icon-options-general"></div>
			<h2>' . esc_html__( 'Custom Bulk/Quick Edit Settings', 'custom-bulkquick-edit' ) . '</h2>';

		echo '<form action="options.php" method="post">';

		settings_fields( self::ID );

		echo '<div id="' . self::ID . '">
			<ul>';

		foreach ( self::$sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';

		echo '</ul>';

		self::do_settings_sections( self::ID );

		echo '
			<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . esc_html__( 'Save Changes', 'custom-bulkquick-edit' ) . '" /></p>
			</form>
		</div>
		';


		$disable_donate = cbqe_get_option( 'disable_donate' );
		if ( ! $disable_donate ) {
			echo '<p>' .
				sprintf(
				__( 'If you like this plugin, please <a href="%1$s" title="Donate for Good Karma"><img src="%2$s" border="0" alt="Donate for Good Karma" /></a> or <a href="%3$s" title="purchase Custom Bulk/Quick Edit Premium">purchase Custom Bulk/Quick Edit Premium</a> to help fund further development and <a href="%4$s" title="Support forums">support</a>.', 'custom-bulkquick-edit' ),
				esc_url( 'http://aihr.us/about-aihrus/donate/' ),
				esc_url( 'https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' ),
				esc_url( 'http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/' ),
				esc_url( 'https://aihrus.zendesk.com/categories/20112546-Custom-Bulk-Quick-Edit' )
			) .
				'</p>';
		}

		$text = esc_html__( 'Copyright &copy;%1$s %2$s.', 'custom-bulkquick-edit' );
		$link = '<a href="http://aihr.us">Aihrus</a>';
		echo '<p class="copyright">' . sprintf( $text, date( 'Y' ), $link ) . '</p>';

		self::section_scripts();

		echo '</div>';
	}


	public static function section_scripts() {
		echo '
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$( "#' . self::ID . '" ).tabs();
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

		if ( ! isset( $wp_settings_sections ) || !isset( $wp_settings_sections[$page] ) )
			return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;

			echo '<table id=' . $section['id'] . ' class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}


	public function display_section() {}


	public function display_about_section() {
		echo '
			<div id="about" style="width: 70%; min-height: 225px;">
				<p><img class="alignright size-medium" title="Michael in Red Square, Moscow, Russia" src="' . WP_PLUGIN_URL . '/custom-bulkquick-edit/media/michael-cannon-red-square-300x2251.jpg" alt="Michael in Red Square, Moscow, Russia" width="300" height="225" /><a href="http://wordpress.org/extend/plugins/custom-bulkquick-edit/">Custom Bulk/Quick Edit Settings</a> is by <a href="http://aihr.us/about-aihrus/michael-cannon-resume/">Michael Cannon</a>. He\'s <a title="Lot\'s of stuff about Peichi Liu…" href="http://peimic.com/t/peichi-liu/">Peichi’s</a> smiling man, an adventurous <a title="Water rat" href="http://www.chinesehoroscope.org/chinese_zodiac/rat/" target="_blank">water-rat</a>, <a title="Axelerant – Open Source. Engineered." href="http://axelerant.com/who-we-are">chief people officer</a>, <a title="Road biker, cyclist, biking; whatever you call, I love to ride" href="http://peimic.com/c/biking/">cyclist</a>, <a title="Aihrus – website support made easy since 1999" href="http://aihr.us/about-aihrus/">full stack developer</a>, <a title="Michael\'s poetic like literary ramblings" href="http://peimic.com/t/poetry/">poet</a>, <a title="World Wide Opportunities on Organic Farms" href="http://peimic.com/t/WWOOF/">WWOOF’er</a> and <a title="My traveled to country list, is more than my age." href="http://peimic.com/c/travel/">world traveler</a>.</p>
			</div>
		';
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function display_setting( $args = array(), $do_echo = true, $input = null ) {
		$content = '';

		extract( $args );

		if ( is_null( $input ) ) {
			$options = get_option( self::ID );
		} else {
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
		$choices      = array_map( 'esc_attr', $choices );
		$field_class  = esc_attr( $field_class );
		$id           = esc_attr( $id );
		$options[$id] = esc_attr( $options[$id] );
		$std          = esc_attr( $std );

		switch ( $type ) {
		case 'checkbox':
			$content .= '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="' . self::ID . '[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> ';

			if ( ! empty( $desc ) )
				$content .= '<label for="' . $id . '"><span class="description">' . $desc . '</span></label>';

			break;

		case 'file':
			$content .= '<input class="regular-text' . $field_class . '" type="file" id="' . $id . '" name="' . self::ID . '[' . $id . ']" />';

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		case 'heading':
			$content .= '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
			break;

		case 'hidden':
			$content .= '<input type="hidden" id="' . $id . '" name="' . self::ID . '[' . $id . ']" value="' . $options[$id] . '" />';

			break;

		case 'password':
			$content .= '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="' . self::ID . '[' . $id . ']" value="' . $options[$id] . '" />';

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		case 'radio':
			$i             = 1;
			$count_choices = count( $choices );
			foreach ( $choices as $value => $label ) {
				$content .= '<input class="radio' . $field_class . '" type="radio" name="' . self::ID . '[' . $id . ']" id="' . $id . $i . '" value="' . $value . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';

				if ( $i < $count_choices )
					$content .= '<br />';

				$i++;
			}

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		case 'readonly':
			$content .= '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="' . self::ID . '[' . $id . ']" value="' . $options[$id] . '" readonly="readonly" />';

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		case 'select':
			$content .= '<select class="select' . $field_class . '" name="' . self::ID . '[' . $id . ']">';

			foreach ( $choices as $value => $label )
				$content .= '<option value="' . $value . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';

			$content .= '</select>';

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		case 'text':
			$content .= '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="' . self::ID . '[' . $id . ']" placeholder="' . $std . '" value="' . $options[$id] . '" />';

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		case 'textarea':
			$content .= '<textarea class="' . $field_class . '" id="' . $id . '" name="' . self::ID . '[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';

			if ( ! empty( $desc ) )
				$content .= '<br /><span class="description">' . $desc . '</span>';

			break;

		default:
			$content .= apply_filters( 'cbqe_settings_display_setting', $args, $input );
			break;
		}

		// stick config show/hide javascript here
		if ( $has_config )
			$content .= '';

		if ( ! $do_echo )
			return $content;

		echo $content;
	}


	public function initialize_settings() {
		$defaults                 = self::get_defaults( 'backwards' );
		$current                  = get_option( self::ID );
		$current                  = wp_parse_args( $current, $defaults );
		$current['admin_notices'] = cbqe_get_option( 'version', self::$version );
		$current['version']       = self::$version;

		update_option( self::ID, $current );
	}


	public function register_settings() {
		register_setting( self::ID, self::ID, array( $this, 'validate_settings' ) );

		foreach ( self::$sections as $slug => $title ) {
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( $this, 'display_about_section' ), self::ID );
			else
				add_settings_section( $slug, $title, array( $this, 'display_section' ), self::ID );
		}

		foreach ( self::$settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
	}


	public function scripts() {
		wp_enqueue_script( 'jquery-ui-tabs' );
	}


	public function styles() {
		if ( ! is_ssl() )
			wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
		else
			wp_enqueue_style( 'jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
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
			$defaults = self::get_defaults();

			if ( is_admin() ) {
				if ( ! empty( $input['reset_defaults'] ) ) {
					foreach ( $defaults as $id => $std ) {
						$input[$id] = $std;
					}

					unset( $input['reset_defaults'] );
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
			$default = $parts['std'];

			// ensure default config
			if ( strstr( $id, Custom_Bulkquick_Edit_Settings::CONFIG ) ) {
				$config = $input[ $id ];
				if ( empty( $config ) ) {
					$field = str_replace( Custom_Bulkquick_Edit_Settings::CONFIG, '', $id );
					$type  = $input[ $field ];
					switch ( $type ) {
					case 'checkbox';
						$default = '1|' . esc_html__( 'Enable', 'custom-bulkquick-edit' );
						break;

					case 'radio';
					case 'select';
						$default  = '';
						$default .= esc_html__( 'Yes', 'custom-bulkquick-edit' );
						$default .= "\n";
						$default .= 'no|' . esc_html__( 'No', 'custom-bulkquick-edit' );
						$default .= "\n";
						$default .= 'where-beef|' . esc_html__( 'Where\'s the beef?', 'custom-bulkquick-edit' );
						break;

					default:
						$default = apply_filters( 'cbqe_configuration_default', $default, $id, $type );
						break;
					}

					$input[ $id ] = $default ;
				}
			}

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

		$input['version']        = self::$version;
		$input['donate_version'] = Custom_Bulkquick_Edit::VERSION;
		$input                   = apply_filters( 'cbqe_validate_settings', $input, $errors );

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

		case 'trim':
			$options = explode( "\n", $input[ $id ] );
			foreach ( $options as $key => $value )
				$options[ $key ] = trim( $value );

			$input[ $id ] = implode( "\n", $options );
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
	 * Let values like "true, 'true', 1, and 'yes'" to be true. Else, false
	 */
	public static function is_true( $value = null, $return_boolean = true ) {
		if ( true === $value || 'true' == strtolower( $value ) || 1 == $value || 'yes' == strtolower( $value ) ) {
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


}


function cbqe_get_options() {
	$options = get_option( Custom_Bulkquick_Edit_Settings::ID );

	if ( false === $options ) {
		$options = Custom_Bulkquick_Edit_Settings::get_defaults();
		update_option( Custom_Bulkquick_Edit_Settings::ID, $options );
	}

	return $options;
}


function cbqe_get_option( $option, $default = null ) {
	$options = get_option( Custom_Bulkquick_Edit_Settings::ID );

	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return $default;
}


function cbqe_set_option( $option, $value = null ) {
	$options = get_option( Custom_Bulkquick_Edit_Settings::ID );

	if ( ! is_array( $options ) )
		$options = array();

	$options[$option] = $value;
	update_option( Custom_Bulkquick_Edit_Settings::ID, $options );
}


?>
