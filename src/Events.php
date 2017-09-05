<?php
/**
 * Class SkiddleEventAPI
 *
 * This class deals with creating calls to the event endpoint.
 * Any methods that can help to call event info specifically belong in here
 *
 * @package  SkiddleSDK
 */

namespace SkiddleSDK;

class Events extends SkiddleBase
{

    /**
     * @var string The endpoint we will be sending requests to
     */
    const ENDPOINT = '/events/search/';

    /**
     * @var string The endpoint for calling single events
     */
    const SINGLE_ENDPOINT = '/events/';

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
     * @return object An object of stuff
     */
    public function getListings($asArray = false)
    {
        return $this->makeCall(self::ENDPOINT, $asArray);
    }

    /**
     * Pass a listing instance id to get details about an individual event
     * @param bool $listingId The listing instance id
     * @return object An object with results
     */
    public function getListing($listingId = false)
    {
        return $this->makeCall(self::SINGLE_ENDPOINT . $listingId);
    }

}