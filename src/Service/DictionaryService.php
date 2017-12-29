<?php

namespace App\Service;

use App\Entity\Definition;
use App\Entity\Example;
use App\Entity\Word;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 12/28/17
 * Time: 1:53 PM.
 */
class DictionaryService
{

    private $oxfordDictionaryUrl = 'https://www.oxfordlearnersdictionaries.com/definition/english/';

    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function fetchPage($word)
    {
        return file_get_contents($this->oxfordDictionaryUrl.$word);
    }

    public function translate($word)
    {
        $crawler = new Crawler($this->fetchPage($word));
        $definition = [];
        $definition['word'] = $crawler->filter('div.webtop-g h2')->text();
        $definition['pos'] = $crawler->filter('div.webtop-g span.pos')->text();
        $definition['pron'] = $crawler->filter('div.pron-gs')->text();

        $crawler->filter('#entryContent .sn-g')->each(function (Crawler $node, $i) use (&$definition, $crawler) {
            $def = $node->filter('span.def')->text();
            $definition['definitions'][$def] = [];
            $node->filter('span.x-g')->each(function (Crawler $span, $i) use (&$definition, $def) {
                $definition['definitions'][$def]['examples'][] = $span->filter('span.x-g')->text();
            });
        });

        return $definition;
    }


    public function save($definition)
    {
        $em = $this->em;
        $wordEntity = new Word();
        $wordEntity->setWord($definition['word']);
        $wordEntity->setPartsOfSpeech($definition['pos']);
        $wordEntity->setSavedFrom('web');
        $wordEntity->setSource(Word::SOURCE_OXFORD);
        $wordEntity->setPronunciation($definition['pron']);
        $wordEntity->setUpdatedAt(new \DateTime());
        $wordEntity->setCreatedAt(new \DateTime());
        $em->persist($wordEntity);
        foreach ($definition['definitions'] as $definition => $examples) {
            $definitionEntity = new Definition();
            $definitionEntity->setDefinition($definition);
            $definitionEntity->setWord($em->getReference(Word::class, $wordEntity->getId()));
            $definitionEntity->setCreatedAt(new \DateTime());
            $definitionEntity->setUpdatedAt(new \DateTime());
            $em->persist($definitionEntity);

            foreach ($examples['examples'] as $index => $example) {
                $exampleEntity = new Example();
                $exampleEntity->setDefinition($em->getReference(Definition::class, $definitionEntity->getId()));
                $exampleEntity->setExample($example);
                $exampleEntity->setCreatedAt(new \DateTime());
                $exampleEntity->setUpdatedAt(new \DateTime());
                $em->persist($exampleEntity);
            }
        }
        $em->flush();
        $em->clear();
    }

    public function getDefinitions($word)
    {
        /**
         * @var $wordEntity Word
         * @var $definition Definition
         * @var $example Example
         */
        $wordRepository = $this->em->getRepository('App\Entity\Word');
        $wordEntity = $wordRepository->findOneBy([
            'word' => $word
        ]);

        if (empty($wordEntity)){
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
