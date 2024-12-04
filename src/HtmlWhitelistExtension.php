<?php

namespace Cradeq\CommonMark\HtmlWhitelistExtension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class HtmlWhitelistExtension implements ExtensionInterface, ConfigurableExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        if ($environment->getConfiguration()->get('html_whitelist/tags') === []) {
            return;
        }

        $environment->addInlineParser(new HtmlTagParser, 50);
        $environment->addRenderer(HtmlTag::class, new HtmlTagRenderer);
    }

    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('html_whitelist', Expect::structure([
            'tags' => Expect::arrayOf(Expect::string())->default([]),
        ]));
    }
}
