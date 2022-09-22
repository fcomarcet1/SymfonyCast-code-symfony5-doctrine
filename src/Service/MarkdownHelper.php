<?php

declare(strict_types=1);

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Contracts\Cache\CacheInterface;

class MarkdownHelper
{
    private CacheInterface $cache;
    private MarkdownParserInterface $markdownParser;


    public function __construct(MarkdownParserInterface $markdownParser, CacheInterface $cache) {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
    }
    
    public function parse(string $source): string {
        return $this->cache->get('markdown_'.md5($source), function() use ($source) {
            return $this->markdownParser->transformMarkdown($source);
        });
    }
}