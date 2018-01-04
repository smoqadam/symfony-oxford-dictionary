<?php

namespace App\Service;

use \App\Entity\Word;
/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 1/4/18
 * Time: 3:15 PM
 */
Interface DictionaryProviderInterface
{
    public function translate($word);

}