<?php

namespace Cradeq\CommonMark\HtmlWhitelistExtension;

use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

class HtmlTagParser implements InlineParserInterface, ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        $tags = $this->config->get('html_whitelist/tags');
        $tagRegex = implode('|', $tags);

        return InlineParserMatch::regex("<\/?($tagRegex)\s*\/?>");
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
        $inlineContext->getContainer()->appendChild(new HtmlTag($inlineContext->getFullMatch()));

        return true;
    }
}