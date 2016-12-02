<?php
/**
 * Class Venues
 *
 * This class deals with creating calls to the venue endpoint.
 * Any methods that can help to call venue info specifically belong in here
 *
 * @package  SkiddleSDK
 */

namespace SkiddleSDK;

class Venues extends SkiddleBase
{

    /**
     * @var The endpoint we will be sending requests to
     */
    const ENDPOINT = '/venues/';

    /**
     * Inherit constructor from SkiddleBase
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Pass arguments in and execute call
     * @param bool $asArray Whether or not to return results as an array
     * @return obj An object of stuff
     */
    public function getListings($asArray = false)
    {
        return $this->makeCall(self::ENDPOINT, $asArray);
    }

}