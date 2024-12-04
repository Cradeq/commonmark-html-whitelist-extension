## CommonMark HTML Whitelist Extension

This package allows any HTML tags to be used in Markdown, while still escaping or stripping all other HTML input.
Only tags without attributes or styling are supported.
Content of the HTML elements remains intact.

## Install
This project can be installed via composer:

`composer require cradeq/commonmark-html-whitelist-extension`

## Usage
```php
use Cradeq\CommonMark\HtmlWhitelistExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

$environment = new Environment([
    'html_input' => 'strip' // Both strip or escape are supported
    'html_whitelist' => [
        'tags' => ['br', 'sub'], // Any set of html tags
    ],
]);
$environment->addExtension(new CommonMarkCoreExtension);
$environment->addExtension(new HtmlWhitelistExtension);
```