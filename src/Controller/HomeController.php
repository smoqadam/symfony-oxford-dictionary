<?php

namespace App\Controller;

use App\Entity\Word;
use App\Service\AbstractDictionaryService;
use App\Service\OxfordDictionaryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/{word}", name="home")
     *
     * @param AbstractDictionaryService $dictionaryService
     * @param $word
     *
     * @return Response
     */
    public function translate(OxfordDictionaryService $dictionaryService, $word)
    {
        try {
            $wordRepository = $this->getDoctrine()->getRepository('App\Entity\Word');
            $result = $wordRepository->findOneBy([
                'word' => $word,
            ]);

            if ($result === null) {
                $result = $dictionaryService->translate($word)->getResultAsJson();
            }

            return new Response($result, 200, [
                'Content-Type' => 'application/json',
            ]);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return new JsonResponse(['The word you\'re looking for did not find'], 404);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'Something went wrong '.$exception->getMessage().'::'.$exception->getFile().'::'.$exception->getLine()], 500);
        }
    }
}
