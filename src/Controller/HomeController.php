<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Word;
use App\Service\GoogleDictionaryService;
use App\Service\OxfordDictionaryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends Controller
{
    /**
     * @Route("/{word}", name="home")
     *
     * @param OxfordDictionaryService $dictionaryService
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
                $result = $dictionaryService->translate($word)->getResult();
                if (isset($result['didYouMean'])) {
                    $result = \GuzzleHttp\json_encode($result);
                } else {
                    $result = $dictionaryService->getResultAsObject();
                }
            }

            return new Response($result, 200, [
                'Content-Type' => 'application/json',
            ]);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return new JsonResponse(['The word you\'re looking for did not find'], 404);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'Something went wrong '.$exception->getMessage().'::'.$exception->getFile().'::'.$exception->getLine(), ], 500);
        }
    }

    /**
     * @Route("/{from}/{to}/{word}", name="google_translate")
     *
     * @param $word
     *
     * @return Response
     */
    public function translateByGoogle(GoogleDictionaryService $dictionaryService, $word)
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
                'Something went wrong '.$exception->getMessage().'::'.$exception->getFile().'::'.$exception->getLine(), ], 500);
        }
    }

    /**
     * @Route("/user/register", name="register")
     */
    public function register(UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $user->setPassword($encoder->encodePassword($user, '123456'));
        $user->setUsername('saeed');
        $user->setEmail('saeed@saeed.com');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'msg' => 'ok'
        ]);
    }
}
