<?php

namespace App\Service;

use \App\Entity\Word;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 1/4/18
 * Time: 3:15 PM
 */
Abstract class AbstractDictionaryService
{
    abstract public function translate($word);

    protected $em;
//
//    public function __construct(EntityManagerInterface $em)
//    {
//        $this->em = $em;
//    }

    public function normalizeWord($word)
    {
        $word = Inflector::singularize($word);
        $word = trim($word);
        $word = strtolower($word);

        return $word;
    }

}