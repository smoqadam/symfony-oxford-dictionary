<?php

namespace App\Service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractCrawler
{
    protected $url = '';

    protected $word;

    protected $result = [];


    abstract protected function getUrl();

    abstract public function getResult();

    abstract public function crawl();

    abstract public function setWord($word);

    abstract protected function getWord();

    protected function fetchPage()
    {
        try {
            $client = new Client();
            $response = $client->get($this->getUrl());
            if ($response->getStatusCode() !== 200) {
                throw new NotFoundHttpException('word you\'re looking for not found');
            }

            return $response->getBody()->getContents();
        } catch (ConnectException $clientException) {
            return file_get_contents($this->getUrl());
        } catch (\Exception $exception) {
            throw new $exception('Word not found');
        }
    }
}