<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwitterController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/twitter', name: 'app_twitter', methods: ['GET'])]
    public function index(): Response
    {
        $twitterSaveFilename = $this->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.$_ENV['TWITTER_SAVE_FILENAME'];
        if(file_exists($twitterSaveFilename)) {
            $tweetsJson = file_get_contents($twitterSaveFilename);
            $response = new JsonResponse($tweetsJson);
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
            return $response;
        } else {
            throw new FileNotFoundException('Plik z tweetami nie istnieje.');
        }
    }
}
