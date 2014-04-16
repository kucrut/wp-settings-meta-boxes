<?php

/**
 * Plugin Name: Settings Page Meta Boxes Demo
 * Description: Demonstration of Settings Page Meta Boxes plugin.
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * Version: 0.1.0
 * License: GPL v2
 * Text Domain: settings-meta-boxes-demo
 * Depends: Settings Page Meta Boxes
 */

class Kucrut_Settings_Meta_Boxes_Demo {

	/**
	 * Holds option key
	 *
	 * @since  0.1.0
	 * @var    string
	 * @access protected
	 */
	protected static $key = 'settings-meta-boxes-demo';

	/**
	 * Holds Settings page hook name
	 *
	 * @since  0.1.0
	 * @var    string
	 * @access protected
	 */
	protected static $hook;

	/**
	 * Holds Settings page title
	 *
	 * @since  0.1.0
	 * @var    string
	 * @access protected
	 */
	protected static $title;

	/**
	 * Holds Meta Boxes instance
	 *
	 * @since  0.1.0
	 * @var    object
	 * @access protected
	 */
	protected static $meta_boxes;


	/**
	 * Initialize settings page
	 *
	 * @since   0.1.0
	 * @wp_hook action init
	 */
	public static function init() {
		if ( ! class_exists( 'Kucrut_Settings_Meta_Boxes' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( plugin_basename( __FILE__ ) );

			add_action( 'admin_notices', array( __CLASS__, '_admin_notice' ) );

			return;
		}

		add_action( 'admin_menu', array( __CLASS__, '_register_menu' ) );
		add_action( 'plugin_row_meta', array( __CLASS__, '_settings_link' ), 10, 3 );
		add_action( 'admin_init', array( __CLASS__, '_register_setting' ) );
		add_action( 'admin_init', array( __CLASS__, '_register_meta_boxes' ) );

		self::$title = __( 'Settings Page Meta Boxes Demo', 'settings-meta-boxes-demo' );
	}


	public static function _admin_notice() {
		?>
		<div class="error">
			<p><?php printf(
				esc_html__( 'Please activate %s before activating %s.', 'settings-meta-boxes' ),
				'<strong>Settings Page Meta Boxes</strong>',
				'<strong>Settings Page Meta Boxes Demo</strong>'
			) ?></p>
		</div>
		<?php
	}


	/**
	 * Get value of a multidimensional array
	 *
	 * @since  0.1.0
	 * @param  array $array Array to search
	 * @param  array $keys  Keys to search
	 * @return mixed
	 */
	public static function get_array_value_deep( Array $array, Array $keys ) {
		foreach ( $keys as $idx => $key ) {
			unset( $keys[ $idx ] );

			if ( ! isset( $array[ $key ] ) ) {
				return false;
			}

			if ( count( $keys ) ) {
				$array = $array[ $key ];
			}
		}

		if ( ! isset( $array[ $key ] ) ) {
			return false;
		}

		return $array[ $key ];
	}


	/**
	 * Get current value of our settings page
	 *
	 * @since  0.1.0
	 * @param  string $field, Field id
	 * @return mixed
	 */
	public static function get_option() {
		$values = get_option( self::$key, array() );

		if ( empty( $values ) || 1 > func_num_args() ) {
			return false;
		}

		return self::get_array_value_deep( $values, func_get_args() );
	}


	/**
	 * Register menu for our settings page
	 *
	 * @since   0.1.0
	 * @wp_hook action admin_menu
	 */
	public static function _register_menu() {
		self::$hook = add_options_page(
			self::$title,
			__( 'Meta Boxes', 'settings-meta-boxes-demo' ),
			'manage_options',
			'settings-meta-boxes-demo',
			array( __CLASS__, '_page' )
		);
	}


	/**
	 * Add Link to settings page on plugins list table
	 *
	 * @since   0.1.0
	 * @wp_hook filter plugin_row_meta
	 * @param   array  $plugin_meta Plugin meta
	 * @param   string $plugin_file Plugin file
	 * @param   array  $plugin_data Plugin data
	 * @return  array
	 */
	public static function _settings_link( $plugin_meta, $plugin_file, $plugin_data ) {
		if ( $plugin_file === plugin_basename( __FILE__ ) ) {
			$plugin_meta[] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url( menu_page_url( self::$key, false ) ),
				esc_attr__( 'Go to settings page', 'settings-meta-boxes-demo' ),
				esc_html__( 'Settings', 'settings-meta-boxes-demo' )
			);
		}

		return $plugin_meta;
	}


	/**
	 * Register our option key
	 *
	 * @since   0.1.0
	 * @wp_hook action admin_init
	 */
	public static function _register_setting() {
		register_setting( self::$key, self::$key, array( __CLASS__, '_validate' ) );
	}


	/**
	 * Register meta boxes for each of our setting sections
	 *
	 * @since   0.1.0
	 * @wp_hook action admin_init
	 */
	public static function _register_meta_boxes() {
		if ( defined('DOING_AJAX') && DOING_AJAX ) {
			return;
		}

		self::$meta_boxes = new Kucrut_Settings_Meta_Boxes(
			array(
				'hook'            => self::$hook,
				'default_columns' => 1,
				'max_columns'     => 2,
			)
		);

		add_action( 'add_meta_boxes_' . self::$hook, array( __CLASS__, '_add_meta_boxes' ) );
	}


	/**
	 * Add meta boxes for each of our setting sections
	 *
	 * @since   0.1.0
	 * @wp_hook action add_meta_boxes_*
	 */
	public static function _add_meta_boxes( $args ) {
		$sections = array(
			array(
				'id'          => 'one',
				'title'       => __( 'Section #1', 'settings-meta-boxes-demo' ),
				'description' => __( 'Some description about section #1', 'settings-meta-boxes-demo' ),
				'context'     => 'normal',
				'priority'    => 'default',
				'fields'      => array(
					array(
						'id'          => 'text',
						'type'        => 'text',
						'label'       => __( 'Text Field', 'settings-meta-boxes-demo' ),
						'description' => __( 'Some description about this field.', 'settings-meta-boxes-demo' ),
					),
					array(
						'id'    => 'textarea',
						'type'  => 'textarea',
						'label' => __( 'Textarea Field', 'settings-meta-boxes-demo' )
					),
				),
			),
			array(
				'id'          => 'two',
				'title'       => __( 'Section #2', 'settings-meta-boxes-demo' ),
				'description' => __( 'Some description about section #2', 'settings-meta-boxes-demo' ),
				'context'     => 'normal',
				'priority'    => 'default',
				'fields'      => array(
					array(
						'id'    => 'text2',
						'type'  => 'text',
						'label' => __( 'Text Field #2', 'settings-meta-boxes-demo' )
					),
					array(
						'id'    => 'textarea2',
						'type'  => 'textarea',
						'label' => __( 'Textarea Field #2', 'settings-meta-boxes-demo' )
					),
				),
			),
		);

		foreach ( $sections as $section ) {
			add_meta_box(
				$section['id'],
				$section['title'],
				array( __CLASS__, '_meta_box' ),
				self::$hook,
				$section['context'],
				$section['priority'],
				$section
			);
		}
	}


	/**
	 * Get field's id attribute
	 *
	 * @since  0.1.0
	 * @param  array $field Field array
	 * @access protected
	 * @return string
	 */
	protected static function _get_field_id( Array $field ) {
		return sprintf( '_field-%s-%s', self::$key, $field['id'] );
	}


	/**
	 * Get field's name attribute
	 *
	 * @since  0.1.0
	 * @param  array $field    Field array
	 * @param  bool  $multiple Whether or not this field is multiple (select, checkbox, etc)
	 * @access protected
	 * @return string
	 */
	protected static function _get_field_name( Array $field, $multiple = false ) {
		$format = '%s[%s]';
		if ( true === $multiple ) {
			$format .= '[]';
		}

		return sprintf( $format, self::$key, $field['id'] );
	}


	/**
	 * Display field
	 *
	 * @since  0.1.0
	 * @param  array $field Field array
	 * @param  mixed $value Current field value
	 * @access protected
	 */
	protected static function _the_field( Array $field, $value = '' ) {
		$_id   = self::_get_field_id( $field );
		$_name = self::_get_field_name( $field );

		switch ( $field['type'] ) {
			case 'textarea' :
				printf(
					'<textarea id="%s" name="%s" class="widefat">%s</textarea>',
					esc_attr( $_id ),
					esc_attr( $_name ),
					esc_textarea( $value )
				);
			break;

			default :
				printf(
					'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" />',
					esc_attr( $field['type'] ),
					esc_attr( $_id ),
					esc_attr( $_name ),
					esc_attr( $value )
				);
			break;
		}

		if ( ! empty( $field['description'] ) ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $field['description'] )
			);
		}
	}


	/**
	 * Meta box content
	 *
	 * @since  0.1.0
	 * @param  array $args
	 * @param  array $box
	 */
	public static function _meta_box( $args, $box ) {
		$section = $box['args'];

		if ( ! empty( $section['description'] ) ) {
			echo wpautop( esc_html( $section['description'] ) ); // xss ok
		}
		?>
		<table class="form-table">
			<tbody>
				<?php foreach ( $section['fields'] as $field ) : ?>
					<tr>
						<th scope="col"><?php printf(
							'<label for="%s">%s</label>',
							esc_attr( self::_get_field_id( $field ) ),
							esc_html( $field['label'] )
						) ?></th>
						<td>
							<?php self::_the_field( $field, self::get_option( $field['id'] ) ) ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}


	/**
	 * Validate values
	 *
	 * @since  0.1.0
	 * @param  array $input
	 * @return mixed
	 */
	public static function _validate( $input ) {
		$output = array_filter( (array) $input );

		foreach ( $output as $key => $value ) {
			if ( is_array( $value ) ) {
				$output[ $key ] = self::_validate( $value );
			}
			else {
				$output[ $key ] = sanitize_text_field( $value );
			}
		}

		return $output;
	}


	/**
	 * Settings page content
	 *
	 * @since  0.1.0
	 */
	public static function _page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html( self::$title ) ?></h2>
			<form method="post" action="options.php" >
				<?php settings_fields( self::$key ) ?>
				<?php self::$meta_boxes->display() ?>
				<?php submit_button() ?>
			</form>
		</div>
		<?php
	}
}
add_action( 'init', array( 'Kucrut_Settings_Meta_Boxes_Demo', 'init' ), 99 );
