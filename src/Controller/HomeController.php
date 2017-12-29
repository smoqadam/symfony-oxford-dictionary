<?php

namespace App\Controller;

use App\Entity\Word;
use App\Service\DictionaryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        try{
            $content =$dictionaryService->fetchPage($dictionaryService->normalizeWord($word));
            $definition = $dictionaryService->getDefinitions($word);
            if ($definition === null) {
                $definition = $dictionaryService->crawlContent($content);
                $dictionaryService->save($definition);
            }

            return new JsonResponse($definition);
        } catch (NotFoundHttpException $notFoundHttpException){
            return new JsonResponse(['The word you\'re looking for did not find'], 404);
        } catch (\Exception $exception){
            return new JsonResponse(['Something went wrong'], 500);
        }
    }
}
