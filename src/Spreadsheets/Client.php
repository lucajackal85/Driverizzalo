<?php


namespace Jackal\Driverizzalo\Spreadsheets;


use Exception;
use Google_Client;
use Jackal\Driverizzalo\Model\Credentials;

class Client
{
    protected $client;

    public function __construct(Credentials $credentials){

        $client = new Google_Client();
        $client->setApplicationName('Google Drive API PHP Quickstart');
        $client->setScopes([\Google_Service_Sheets::DRIVE_FILE]);
        $client->setAuthConfig($credentials->getClientSecret()->getFilename());
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = $credentials->getOauth2()->getFilename();
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        $this->client = $client;
    }

    /**
     * @return Google_Client
     */
    public function getClient()
    {
        return $this->client;
    }
}