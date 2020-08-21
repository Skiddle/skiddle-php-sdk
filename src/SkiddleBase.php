<?php
/**
 * Class SkiddleBase
 *
 * This provides shared methods between entity classes
 *
 * @package  SkiddleSDK
 */

namespace SkiddleSDK;

class SkiddleBase
{

    /**
     * Basic constructor.  Here we basically initiate the SkiddleRequest class if it doesn't exist already
     * @param object $request The SkiddleRequest object
     */
    public function __construct($request = null)
    {
        if (!isset($request)) {
            $request = new SkiddleRequest;
        }
        $this->request = $request;

        $this->conditions = [];
    }

    /**
     * This allows us to store details of the session, so we can make authenticated calls
     * @param object $sess The session object
     * @throws SkiddleException If the session object isn't right
     */
    public function setSession($sess)
    {
        if (!$sess || get_class($sess) !== 'SkiddleSDK\SkiddleSession') {
            throw new SkiddleException('Skiddle API Session not set correctly');
        }
        $this->session = $sess;

        if (isset($sess->dev_mode)) {
            $this->request->dev_mode = true;
        }
    }

    /**
     * Allow a user to specify a condition to filter results by
     * @param string $key The field key
     * @param string $val The field value
     */
    public function addCond($key, $val)
    {
        if (is_array($val)) {
            $val = implode(',', $val);
        }
        
        if (is_bool($val) === true) {
            $val = boolval($val);
        }
        $this->conditions[$key] = $val;
    }

    /**
     * Allow a user to remove a condition
     * @param string $key The field key
     * @throws SkiddleException If the field doesn't exist
     */
    public function delCond($key)
    {
        if (isset($this->conditions[$key])) {
            unset($this->conditions[$key]);
        } else {
            throw new SkiddleException('Trying to delete condition ' . $key . ', but condition not found');
        }
    }

    /**
     * Totally clear out the conditions array
     */
    public function clearCond()
    {
        $this->conditions = [];
    }

    /**
     * For logManyListings, there needs to be certain array keys which the API doesn't return
     * THIS FUNCTION SHOULDN'T GO OUT OF SKIDDLE, IT IS JUST FOR US
     * @param array $data The data to parse through.  Should be a result set from a SkiddleRequest::call()
     * @param bool  $unset Whether we want to delete the original keys or not
     * @return array The data with the keys jigged around
     */
    public function formatForLog($data = [], $unset = false)
    {
        $reformat = [
            'promotorid'   => 'PromotorID',
            'listingid'    => 'ListingID',
            'eventname'    => 'EventName',
            'description'  => 'EventDesc',
            'description'  => 'EventDescShort',
            'cancelled'    => 'Cancelled',
            'goingtocount' => 'goingto',
            'id'           => 'iListingInstance',
            'date'         => 'iEventDate',
            'tickets'      => 'ticketsAdded',
            'minage'       => 'MinAge',
            'entryprice'   => 'EntryPrice',
        ];
        $reformat_venues = [
            'name' => 'Name',
            'id'   => 'EntID',
            'town' => 'Town',
            'postcode_lookup' => 'postcode_lookup',
            'currentranking' => 'currentRanking',
            'currentrankingmax' => 'currentRankingMax',
        ];
        $reformat_times = [
            'doorsopen'  => 'DoorsOpen',
            'doorsclose' => 'DoorsClose'

        ];
        foreach ($data as $k => $ticket) {
            foreach ($reformat as $old => $new) {
                $data[$k][$new] = $ticket[$old];
                if ($unset) {
                    unset($data[$k][$old]);
                }
            }
            foreach ($reformat_venues as $old => $new) {
                $data[$k][$new] = $ticket['venue'][$old];
                if ($unset) {
                    unset($data[$k]['venue'][$old]);
                }
            }
            $data[$k]['Town'] = $ticket['venue']['town']; //:thinking_face:
            foreach ($reformat_times as $old => $new) {
                $data[$k][$new] = $ticket['openingtimes'][$old];
                if ($unset) {
                    unset($data[$k]['openingtimes'][$old]);
                }
            }
        }

        return $data;
    }

    /**
     * Connect to request class to make the actual call
     * @param  string $endpoint The endpoint URL to call
     * @param bool    $asArray Whether to return results as array or object
     * @return object Server result set
     */
    protected function makeCall($endpoint, $asArray = false)
    {
        //if we have any additional conditions, add them here
        $args = $this->conditions;

        //QUIRK: API expects eventcode to be uppercase
        if (isset($args['eventcode'])) {
            $args['eventcode'] = strtoupper($args['eventcode']);
        }
        //append the api key to the arguments
        $args['api_key'] = $this->session->api_key;
        return $this->request->call($endpoint, $args, $asArray);
    }

    /**
     * If we want to get the debug info independently, do so here
     * @return object The results of the debug info
     */
    public function getDebugInfo()
    {
        return $this->request->getDebugInfo();
    }
}
