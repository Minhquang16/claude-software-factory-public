<?php
/**
 * Unit test cho CSFSS_Notice_Shortcode (Brain Monkey, không cần WordPress/MySQL).
 *
 * @package CSF_Sample_Shortcode
 */

namespace CSFSS\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use CSFSS_Notice_Shortcode;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class NoticeShortcodeTest extends TestCase {

	use MockeryPHPUnitIntegration;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		// esc_html, esc_attr, ... -> htmlspecialchars (stub chuẩn của Brain Monkey).
		Functions\stubEscapeFunctions();

		Functions\when( 'shortcode_atts' )->alias(
			static function ( array $pairs, array $atts ): array {
				return array_merge( $pairs, array_intersect_key( $atts, $pairs ) );
			}
		);
		Functions\when( 'sanitize_text_field' )->alias(
			static fn( string $s ): string => trim( strip_tags( $s ) )
		);
		Functions\when( 'sanitize_key' )->alias(
			static fn( string $s ): string => (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $s ) )
		);
		Functions\when( 'wp_kses_post' )->returnArg();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_register_adds_shortcode(): void {
		Functions\expect( 'add_shortcode' )
			->once()
			->with( 'csfss_notice', array( CSFSS_Notice_Shortcode::class, 'render' ) );

		CSFSS_Notice_Shortcode::register();
	}

	public function test_plugin_file_hooks_register_on_init(): void {
		Actions\expectAdded( 'init' )->once()->with( array( 'CSFSS_Notice_Shortcode', 'register' ) );

		require dirname( __DIR__, 2 ) . '/csf-sample-shortcode.php';
	}

	public function test_renders_default_info_notice(): void {
		$html = CSFSS_Notice_Shortcode::render( array( 'title' => 'Xin chào' ), 'Nội dung' );

		$this->assertSame(
			'<div class="csfss-notice csfss-notice--info" role="status">'
			. '<p class="csfss-notice__title"><strong>Xin chào</strong></p>'
			. '<div class="csfss-notice__content">Nội dung</div></div>',
			$html
		);
	}

	public function test_wordpress_empty_string_atts_is_supported(): void {
		$html = CSFSS_Notice_Shortcode::render( '', 'Chỉ có nội dung' );

		$this->assertStringContainsString( 'csfss-notice--info', $html );
		$this->assertStringNotContainsString( 'csfss-notice__title', $html );
	}

	/**
	 * @dataProvider type_provider
	 */
	public function test_type_is_whitelisted( string $input, string $expected_type, string $expected_role ): void {
		$html = CSFSS_Notice_Shortcode::render( array( 'type' => $input ), 'x' );

		$this->assertStringContainsString( 'csfss-notice--' . $expected_type . '"', $html );
		$this->assertStringContainsString( 'role="' . $expected_role . '"', $html );
	}

	/**
	 * @return array<string, array{string, string, string}>
	 */
	public static function type_provider(): array {
		return array(
			'success'              => array( 'success', 'success', 'status' ),
			'warning -> alert'     => array( 'warning', 'warning', 'alert' ),
			'error -> alert'       => array( 'error', 'error', 'alert' ),
			'uppercase normalised' => array( 'WARNING', 'warning', 'alert' ),
			'unknown -> info'      => array( 'danger', 'info', 'status' ),
			'attr injection'       => array( 'info" onclick="alert(1)', 'info', 'status' ),
		);
	}

	public function test_title_is_escaped(): void {
		$html = CSFSS_Notice_Shortcode::render( array( 'title' => 'Tom & Jerry "<b>x</b>"' ), '' );

		$this->assertStringContainsString( '<strong>Tom &amp; Jerry &quot;x&quot;</strong>', $html );
		$this->assertStringNotContainsString( '<b>', $html );
	}

	public function test_title_script_injection_is_neutralised(): void {
		$html = CSFSS_Notice_Shortcode::render( array( 'title' => '"><script>alert(1)</script>' ), '' );

		$this->assertStringNotContainsString( '<script', $html );
		$this->assertStringContainsString( '&quot;&gt;alert(1)', $html );
	}

	public function test_content_is_passed_through_wp_kses_post(): void {
		// Ghi đè stub returnArg() của setUp(): expect() không thay được một when() đã khai báo.
		$calls = array();
		Functions\when( 'wp_kses_post' )->alias(
			static function ( string $s ) use ( &$calls ): string {
				$calls[] = $s;
				return '<em>ok</em>x';
			}
		);

		$html = CSFSS_Notice_Shortcode::render( array(), '  <em>ok</em><script>x</script>  ' );

		$this->assertSame( array( '<em>ok</em><script>x</script>' ), $calls, 'wp_kses_post phải được gọi đúng 1 lần với nội dung đã trim' );
		$this->assertStringContainsString( '<div class="csfss-notice__content"><em>ok</em>x</div>', $html );
		$this->assertStringNotContainsString( '<script', $html );
	}

	public function test_returns_empty_string_without_title_and_content(): void {
		$this->assertSame( '', CSFSS_Notice_Shortcode::render( array( 'type' => 'error' ), "  \n " ) );
	}

	public function test_unknown_attributes_are_ignored(): void {
		$html = CSFSS_Notice_Shortcode::render( array( 'onclick' => 'alert(1)', 'title' => 'T' ), '' );

		$this->assertStringNotContainsString( 'onclick', $html );
	}
}
