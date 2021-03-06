<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\SerializerBuilder;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WordRepository")
 */
class Word
{
    const SOURCE_OXFORD = 'oxford';

    /**
     * @Exclude
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $word;

    /**
     * @Exclude
     * @ORM\Column(type="string")
     */
    private $savedFrom;

    /**
     * @Exclude
     * @ORM\Column(type="string")
     */
    private $source;

    /**
     * @ORM\Column(type="string")
     */
    private $pronunciation;

    /**
     * @ORM\Column(type="string")
     */
    private $partsOfSpeech;

    /**
     * @Exclude
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Exclude
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="word")
     */
    private $definitions;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    function __construct()
    {
        $this->definitions = new ArrayCollection();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param mixed $word
     */
    public function setWord($word)
    {
        $this->word = $word;
    }

    /**
     * @return mixed
     */
    public function getSavedFrom()
    {
        return $this->savedFrom;
    }

    /**
     * @param mixed $savedFrom
     */
    public function setSavedFrom($savedFrom)
    {
        $this->savedFrom = $savedFrom;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getPronunciation()
    {
        return $this->pronunciation;
    }

    /**
     * @param mixed $pronunciation
     */
    public function setPronunciation($pronunciation)
    {
        $this->pronunciation = $pronunciation;
    }

    /**
     * @return mixed
     */
    public function getPartsOfSpeech()
    {
        return $this->partsOfSpeech;
    }

    /**
     * @param mixed $partsOfSpeech
     */
    public function setPartsOfSpeech($partsOfSpeech)
    {
        $this->partsOfSpeech = $partsOfSpeech;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Definition
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param mixed $definitions
     */
    public function setDefinitions(Definition $definitions)
    {
        $this->definitions->add($definitions);
    }

    function __toString()
    {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($this, 'json');
    }
}
