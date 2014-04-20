<?php

/**
 * Plugin Name: Settings Page Meta Boxes
 * Description: A helper class to easily create custom meta boxes on plugin/theme settings page.
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * Version: 0.1.1
 * License: GPL v2
 */

if ( ! class_exists( 'Kucrut_Settings_Meta_Boxes' ) ) {
	class Kucrut_Settings_Meta_Boxes {

		const VERSION = '0.1.1';

		/**
		 * Holds default arguments
		 *
		 * @since 0.1.0
		 * @var array
		 */
		protected $defaults = array(
			'hook'            => '',
			'default_columns' => 1,
			'max_columns'     => 4,
		);

		/**
		 * Holds arguments passed to constructor
		 *
		 * @since 0.1.0
		 * @var object
		 */
		protected $args;


		/**
		 * Constructor
		 *
		 * @since 0.1.0
		 * @param string $hook Settings page's hook name returned by add_options_page()
		 */
		public function __construct( $args ) {
			if ( ! did_action( 'admin_init' ) ) {
				_doing_it_wrong( __CLASS__, "This class shouldn't be initialized before admin_init hook", self::VERSION );

				return false;
			}

			$args = (object) wp_parse_args( $args, $this->defaults );

			$args->hook = trim( $args->hook );
			if ( empty( $args->hook ) ) {
				_doing_it_wrong( __CLASS__, 'Hook name cannot be empty', self::VERSION );

				return false;
			}

			$args->max_columns     = min( absint( $args->max_columns ), $this->defaults['max_columns'] );
			$args->default_columns = max( min( absint( $args->default_columns ), $args->max_columns ), $this->defaults['max_columns'] );

			$this->args = $args;

			add_action( "load-{$this->args->hook}", array( $this, '_load_page' ) );
		}


		/**
		 * Add callbacks to load-* hook
		 *
		 * @since 0.1.0
		 * @wp_hook action load-*
		 */
		public function _load_page() {
			add_screen_option(
				'layout_columns',
				array(
					'default' => $this->args->default_columns,
					'max'     => $this->args->max_columns,
				)
			);

			wp_enqueue_script( 'postbox' );
			add_action( 'admin_print_footer_scripts', array( $this, '_script' ), 99 );

			do_action( 'add_meta_boxes_' . $this->args->hook, $this->args );
		}


		/**
		 * Script
		 *
		 * This will initialize the sortable meta boxes and mark drop areas.
		 *
		 * @since   0.1.0
		 * @wp_hook admin_print_footer_script
		 */
		public function _script() {
			?>
			<script>
				(function($){
					postboxes.add_postbox_toggles( window.pagenow );

					var markArea = function() {
						$('div.meta-box-sortables:visible').each(function(i, el){
							var t = $(this);
							var c = t.children('.postbox:visible');

							t.toggleClass( 'empty-container', ! c.length );
						});
					};

					$('div.meta-box-sortables').on( 'sortreceive', function( event, ui ) {
						if ( 'dashboard_browser_nag' === ui.item[0].id ) {
							$(ui.sender).sortable('cancel');
						}

						markArea();
					});

					markArea();
				}(jQuery));
			</script>
			<?php
		}


		/**
		 * Display meta boxes
		 *
		 * @since 0.1.0
		 */
		public function display() {
			?>
			<div id="dashboard-widgets-wrap">
				<?php $class = 'metabox-holder columns-' . get_current_screen()->get_columns(); ?>
				<div id="dashboard-widgets" class="<?php echo esc_attr( $class ) ?>">
					<div id="postbox-container-1" class="postbox-container">
						<?php do_meta_boxes( $this->args->hook, 'normal', $this->args ); ?>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<?php do_meta_boxes( $this->args->hook, 'side', $this->args ); ?>
					</div>
					<div id="postbox-container-3" class="postbox-container">
						<?php do_meta_boxes( $this->args->hook, 'column3', $this->args ); ?>
					</div>
					<div id="postbox-container-4" class="postbox-container">
						<?php do_meta_boxes( $this->args->hook, 'column4', $this->args ); ?>
					</div>
				</div>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			</div>
			<?php
		}
	}
}
