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
            $response = $client->request('GET', $this->getUrl(), [
                'http_errors' => true,
                'allow_redirects' => true,
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new NotFoundHttpException('word you\'re looking for not found');
            }

            return $response->getBody()->getContents();
        } catch (ConnectException $clientException) { // fetch 404 pages

            $result = file_get_contents($this->getUrl(), false, stream_context_create([
                        'http' => [
                            'ignore_errors' => true,
                        ],
                    ]
                )
            );

            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
