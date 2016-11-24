<?php
/**
 * Class Events
 *
 * This class deals with creating calls to the event endpoint.
 * Any methods that can help to call event info specifically belong in here
 *
 * @package  SkiddleSDK
 */

namespace SkiddleSDK;

class Events extends SkiddleBase {

	/**
	 * @var The endpoint we will be sending requests to
	 */
	const ENDPOINT = '/events/search/';

	/**
	 * Inherit constructor from SkiddleBase
	 */
	public function __construct() {
		parent::__construct();
	}

    /**
     * Pass arguments in and execute call
     * @param bool  $asArray Whether or not to return results as an array
     * @return obj An object of stuff
     */
	public function getListings($asArray = false) {
		return $this->makeCall(self::ENDPOINT,$asArray);
	}

}