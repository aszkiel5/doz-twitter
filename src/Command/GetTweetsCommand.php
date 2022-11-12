<?php

namespace App\Command;

use Abraham\TwitterOAuth\TwitterOAuthException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Abraham\TwitterOAuth\TwitterOAuth;

#[AsCommand(
    name: 'app:get-tweets',
    description: 'Get recent tweets from NASA, SpaceX and BoeingSpace',
)]
class GetTweetsCommand extends Command
{
    /**
     * @var string
     */
    private string $twitterApiKey;
    /**
     * @var string
     */
    private string $twitterApiSecret;
    /**
     * @var string
     */
    private string $twitterAccessToken;
    /**
     * @var string
     */
    private string $twitterAccessTokenSecret;
    /**
     * @var array|string[]
     */
    private array $desiredUsernames = ['NASA', 'SpaceX', 'BoeingSpace'];

    /**
     * @param $twitterApiKey
     * @param $twitterApiSecret
     * @param $twitterAccessToken
     * @param $twitterAccessTokenSecret
     */
    public function __construct($twitterApiKey, $twitterApiSecret, $twitterAccessToken, $twitterAccessTokenSecret)
    {
        parent::__construct();
        $this->twitterApiKey = $twitterApiKey;
        $this->twitterApiSecret = $twitterApiSecret;
        $this->twitterAccessToken = $twitterAccessToken;
        $this->twitterAccessTokenSecret = $twitterAccessTokenSecret;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws TwitterOAuthException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connection = new TwitterOAuth(
            $this->twitterApiKey,
            $this->twitterApiSecret,
            $this->twitterAccessToken,
            $this->twitterAccessTokenSecret
        );
        $connection->setApiVersion('2');
        $usernames = implode(',', $this->desiredUsernames);
        $usersDataResult = $connection->get("users/by", ["usernames" => $usernames]);
        if (!empty($usersDataResult->data)) {
            $tweets = [];
            foreach ($usersDataResult->data as $user) {
                $userTweetsResult = $connection->get("users/{$user->id}/tweets");
                $tweets[$user->id] = $userTweetsResult->data;
                sleep(2);
            }
            $tweetsJson = json_encode($tweets);
            if (file_put_contents('var/tweets.json', $tweetsJson)) {
                $io->success('Tweety użytkowników ' . $usernames . ' zostały pobrane i zapisane do pliku.');
                return Command::SUCCESS;
            } else {
                $io->error('Nie udało się zapisać tweetów użytkowników ' . $usernames . ' do pliku.');
                return Command::FAILURE;
            }
        } else {
            $io->error('Nie udało się pobrać identyfikatorów użytkowników ' . $usernames);
            return Command::FAILURE;
        }
    }
}
