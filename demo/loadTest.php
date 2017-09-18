<?php
/**
 * Here we output the results from the test scenarios
 */
require_once(dirname(__FILE__) . '/../autoloader.php');

/**
 * Perform the test here using the SDK.  Also output a code block to the debug block so people can see the example
 * @param int $test The test id to run
 * @return object|string
 */
function doTest($test)
{
    global $debugInfo;
    try {
        $session = new SkiddleSDK\SkiddleSession(['api_key'=>'23fcafe1bc842f250083bb1923c9f9ee','dev_mode'=>true]);
    } catch (SkiddleSDK\SkiddleException $e) {
        return $e->getMessage();
    }

    $events = new SkiddleSDK\Events;
    try {
        $events->setSession($session);
    } catch (SkiddleSDK\SkiddleException $e) {
        return $e->getMessage();
    }

    $connect_block = <<<BLOCK
    
\$session = new SkiddleSDK\SkiddleSession(['api_key'=>'xxxxxxxxxxxxxxxxxx']);

\$events = new SkiddleSDK\Events;
\$events->setSession(\$session);


BLOCK;

    switch ($test) {
        case 1:
            //this is a test for events within 10 miles of Manchester
            try {
                $events->addCond('latitude','53.4809500');
                $events->addCond('longitude','-2.2374300');
                $events->addCond('radius','10');
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->addCond(\'latitude\',\'53.4809500\');
$events->addCond(\'longitude\',\'-2.2374300\');
$events->addCond(\'radius\',\'10\');
$results = $events->getListings();',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        case 2:
            //test 2 - club nights, random order
            try {
                $events->addCond('eventcode','CLUB');
                $events->addCond('order','random');
                $events->addCond('limit','10');
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->addCond(\'eventcode\',\'CLUB\');
$events->addCond(\'order\',\'random\');
$events->addCond(\'limit\',\'10\');
$results = $events->getListings(); ?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        case 3:
            //test 3 - trending events
            try {
                $events->addCond('eventcode','FEST');
                $events->addCond('ticketsavailable',1);
                $events->addCond('order','trending');
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->addCond(\'eventcode\',\'FEST\');
$events->addCond(\'ticketsavailable\',1);
$events->addCond(\'order\',\'trending\');
$results = $events->getListings();
?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        case 4:
            //test 4 - keyword
            try {
                $events->addCond('keyword','Tribute Acts');
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->addCond(\'keyword\',\'Tribute Acts\');
$results = $events->getListings(); ?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        case 5:
            //test 5 - date range
            try {
                $events->addCond('minDate',date('Y-m-d\TH:i:s',strtotime('today 6pm')));
                $events->addCond('maxDate',date('Y-m-d\TH:i:s',strtotime('Next Tuesday 12pm')));
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->addCond(\'minDate\',date(\'Y-m-d\TH:i:s\',strtotime(\'today 6pm\')));
$events->addCond(\'maxDate\',date(\'Y-m-d\TH:i:s\',strtotime(\'Next Tuesday 12pm\')));
$results = $events->getListings(); ?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        case 6:
            //test 6 - events only with tickets
            try {
                $events->addCond('ticketsavailable',1);
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->addCond(\'ticketsavailable\',1);
$results = $events->getListings(); ?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        case 7:
            //test 7 - individual event
            try {

                $results = $events->getListing(12993676);
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$events->getListing(12993676); ?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
        default:
            //everything
            try {
                $results = $events->getListings();
                $debugInfo = $events->getDebugInfo();
                $debugInfo .= highlight_string('<?php '.$connect_block.'$results = $events->getListings(); ?>',true);
            } catch (SkiddleSDK\SkiddleException $e) {
                return $e->getMessage();
            }
            return $results;
            break;
    }
}

/**
 * Just output a title here
 * @param int $test The test to check
 * @return string The title
 */
function getTitle($test)
{
    switch ($test) {
        case 1:
            return "Listings not too far from Manchester, within 10 miles anyway";
            break;
        case 2:
            return "The latest club nights - not too bothered about what order they're in";
            break;
        case 3:
            return "A list of events all the cool people are going to";
            break;
        case 4:
            return "The best of everything - Tribute Acts";
            break;
        case 5:
            return "Anything happening between teatime tonight and dinnertime next tuesday. To clarify I have my tea at 6 and eat lunch at 12";
            break;
        case 6:
            return "Events that still have tickets on sale so that I don't look like a right idiot at the box office";
            break;
        case 7:
            return "Details about an up-and-coming act";
            break;
        default:
            return "Screw it, just get me all your events";
            break;

    }
}
