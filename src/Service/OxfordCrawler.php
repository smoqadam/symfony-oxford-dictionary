<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

class OxfordCrawler extends AbstractCrawler
{
    protected $url = 'https://www.oxfordlearnersdictionaries.com/us/definition/american_english/';

    public function crawl()
    {
        $crawler = new Crawler($this->fetchPage());
        $result['word'] = $crawler->filter('div.webtop-g h2')->text();
        $result['partsOfSpeech'] = $crawler->filter('div.webtop-g span.pos')->text();
        $result['pronunciation'] = $crawler->filter('div.pron-gs')->text();
        $result['definitions'] = [];

        $crawler->filter('#entryContent ol .sn-g')->each(function (Crawler $node, $i) use (&$result) {
            $definition = $node->filter('span.def')->text();
            $node->filter('span.x-gs span.x-g')->each(function (Crawler $span, $i) use (&$result, &$definition) {
                $result['definitions'][$definition][] = $span->filter('span.x-g')->text();
            });
        });

        $this->setResult($result);

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    private function setResult($result){
        $this->result = $result;
    }

    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    public function getUrl()
    {
        return $this->url.$this->getWord();
    }

    protected function getWord()
    {
        return $this->word;
    }

}