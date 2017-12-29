<?php

namespace App\Service;

use App\Entity\Definition;
use App\Entity\Example;
use App\Entity\Word;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 12/28/17
 * Time: 1:53 PM.
 */
class DictionaryService
{
    private $oxfordDictionaryUrl = 'https://www.oxfordlearnersdictionaries.com/us/definition/american_english/';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function fetchPage($word)
    {
        $client = new Client();
        $response  = $client->get($this->oxfordDictionaryUrl.$word);
        if ($response->getStatusCode() !== 200) {
            throw new NotFoundHttpException('Something went wrong');
        }

        return $response->getBody()->getContents();
    }

    public function crawlContent($content)
    {
        $crawler = new Crawler($content);
        $definition = [];
        $definition['word'] = $crawler->filter('div.webtop-g h2')->text();
        $definition['pos'] = $crawler->filter('div.webtop-g span.pos')->text();
        $definition['pron'] = $crawler->filter('div.pron-gs')->text();

        $crawler->filter('#entryContent ol .sn-g')->each(function (Crawler $node, $i) use (&$definition, $crawler) {
            $def = $node->filter('span.def')->text();
            $node->filter('span.x-gs span.x-g')->each(function (Crawler $span, $i) use (&$definition, $def) {
                $definition['definitions'][$def]['examples'][] = $span->filter('span.x-g')->text();
            });
        });

        return $definition;
    }

    public function normalizeWord($word)
    {
        $word = Inflector::singularize($word);
        $word = trim($word);
        $word = strtolower($word);

        return $word;
    }

    public function save($definition)
    {
        try {
            $this->em->beginTransaction();
            $wordEntity = new Word();
            $wordEntity->setWord($definition['word']);
            $wordEntity->setPartsOfSpeech($definition['pos']);
            $wordEntity->setSavedFrom('web');
            $wordEntity->setSource(Word::SOURCE_OXFORD);
            $wordEntity->setPronunciation($definition['pron']);
            $wordEntity->setUpdatedAt(new \DateTime());
            $wordEntity->setCreatedAt(new \DateTime());
            $this->em->persist($wordEntity);
            $this->em->flush();
            $this->em->clear();

            foreach ($definition['definitions'] as $definition => $examples) {
                $definitionEntity = new Definition();
                $definitionEntity->setDefinition($definition);
                $definitionEntity->setWord($this->em->getReference(Word::class, $wordEntity->getId()));
                $definitionEntity->setCreatedAt(new \DateTime());
                $definitionEntity->setUpdatedAt(new \DateTime());
                $this->em->persist($definitionEntity);
                $this->em->flush();
                $this->em->clear();

                foreach ($examples['examples'] as $index => $example) {
                    $exampleEntity = new Example();
                    $exampleEntity->setDefinition($this->em->getReference(Definition::class, $definitionEntity->getId()));
                    $exampleEntity->setExample($example);
                    $exampleEntity->setCreatedAt(new \DateTime());
                    $exampleEntity->setUpdatedAt(new \DateTime());
                    $this->em->persist($exampleEntity);
                }
            }
            $this->em->flush();
            $this->em->clear();
            $this->em->commit();
        }catch (\Exception $exception){
            $this->em->rollback();
            dump($definition);die();
            throw new $exception;
        }
    }

    public function getDefinitions($word)
    {
        /**
         * @var Word
         * @var $definition Definition
         * @var $example    Example
         */
        $wordRepository = $this->em->getRepository('App\Entity\Word');
        $wordEntity = $wordRepository->findOneBy([
            'word' => $word,
        ]);

        if (empty($wordEntity)) {
            return null;
        }
        $result = [];
        $result['word'] = $wordEntity->getWord();
        $result['pos'] = $wordEntity->getPartsOfSpeech();
        $result['pron'] = $wordEntity->getPronunciation();
        $result['definitions'] = [];

        foreach ($wordEntity->getDefinitions() as $definition) {
            foreach ($definition->getExamples() as $example) {
                $result['definitions'][$definition->getDefinition()]['examples'][] = $example->getExample();
            }
        }

        return $result;
    }
}
