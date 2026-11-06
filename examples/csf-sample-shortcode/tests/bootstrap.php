<?php
/**
 * Bootstrap cho unit test: không nạp WordPress; hàm WP được giả lập bằng Brain Monkey.
 *
 * @package CSF_Sample_Shortcode
 */

define( 'ABSPATH', __DIR__ . '/' );

require dirname( __DIR__ ) . '/vendor/autoload.php';
require dirname( __DIR__ ) . '/includes/class-csfss-notice-shortcode.php';
