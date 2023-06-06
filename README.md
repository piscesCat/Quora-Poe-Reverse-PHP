## Quora-Poe-Reverse

Quora-Poe-Reverse is a Composer package that allows you to interact with Quora Poe.com using PHP code.

## Language

- [English](README.md)
- [Tiếng Việt](README_vi.md)

## Installation

The minimum required PHP version is 7.2.5

Use [Composer](https://getcomposer.org) to install the package.

Run the following command in the terminal:

```
composer require khaiphan/poe-reverse:dev-main
```
## Chatbot

Below is a list of available chatbots in the Quora-Poe-Reverse package:

| Chatbot                 | Description                                                       |
|-------------------------|-------------------------------------------------------------------|
| Sage                    | Sage is an AI chatbot developed to provide intelligent answers and support various topics. (Free)                         |
| ChatGPT                 | ChatGPT is a chatbot that uses a deep learning language model, trained on diverse data sources, to provide intelligent responses. (Free) |
| Claude-instant          | Claude-instant is an AI chatbot with natural conversation ability and can provide detailed information on various topics. (Free)        |
| Claude+                 | Claude+ is an advanced version of the Claude-instant chatbot, with deeper conversational capabilities and more detailed information. (Paid)        |
| Claude-instant-100k     | Claude-instant-100k is a version of Claude-instant with a larger training dataset (100,000 questions) to provide better responses. (Paid)      |
| GPT-4                   | GPT-4 is a chatbot that uses a powerful deep learning language model, trained on a large amount of data, to provide high-quality responses. (Paid) |

To use a chatbot in your PHP code, follow these steps:

1. First, you need to include the autoloader in your PHP code:

```php
require 'vendor/autoload.php';
```

2. Next, create an instance of the `Poe` class and provide the cookie and the name of the chatbot. For example:

```php
use KhaiPhan\Google\Poe;

$poeSage = new Poe('p-b', 'Sage');
```

Make sure to replace `p-b` with the value of the p-b cookie obtained from the [Quora Poe.com website](https://poe.com).
Replace `Sage` with the name of the chatbot you want to use.

3. Then, call the `getAnswer()` method to retrieve the response from the chatbot. For example:

```php
$answer = $poeSage->getAnswer('Hello');
echo $answer;
```

Note that you can create different chatbot objects by changing the chatbot name and cookie value.

## License

This package is open-source and available under the [MIT License](https://opensource.org/licenses/MIT).