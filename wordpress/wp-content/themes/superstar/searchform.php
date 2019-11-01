<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?><form role="search" method="get" class="form form-search" action="<?php echo home_url( '/' ); ?>">
	<input type="search" title="<?php _e( 'Search', 'pojo' ); ?>" name="s" value="<?php echo ( isset( $_GET['s'] ) ) ? esc_attr( $_GET['s'] ) : ''; ?>" placeholder="<?php _e( 'Search', 'pojo' ); ?>" class="field search-field">
	<button value="<?php _e( 'Search', 'pojo' ); ?>" class="search-submit" type="submit"><i class="fa fa-search"></i></button>
</form>
