<?php
/**
 * Class SkiddleSession
 *
 * This class deals with authenticating the user and setting any other parameters that may affect their API session
 *
 * @package  SkiddleSDK
 */

namespace SkiddleSDK;

class SkiddleSession
{

    /**
     * @var ini config to check for the API key
     */
    const API_KEY_NAME = "SKIDDLE_API_KEY";

    /**
     * Basic constructor.  Here we want to set the API key and any other session based stuff
     * @param array $config The details of the session config. Needs an API key (unless that is in the env)
     * @throws SkiddleException
     */
    public function __construct(array $config = [])
    {
        $api_key = isset($config['api_key']) ? $config['api_key'] : getenv(self::API_KEY_NAME);

        if (!$api_key) {
            throw new SkiddleException('API Key is missing');
        }

        $this->setApiKey($api_key);
        if (isset($config['dev_mode']) && $config['dev_mode'] !== false) {
            $this->setDebugMode($config['dev_mode']);
        }
    }

    /**
     * Store the API key in the object
     * @param string $key The users API key.  No validation here yet
     */
    private function setApiKey($key)
    {
        $this->api_key = $key;
    }

    /**
     * Convinience method for returning API key
     * @return string The users API key.  No validation here yet
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Determine whether to enable debugging output etc.
     * @param boolean $mode Whether to enable or not
     */
    private function setDebugMode($mode = false)
    {
        if (!$mode || !isset($mode)) {
            $this->dev_mode = false;
        } else {
            $this->dev_mode = true;
        }
    }

}
