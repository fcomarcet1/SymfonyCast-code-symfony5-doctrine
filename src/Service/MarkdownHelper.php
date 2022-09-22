<?php

declare(strict_types=1);

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class MarkdownHelper
{
    private CacheInterface $cache;
    private MarkdownParserInterface $markdownParser;
    private bool $isDebug;
    private LoggerInterface $logger;


    public function __construct(
        MarkdownParserInterface $markdownParser,
        CacheInterface $cache,
        bool $isDebug,
        LoggerInterface $logger
    ) {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
        //dump($isDebug);
        $this->isDebug = $isDebug;
        $this->logger = $logger;
    }
    
    public function parse(string $source): string {

        if (stripos($source, 'cat') !== false) {
            $this->logger->info('Meow!');
        }

        if ($this->isDebug) {
            return $this->markdownParser->transformMarkdown($source);
        }

        return $this->cache->get('markdown_'.md5($source), function() use ($source) {
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}