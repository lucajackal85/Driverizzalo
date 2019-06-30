<?php


namespace Jackal\Driverizzalo\Command;


use Google_Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CredentialCommand extends Command
{
    private $oauth2Filename = 'php-gd-oauth2.json';

    protected function configure()
    {
        $this->setName('jackal:driverizzalo:create-credential')
            ->addArgument('client-secret',InputArgument::REQUIRED)
            ->addArgument('oauth2',InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $credentialsPath = $input->getArgument('client-secret');
        if(!file_exists($credentialsPath)){
            throw new \RuntimeException('File '.$credentialsPath.' not found');
        }
        $oauth2Path = null;
        if($input->getArgument('oauth2')) {
            $oauth2Path = $input->getArgument('oauth2').$this->oauth2Filename;
        }
        $client = new Google_Client();
        $client->setApplicationName('Google Drive API PHP Quickstart');
        $client->setScopes([\Google_Service_Sheets::DRIVE_FILE]);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        $output->writeln(sprintf("Open the following link in your browser:\n%s\n", $authUrl));
        $output->writeln('Enter verification code:');
        $authCode = trim(fgets(STDIN));
        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        if($oauth2Path) {
            file_put_contents($oauth2Path, json_encode($accessToken));
            $output->writeln("Credentials saved to %s\n", realpath($oauth2Path));
        }else{
            $output->writeln('######################################################');
            $output->writeln(json_encode($accessToken));
            $output->writeln('######################################################');
        }
    }
}