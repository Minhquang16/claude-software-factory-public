<?php
/**
 * Plugin Name:       CSF Sample Shortcode
 * Description:       Plugin mẫu của Claude Software Factory: shortcode [csfss_notice] hiển thị hộp thông báo.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Claude Software Factory
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       csf-sample-shortcode
 *
 * @package CSF_Sample_Shortcode
 */

defined( 'ABSPATH' ) || exit;

define( 'CSFSS_VERSION', '0.1.0' );

require_once __DIR__ . '/includes/class-csfss-notice-shortcode.php';

add_action( 'init', array( 'CSFSS_Notice_Shortcode', 'register' ) );
