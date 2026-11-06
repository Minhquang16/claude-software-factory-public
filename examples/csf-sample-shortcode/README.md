# CSF Sample Shortcode

Plugin WordPress mẫu dùng cho **kiểm thử đầu cuối** quy trình Developer → Reviewer của Claude Software Factory.
Không dùng cho site khách hàng; không deploy.

## Cách dùng

```
[csfss_notice type="success" title="Đã lưu"]Đơn hàng của bạn đã được ghi nhận.[/csfss_notice]
```

| Thuộc tính | Giá trị | Mặc định |
|---|---|---|
| `type` | `info`, `success`, `warning`, `error` (giá trị khác → `info`) | `info` |
| `title` | Văn bản thuần (HTML bị loại bỏ) | rỗng |

Nội dung giữa hai thẻ được lọc bằng `wp_kses_post()`. Không có tiêu đề lẫn nội dung → không in gì.
`warning`/`error` dùng `role="alert"`, còn lại `role="status"`.
Filter chuẩn của WordPress `shortcode_atts_csfss_notice` có thể đổi thuộc tính mặc định.

## Phát triển

```bash
composer install
composer lint   # php -l + PHPCS (WPCS 3) + PHPStan level 6
composer test   # PHPUnit + Brain Monkey (không cần WordPress/MySQL)
```

Chưa có: integration test với WP test suite (cần MySQL), QA thủ công trong WordPress thật.
