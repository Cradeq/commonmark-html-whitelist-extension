<?php

namespace Cradeq\CommonMark\HtmlWhitelistExtension;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class HtmlTagRenderer implements NodeRendererInterface
{
    /**
     * @param HtmlTag $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        HtmlTag::assertInstanceOf($node);

        return $node->tag;
    }
}
