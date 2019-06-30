<?php


namespace Jackal\Driverizzalo\Model;

class Credentials
{
    /**
     * @var string
     */
    private $clientSecretPathname;
    /**
     * @var string;
     */
    private $oauth2Pathname;
    public function __construct($clientSecretPathname, $oauth2Pathname)
    {
        $this->clientSecretPathname = new \SplFileObject($clientSecretPathname);
        $this->oauth2Pathname = new \SplFileObject($oauth2Pathname);
    }
    /**
     * @return \SplFileObject
     */
    public function getClientSecret()
    {
        return $this->clientSecretPathname;
    }
    /**
     * @return \SplFileObject
     */
    public function getOauth2()
    {
        return $this->oauth2Pathname;
    }
    public function getOauth2Expiration(){
        $content = json_decode($this->getOauth2()->fread($this->getOauth2()->getSize()),true);
        return ($content['created'] + ($content['expires_in'] - 30)) - time();
    }
}