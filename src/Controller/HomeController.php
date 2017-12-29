<?php

namespace App\Controller;

use App\Entity\Word;
use App\Service\DictionaryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/{word}", name="home")
     *
     * @param DictionaryService $dictionaryService
     * @param $word
     *
     * @return JsonResponse
     */
    public function translate(DictionaryService $dictionaryService, $word)
    {
        $wordRepository = $this->getDoctrine()->getRepository('App\Entity\Word');

        $definition = null;//$dictionaryService->getDefinitions($word);
        if ($definition === null) {
            $definition = $dictionaryService->translate($word);
            dump($definition);die();
            $dictionaryService->save($definition);
        }

        dump($definition);
        return new JsonResponse([
            'word' => $word,
            'def' => $definition,
        ]);
    }
}
