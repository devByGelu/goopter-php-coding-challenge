<?php
/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
class GoogleClient
{
    const SCOPES = [Google_Service_Sheets::SPREADSHEETS, Google_Service_Drive::DRIVE, Google_Service_Drive::DRIVE_FILE];
    public $client;
    public function GoogleClient()
    {
        $jsonStr = file_get_contents("config.json");

        // Parse data from config file
        $config = json_decode($jsonStr);

        $this->client = new Google_Client();
        $this->client->setScopes(self::SCOPES);
        $this->client->setAuthConfig($config->credentials_path); // Get from config
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = $config->token_path; // Get from config
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $this->client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }
    }
}
