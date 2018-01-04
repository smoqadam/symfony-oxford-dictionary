<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DefinitionRepository")
 */
class Definition implements \JsonSerializable
{
    /**
     * @Exclude
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="Word", inversedBy="definitions", cascade={"persist"})
     */
    private $word;

    /**
     * @ORM\Column(type="string")
     */
    private $definition;

    /**
     * @ORM\OneToMany(targetEntity="Example", mappedBy="definition")
     */
    private $examples;

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


    function __construct()
    {
        $this->examples = new ArrayCollection();
    }

    /**
     * @Exclude
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Word
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param Word $word
     */
    public function setWord(Word $word)
    {
        $this->word = $word;
    }

    /**
     * @return mixed
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param mixed $definition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }

    /**
     * @return Example
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * @param Example $examples
     */
    public function setExamples(Example $examples)
    {
        $this->examples->add($examples);
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'word' => $this->word,
            'definition' => $this->definition,
            'examples' => $this->examples,
        ];
    }
}
