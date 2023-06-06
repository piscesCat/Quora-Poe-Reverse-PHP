## Quora-Poe-Reverse

Quora-Poe-Reverse là một gói Composer cho phép bạn tương tác với Quora Poe.com bằng mã PHP.

## Ngôn ngữ

- [English](README.md)
- [Tiếng Việt](README_vi.md)

## Cài đặt

Phiên bản PHP tối thiểu yêu cầu là 7.2.5

Sử dụng [Composer](https://getcomposer.org) để cài đặt gói.

Chạy lệnh sau trong terminal:

```
composer require khaiphan/poe-reverse:dev-main
```
## Chatbot

Dưới đây là danh sách các chatbot có sẵn trong gói Quora-Poe-Reverse:

| Chatbot                 | Mô tả                                                             |
|-------------------------|-------------------------------------------------------------------|
| Sage                    | Sage là một chatbot AI được phát triển để cung cấp trả lời thông minh và hỗ trợ đa dạng các chủ đề. (Miễn phí)                      |
| ChatGPT                 | ChatGPT là một chatbot sử dụng mô hình ngôn ngữ tự động học sâu, được huấn luyện trên nhiều nguồn dữ liệu để cung cấp các câu trả lời thông minh. (Miễn phí) |
| Claude-instant          | Claude-instant là một chatbot AI với khả năng trò chuyện tự nhiên và có thể cung cấp thông tin chi tiết về nhiều chủ đề khác nhau. (Miễn phí)       |
| Claude+                 | Claude+ là một phiên bản nâng cao của chatbot Claude-instant, với khả năng trò chuyện sâu hơn và cung cấp thông tin chi tiết hơn. (Trả phí)        |
| Claude-instant-100k     | Claude-instant-100k là phiên bản của Claude-instant với tập dữ liệu huấn luyện lớn hơn (100.000 câu hỏi) để cung cấp phản hồi tốt hơn. (Trả phí)     |
| GPT-4                   | GPT-4 là một chatbot sử dụng mô hình ngôn ngữ tự động học sâu mạnh mẽ, được huấn luyện trên một lượng lớn dữ liệu để cung cấp trả lời chất lượng cao. (Trả phí) |

Để sử dụng chatbot trong mã PHP của bạn, hãy làm theo các bước sau:

1. Đầu tiên, bạn cần include autoloader trong mã PHP của bạn:

```php
require 'vendor/autoload.php';
```

2. Tiếp theo, tạo một instance của lớp `Poe` và cung cấp cookie và tên của chatbot. Ví dụ:

```php
use KhaiPhan\Google\Poe;

$poeSage = new Poe('p-b', 'Sage');
```

Hãy chắc chắn thay `p-b` bằng giá trị của cookie p-b được lấy từ [trang web Quora Poe.com](https://poe.com).
Thay thế `Sage` thành tên chatbot bạn muốn sử dụng.

3. Sau đó, gọi phương thức `getAnswer()` để lấy kết quả phản hồi từ chatbot. Ví dụ:

```php
$answer = $poeSage->getAnswer('Hello');
echo $answer;
```

Lưu ý rằng bạn có thể tạo các đối tượng chatbot khác nhau bằng cách thay đổi giá trị tên chatbot và cookie.

## Giấy phép

Gói này là mã nguồn mở và có sẵn theo [Giấy phép MIT](https://opensource.org/licenses/MIT).