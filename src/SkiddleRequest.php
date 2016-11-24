<?php
/**
 * Class SkiddleRequest
 *
 * This class makes the actual call to the API.  Endpoints and arguments should be determined
 * BEFORE getting to this point.
 *
 * @package  SkiddleSDK
 */

namespace SkiddleSDK;

class SkiddleRequest
{

    /**
     * @var The version of the API we are currently using
     */
    const API_VER = "v1";

    /**
     * @var The URL we want to make calls to
     */
    const API_URL = 'http://www.skiddle.com/api/';

    /**
     * @var Whether we are in dev mode or not
     */
    public $dev_mode;

    /**
     * Basic constructor
     */
    public function __construct(){}

    /**
     * This function is what makes the actual call to the API.
     * We construct the URL by combining the $url and arguments passed into a load of $_GET params
     * @param  string $url The API node we want to access (i.e. /events/search)
     * @param  array  $args A list of arguments to pass.  Keys must match API spec and values must relate
     * @param bool    $asArray Whether to return results as object or array
     * @param  array  $headers Any additional headers we are passing with the request
     * @param  string $method What type of action we want to perform.  Defaults to GET
     * @return string Will either return a JSON of the results, or an error exception
     * @throws SkiddleException
     */
    public function call($url, $args = [], $asArray = false,$headers = [], $method = 'GET')
    {
        //If we have no endpoint, stop here
        if (!isset($url)) {
            throw new SkiddleException('API Endpoint not set');
        }

        $mergedHeaders = array();
        foreach ($headers as $key => $val) {
            $mergedHeaders[] = "$key: $val";
        }


        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $mergedHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true, //should be true by default but just in case
            CURLOPT_CAINFO => __DIR__ . '/cacert.pem',
        );

        switch ($method) {
            case 'DELETE':
            case 'PUT':
            case 'POST': //Currently just dealing with GET
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = $method;

                if ($args) {
                    $url .= '?' . http_build_query($args);
                }

                break;
        }

        $options[CURLOPT_URL] = self::API_URL . self::API_VER . $url;

        if ($this->dev_mode) {
            $start = strtotime('now');
            $this->debug = '<pre>Debug info</pre>';
            $this->debug = '<pre>Call made at ' . date('Y-m-d H:i:s', $start) . '</pre>';
            $debugurl = $options[CURLOPT_URL];
            $debugurl = str_replace($args['api_key'],'xxxxxxxxxxxxxxxxxx',$debugurl);//Hide the API Key from prying eyes
            $this->debug .= '<pre>URL called: ' . $debugurl . '</pre>';
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if ($this->dev_mode) {
            $end = strtotime('now');
            $this->debug .= '<pre>cURL request info: ' . str_replace($args['api_key'],'xxxxxxxxxxxxxxxxxx',json_encode(curl_getinfo($ch))) . '</pre>';
            $this->debug .= '<pre>Response at ' . date('Y-m-d H:i:s', $end) . '</pre>';
            $this->debug .= '<pre>Time (seconds):' . ($end - $start) . '</pre>';
        }

        if (curl_error($ch)) {
            throw new SkiddleException('cURL transport error: ' . curl_errno($ch) . ' ' . curl_error($ch));
        }

        list($headers, $rawBody) = explode("\r\n\r\n", $response, 2);

        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($this->dev_mode) {
            $this->debug .= '<pre>Response headers: ' . json_encode($headers) . '</pre>';
            $this->debug .= '<pre>Response body: ' . json_encode($rawBody) . '</pre>';
        }

        if ($status < 200 || $status > 299) {
            $errorBody = json_decode($rawBody);

            if (isset($errorBody->errormessage) && isset($errorBody->error)) {
                // Error from the API
                throw new SkiddleException($errorBody->errormessage, $errorBody->error);
            } else {
                // Error from somewhere else
                throw new SkiddleException('An unknown error occurred. - ' . $status, $status);
            }
        }

        return $this->format($rawBody,$asArray);
    }

    /**
     * Simply take the data string and return as JSON
     * @param  string       $data JSON String
     * @param  bool         $asArray Whether or not to return results as an object or array
     * @return object|array Decoded object or array
     * @throws SkiddleException
     */
    private function format($data, $asArray = false)
    {
        $object = json_decode($data, $asArray);
        if (json_last_error() !== JSON_ERROR_NONE && json_last_error() !== 0) {
            throw new SkiddleException('Error parsing server response');
            return false;
        }
        return $object;
    }

    /**
     * Here we can choose to get some debug info if we need it
     * @return mixed
     */
    public function getDebugInfo(){
        return $this->debug;
    }

}