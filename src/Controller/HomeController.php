<?php

namespace App\Controller;

use App\Entity\Word;
use App\Service\DictionaryProviderInterface;
use App\Service\OxfordDictionaryService;
use JMS\Serializer\SerializerBuilder;
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
     * @param DictionaryProviderInterface $dictionaryService
     * @param $word
     *
     * @return Response
     */
    public function translate(DictionaryProviderInterface $dictionaryService, $word)
    {
        try {
            $definition = $dictionaryService->translate($word);

            $serializer = SerializerBuilder::create()->build();
            $result = $serializer->serialize($definition, 'json');

            return new Response($result, 200, [
                'Content-Type' => 'application/json',
            ]);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return new JsonResponse(['The word you\'re looking for did not find'], 404);
        } catch (\Exception $exception) {
            return new JsonResponse(['Something went wrong '.$exception->getMessage()], 500);
        }
    }
}
