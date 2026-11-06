<?php
/**
 * Shortcode [csfss_notice].
 *
 * Cách dùng:
 *   [csfss_notice type="success" title="Đã lưu"]Đơn hàng của bạn đã được ghi nhận.[/csfss_notice]
 *
 * @package CSF_Sample_Shortcode
 */

defined( 'ABSPATH' ) || exit;

/**
 * Đăng ký và render shortcode hộp thông báo.
 */
final class CSFSS_Notice_Shortcode {

	/**
	 * Tên shortcode.
	 */
	public const TAG = 'csfss_notice';

	/**
	 * Loại thông báo hợp lệ. Giá trị khác sẽ quay về loại mặc định.
	 */
	public const TYPES = array( 'info', 'success', 'warning', 'error' );

	/**
	 * Loại mặc định.
	 */
	public const DEFAULT_TYPE = 'info';

	/**
	 * Đăng ký shortcode (gắn vào hook init).
	 */
	public static function register(): void {
		add_shortcode( self::TAG, array( self::class, 'render' ) );
	}

	/**
	 * Render shortcode.
	 *
	 * @param array<string, mixed>|string $atts    Thuộc tính shortcode (WordPress truyền '' khi không có).
	 * @param string|null                 $content Nội dung giữa thẻ mở và thẻ đóng.
	 * @return string HTML đã escape; chuỗi rỗng khi không có tiêu đề lẫn nội dung.
	 */
	public static function render( $atts = array(), $content = null ): string {
		$atts = shortcode_atts(
			array(
				'type'  => self::DEFAULT_TYPE,
				'title' => '',
			),
			is_array( $atts ) ? $atts : array(),
			self::TAG
		);

		$type    = self::sanitize_type( (string) $atts['type'] );
		$title   = sanitize_text_field( (string) $atts['title'] );
		$content = trim( (string) $content );

		if ( '' === $title && '' === $content ) {
			return '';
		}

		// Cảnh báo/lỗi cần được trình đọc màn hình đọc ngay; còn lại là thông tin trạng thái.
		$role = in_array( $type, array( 'warning', 'error' ), true ) ? 'alert' : 'status';

		$html = sprintf(
			'<div class="%1$s" role="%2$s">',
			esc_attr( 'csfss-notice csfss-notice--' . $type ),
			esc_attr( $role )
		);

		if ( '' !== $title ) {
			$html .= '<p class="csfss-notice__title"><strong>' . esc_html( $title ) . '</strong></p>';
		}

		if ( '' !== $content ) {
			$html .= '<div class="csfss-notice__content">' . wp_kses_post( $content ) . '</div>';
		}

		return $html . '</div>';
	}

	/**
	 * Chuẩn hoá loại thông báo theo whitelist.
	 *
	 * @param string $type Giá trị người dùng nhập.
	 * @return string Một phần tử của self::TYPES.
	 */
	public static function sanitize_type( string $type ): string {
		$type = sanitize_key( $type );

		return in_array( $type, self::TYPES, true ) ? $type : self::DEFAULT_TYPE;
	}
}
