<?php

namespace Cradeq\CommonMark\HtmlWhitelistExtension\Tests;

use Cradeq\CommonMark\HtmlWhitelistExtension\HtmlWhitelistExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HtmlWhitelistTest extends TestCase
{
    #[DataProvider('provideBreakTagData')]
    public function test_html_unescaping_on_break_tag_is_allowed($string, $expected): void {
        $environment = new Environment([
            'html_input' => 'strip',
            'html_whitelist' => [
                'tags' => ['br'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new HtmlWhitelistExtension);

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse($string);

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame($expected, $html);
    }

    public static function provideBreakTagData(): array {
        return [
            // Default behaviour
            // Single
            ["new<br>line", "<p>new<br>line</p>\n"],
            ["new<br/>line", "<p>new<br/>line</p>\n"],
            ["new<br />line", "<p>new<br />line</p>\n"],

            // Multiple
            ["extra<br>new<br />line", "<p>extra<br>new<br />line</p>\n"],
            ["new<br><br>line", "<p>new<br><br>line</p>\n"],
            ["new<br /><br />line", "<p>new<br /><br />line</p>\n"],

            // Start and end
            ["<br>newline", "<p><br>newline</p>\n"],
            ["newline<br>", "<p>newline<br></p>\n"],

            // As child node
            ["<div>extra<br>text</div>", ""],
            ["new<div>extra<br>text</div>line", "<p>newextra<br>textline</p>\n"],
            ["new<div extra<br>text>line", "<p>new&lt;div extra<br>text&gt;line</p>\n"],
            ["new<div extra<br>text>line</div>", "<p>new&lt;div extra<br>text&gt;line</p>\n"],
            ["new <div <br>>line</div>", "<p>new &lt;div <br>&gt;line</p>\n"],

            // Spaces
            ["new<br        />line", "<p>new<br        />line</p>\n"],
            ["new<br\t/>line", "<p>new<br\t/>line</p>\n"],

            // Invalid characters
            ["new<br a>line", "<p>newline</p>\n"],
            ["new<br href=''>line", "<p>newline</p>\n"],
            ["new<brr>line", "<p>newline</p>\n"],
        ];
    }

    public function test_closing_html_tags_can_be_unescaped(): void
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'html_whitelist' => [
                'tags' => ['sub'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new HtmlWhitelistExtension);

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse('is<sub>low</sub>');

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame("<p>is<sub>low</sub></p>\n", $html);
    }

    public function test_multiple_tags_can_be_unescaped(): void
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'html_whitelist' => [
                'tags' => ['br', 'sub', 'sup'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new HtmlWhitelistExtension);

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse('<sub>low</sub>new<br>line<sup>high</sup>');

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame("<p><sub>low</sub>new<br>line<sup>high</sup></p>\n", $html);
    }

    public function test_html_tags_with_attributes_are_filtered_away(): void
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'html_whitelist' => [
                'tags' => ['br'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new HtmlWhitelistExtension);

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse('new<br onfocus="alert(\'foo\')">line');

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame("<p>newline</p>\n", $html);
    }

    public function test_break_tag_is_filtered_away_without_extension(): void
    {
        $environment = new Environment([
            'html_input' => 'strip',
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse('new<br>line');

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame("<p>newline</p>\n", $html);
    }

    public function test_break_tag_is_filtered_away_without_br_added_to_config(): void
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'html_whitelist' => [
                'tags' => ['other'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new HtmlWhitelistExtension);

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $document = $parser->parse('new<br>line');

        $html = (string) $renderer->renderDocument($document);

        $this->assertSame("<p>newline</p>\n", $html);
    }
}