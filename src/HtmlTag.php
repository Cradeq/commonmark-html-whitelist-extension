<?php

namespace Cradeq\CommonMark\HtmlWhitelistExtension;

use League\CommonMark\Node\Inline\AbstractInline;

final class HtmlTag extends AbstractInline
{
    public function __construct(public string $tag)
    {
        parent::__construct();
    }
}
