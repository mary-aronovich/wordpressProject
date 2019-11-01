<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Feedback {
	
	private $_feedback_list = array();

	public function admin_enqueue_scripts() {
		$feedback_list = array(
			//array(
			//	'camp_id' => '2',
			//	'content' => __( 'Want to improve your Pojo theme? Rate our new Forms feature so we know what\'s important to you.', 'pojo' ),
			//	'screen' => array( 'edit-pojo_forms', 'pojo_forms' ),
			//),
		);

		// Get dismissed feedback
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_pojo_feedback', true ) );

		foreach ( $feedback_list as $feedback ) {
			if ( ! in_array( get_current_screen()->id, $feedback['screen'] ) )
				continue;

			if ( in_array( $feedback['camp_id'], $dismissed ) )
				continue;

			if ( ! empty( $feedback['condition_callback'] ) ) {
				$value = call_user_func( $feedback['condition_callback'] );
				if ( ! $value )
					continue;
			}

			$this->_feedback_list[] = $feedback;
		}
		
		if ( ! empty( $this->_feedback_list ) ) {
			wp_enqueue_script( 'pojo-feedback', get_template_directory_uri() . '/core/helpers/feedback/assets/feedback.min.js', array( 'jquery', 'backbone', 'media-views' ), false, true );
		}
	}

	public function feedback_notices() {
		foreach ( $this->_feedback_list as $feedback ) {
			$this->print_feedback_notice( $feedback );
		}
	}

	public function print_feedback_notice( $feedback ) {
		add_action( 'print_media_templates', array( &$this, 'print_media_templates' ) );
		?>
		<div class="updated feedback-camp-<?php echo $feedback['camp_id']; ?>">
			<p>
				<span class="dashicons dashicons-megaphone"></span> <?php echo $feedback['content']; ?>
			</p>
			<p class="submit">
				<a href="#" class="button button-primary pojo-feedback-button" data-campaign="<?php echo $feedback['camp_id']; ?>" data-nonce="<?php echo wp_create_nonce( 'pojo-feedback-submit-' . $feedback['camp_id'] ); ?>"><?php _e( 'Rate Now', 'pojo' ); ?></a>
				<a href="#" data-campaign="<?php echo $feedback['camp_id']; ?>" class="button pojo-dismiss-feedback-button"><?php _e( 'No, don\'t bother me again', 'pojo' ); ?></a>
			</p>
		</div>
		<?php
	}

	public function print_media_templates() {
		?>
		<script type="text/html" id="tmpl-pojo-feedback-panel">
			<div class="modal-wrapper">
				<div class="modal-inner">
					<div class="modal-content">
						<h1><?php _e( 'Give us a feedback', 'pojo' ); ?></h1>

						<p><?php _e( 'We like to receive feedback from our users, it\'s what helps us be better and more improved.', 'pojo' ); ?></p>
						<p><?php _e( 'We\'d be happy to receive a short feedback on the this feature:', 'pojo' ); ?></p>

						<div class="feedback-form">
							<div class="radio-group">
								<label>
									<input type="radio" value="1" name="pojo-feedback-rating" data-setting="rating" />
									<?php _e( 'Dislike', 'pojo' ); ?>
								</label>
								<label>
									<input type="radio" value="2" name="pojo-feedback-rating" data-setting="rating" />
									<?php _e( 'Not useful', 'pojo' ); ?>
								</label>
								<label>
									<input type="radio" value="3" name="pojo-feedback-rating" data-setting="rating" />
									<?php _e( 'Like', 'pojo' ); ?>
								</label>
								<label>
									<input type="radio" value="4" name="pojo-feedback-rating" data-setting="rating" />
									<?php _e( 'Very useful', 'pojo' ); ?>
								</label>
								<label>
									<input type="radio" value="5" name="pojo-feedback-rating" data-setting="rating" />
									<?php _e( 'Amazing feature!', 'pojo' ); ?>
								</label>
							</div>

							<div class="feedback-comment">
								<textarea data-setting="comment" placeholder="<?php _e( 'Have anything to add? We\'re listening.', 'pojo' ); ?>"></textarea>
							</div>

							<div class="feedback-actions">
								<button type="submit" class="feedback-submit button button-primary"><?php _e( 'Send', 'pojo' ); ?></button>
								<span class="spinner"></span>
							</div>
						</div>
						<div class="messages"></div>
					</div>
				</div>
			</div>
		</script>
		<?php
	}

	/**
	 * @param $camp_id
	 *
	 * @return bool
	 */
	public function dismiss_feedback( $camp_id ) {
		$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_pojo_feedback', true ) ) );

		if ( in_array( $camp_id, $dismissed ) )
			return false;

		$dismissed[] = $camp_id;
		$dismissed = implode( ',', $dismissed );

		update_user_meta( get_current_user_id(), 'dismissed_pojo_feedback', $dismissed );
		return true;
	}

	public function ajax_pojo_send_feedback() {
		$camp_id = isset( $_POST['campaign'] ) ? absint( $_POST['campaign'] ) : '';
		
		if ( ! check_ajax_referer( 'pojo-feedback-submit-' . $camp_id, '_nonce', false ) ) {
			wp_send_json_error(
				array(
					'msg' => __( 'You do not have sufficient permissions to access this page.', 'pojo' ),
				)
			);
		}
				
		$comment = isset( $_POST['comment'] ) ? $_POST['comment'] : '';
		$rating = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
		$user = wp_get_current_user();

		$response = wp_remote_post(
			'http://pojo.me/',
			array(
				'sslverify' => false,
				'timeout' => 30,
				'body' => array(
					'pojo_action' => 'pojo_remote_send_feedback',
					'theme' => Pojo_Core::instance()->licenses->updater->theme_name,
					'license' => Pojo_Core::instance()->licenses->get_license_key(),
					'camp_id' => $camp_id,
					'user_email' => $user->user_email,
					'comment' => $comment,
					'rating' => $rating,
				)
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			wp_send_json_error(
				array(
					'msg' => __( 'An error has occurred.', 'pojo' ),
				)
			);
		}
		
		$response_data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! $response_data->success ) {
			wp_send_json_error(
				array(
					'msg' => __( 'An error has occurred.', 'pojo' ),
				)
			);
		}
		
		$this->dismiss_feedback( $camp_id );
		
		wp_send_json_success(
			array(
				'msg' => __( 'Thank you for your feedback, we appreciate it!', 'pojo' )
			)
		);
	}

	public function ajax_dismiss_pojo_feedback() {
		$camp_id = absint( $_POST['campaign'] );
		
		if ( $this->dismiss_feedback( $camp_id ) )
			wp_die( 1 );
		
		wp_die( 0 );
	}

	public function _is_page_builder() {
		if ( 'page' === get_current_screen()->id ) {
			return ( 'page-builder' === atmb_get_field( 'pf_id' ) );
		}
		return true;
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 300 );
		add_action( 'admin_notices', array( &$this, 'feedback_notices' ), 20 );
		add_action( 'wp_ajax_pojo_send_feedback', array( &$this, 'ajax_pojo_send_feedback' ) );
		add_action( 'wp_ajax_dismiss_pojo_feedback', array( &$this, 'ajax_dismiss_pojo_feedback' ) );
	}
}