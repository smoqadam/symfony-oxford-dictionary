<?php

namespace App\Service;

use App\Entity\Definition;
use App\Entity\Example;
use App\Entity\Word;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 12/28/17
 * Time: 1:53 PM.
 */
class OxfordDictionaryService implements DictionaryProviderInterface
{
    private $oxfordDictionaryUrl = 'https://www.oxfordlearnersdictionaries.com/us/definition/american_english/';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function fetchPage($word)
    {
        try {
            $client = new Client();
            $response = $client->get($this->oxfordDictionaryUrl.$word);
            if ($response->getStatusCode() !== 200) {
                throw new NotFoundHttpException('word you\'re looking for not found');
            }

            return $response->getBody()->getContents();
        } catch (ConnectException $clientException) {
            return file_get_contents($this->oxfordDictionaryUrl.$word);
        } catch (\Exception $exception) {
            throw new $exception('Word not found');
        }
    }

    /**
     * @param $content
     *
     * @return Word
     */
    public function crawlContent($content)
    {
        $crawler = new Crawler($content);
        $word = new Word();

        $word->setWord($crawler->filter('div.webtop-g h2')->text());
        $word->setPartsOfSpeech($crawler->filter('div.webtop-g span.pos')->text());
        $word->setPronunciation($crawler->filter('div.pron-gs')->text());
        $word->setSavedFrom(Word::SOURCE_OXFORD);
        $word->setSource(Word::SOURCE_OXFORD);
        $word->setUpdatedAt(new \DateTime());
        $word->setCreatedAt(new \DateTime());
        $this->em->persist($word);
        $this->em->flush();

        $crawler->filter('#entryContent ol .sn-g')->each(function (Crawler $node, $i) use ($word) {
            $definition = new Definition();
            $definition->setDefinition($node->filter('span.def')->text());
            $definition->setWord($word);
            $definition->setCreatedAt(new \DateTime());
            $definition->setUpdatedAt(new \DateTime());
            $this->em->persist($definition);
            $this->em->flush();

            $node->filter('span.x-gs span.x-g')->each(function (Crawler $span, $i) use ($definition) {
                $example = new Example();
                $example->setExample($span->filter('span.x-g')->text());
                $example->setDefinition($definition);
                $example->setCreatedAt(new \DateTime());
                $example->setUpdatedAt(new \DateTime());
                $this->em->persist($example);
                $this->em->flush();
                $definition->setExamples($example);
            });
            $word->setDefinitions($definition);
        });

        return $word;
    }

    public function normalizeWord($word)
    {
        $word = Inflector::singularize($word);
        $word = trim($word);
        $word = strtolower($word);

        return $word;
    }

    /**
     * @param $word
     *
     * @return Word
     */
    public function translate($word)
    {
        $word = $this->normalizeWord($word);
        $wordRepository = $this->em->getRepository('App\Entity\Word');
        $wordEntity = $wordRepository->findOneBy([
            'word' => $word,
        ]);

        if ($wordEntity === null) {
            return $this->crawlContent($this->fetchPage($word));
        }

        return $wordEntity;
    }
}
