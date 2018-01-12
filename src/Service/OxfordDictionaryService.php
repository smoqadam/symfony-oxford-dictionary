<?php

namespace App\Service;

use App\Entity\Definition;
use App\Entity\Example;
use App\Entity\Word;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;

/**
 * User: saeed
 * Date: 12/28/17
 * Time: 1:53 PM.
 */
class OxfordDictionaryService extends AbstractDictionaryService
{
    protected $em;

    private $result;

    private $crawler;

    public function __construct(EntityManagerInterface $entityManager, OxfordCrawler $crawler)
    {
        $this->crawler = $crawler;
        $this->em = $entityManager;
    }

    public function translate($word)
    {
        $word = $this->normalizeWord($word);
        $this->result = $this->crawler->setWord($word)->crawl()->getResult();

        return $this;
    }

    public function getResultAsJson()
    {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($this->getResultAsObject(), 'json');
    }

    public function getResultAsObject()
    {
        return $this->fillWord();
    }

    public function fillWord()
    {
        $word = new Word();
        $result = $this->result;
        $word->setWord($result['word']);
        $word->setPartsOfSpeech($result['partsOfSpeech']);
        $word->setPronunciation($result['pronunciation']);
        $word->setSavedFrom(Word::SOURCE_OXFORD);
        $word->setSource(Word::SOURCE_OXFORD);
        $word->setUpdatedAt(new \DateTime());
        $word->setCreatedAt(new \DateTime());
        $this->em->persist($word);
        $this->em->flush();

        foreach ($result['definitions'] as $def => $examples) {
            $definition = new Definition();
            $definition->setDefinition($def);
            $definition->setWord($word);
            $definition->setCreatedAt(new \DateTime());
            $definition->setUpdatedAt(new \DateTime());
            $this->em->persist($definition);
            $this->em->flush();

            foreach ($examples as $example) {
                $exampleEntity = new Example();
                $exampleEntity->setExample($example);
                $exampleEntity->setDefinition($definition);
                $exampleEntity->setCreatedAt(new \DateTime());
                $exampleEntity->setUpdatedAt(new \DateTime());
                $this->em->persist($exampleEntity);

                $definition->setExamples($exampleEntity);
                $this->em->persist($definition);
                $this->em->flush();
            }
            $word->setDefinitions($definition);
            $this->em->persist($word);
        }

        $this->em->flush();
        return $word;
    }
}
