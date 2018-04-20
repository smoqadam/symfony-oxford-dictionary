<?php
/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 1/7/18
 * Time: 9:33 PM
 */

namespace App\Service;


use Symfony\Component\DomCrawler\Crawler;

class GoogleCrawler extends AbstractCrawler
{
    protected $url = 'https://translate.google.com/#en/fa/';

    protected function getUrl()
    {
        return $this->url;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function crawl()
    {
        $crawler = new Crawler($this->fetchPage());

        $results = $crawler->filter('div.gt-def-info')->text();
        dump($results);die;
        foreach ($results as $result) {
            dump($result);
        }
        die;
        return $this;
    }

    protected function getWord()
    {
        return $this->word;
    }

    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }
}