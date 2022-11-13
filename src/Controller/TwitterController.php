<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwitterController extends AbstractController
{
    /**
     * @param string $username
     * @return Response
     * @throws Exception
     */
    #[Route('/twitter/{username}', name: 'app_twitter', methods: ['GET'])]
    public function index(string $username): Response
    {
        $twitterSaveFilename = $this->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.$_ENV['TWITTER_SAVE_FILENAME'];
        if(file_exists($twitterSaveFilename)) {
            $tweets = json_decode(file_get_contents($twitterSaveFilename),true);
            if(array_key_exists($username,$tweets)) {
                $response = new JsonResponse($tweets[mb_strtolower($username)]);
                $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
                return $response;
            } else {
                throw new Exception('Nie znaleziono tweetów dla danego użytkownika');
            }
        } else {
            throw new FileNotFoundException('Plik z tweetami nie istnieje.');
        }
    }
}
