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
    const API_URL = 'https://www.skiddle.com/api/';

    /**
     * @var The URL we want to make calls to (new api)
     */
    const API_V3_URL = 'https://api.skiddle.com/v3';

    /**
     * @var Whether we are in dev mode or not
     */
    public $dev_mode;

    /**
     * @var Switch between different api versions
     */
    public $apiVersion = 1;

    /**
     * Basic constructor
     */
    public function __construct()
    {
    }

    public function setApiVersion(int $apiVersion): void{
        $this->apiVersion = $apiVersion;
    }

    public function getApiVersion(): int{
        return $this->apiVersion;
    }

    /**
     * v3 API - forces camelcasing of request & response data
     * @param array  $requestData request keys to replace
     * @return array The formatted results
     */
    public function transformRequestKeys($requestData){

        $renameKeys = [
            'api_key' => 'apiKey'
        ];

        $newArgs = [];
        foreach ($requestData as $key => $val) {
            if (array_key_exists($key, $renameKeys)) {
                $key = $renameKeys[$key];
            }
            $newArgs[$key] = $val;
        }

        return $newArgs;

    }

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
    public function call($url, $args = [], $asArray = false, $headers = [], $method = 'GET')
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

        $options[CURLOPT_URL] = self::API_URL . self::API_VER;
        if ($this->getApiVersion() == 3) {
            $options[CURLOPT_URL] = self::API_V3_URL;
            $args = $this->transformRequestKeys($args);
        }

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

        $options[CURLOPT_URL] .= $url;

        if ($this->dev_mode) {
            $start = strtotime('now');
            $this->debug = '<pre>Debug info</pre>';
            $this->debug = '<pre>Call made at ' . date('Y-m-d H:i:s', $start) . '</pre>';
            $debugurl = $options[CURLOPT_URL];
            $debugurl = str_replace($args['api_key'], 'xxxxxxxxxxxxxxxxxx', $debugurl);//Hide the API Key from prying eyes
            $this->debug .= '<pre>URL called: ' . $debugurl . '</pre>';
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if ($this->dev_mode) {
            $end = strtotime('now');
            $this->debug .= '<pre>cURL request info: ' . str_replace($args['api_key'], 'xxxxxxxxxxxxxxxxxx', json_encode(curl_getinfo($ch))) . '</pre>';
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
                // API call error
                throw new SkiddleException($errorBody->errormessage, $errorBody->error);
            }elseif (isset($errorBody->detail) && isset($errorBody->status)) {
                // API v3 call error
                throw new SkiddleException(html_entity_decode($errorBody->detail, ENT_QUOTES), $errorBody->status);
            } else {
                // Something else
                throw new SkiddleException('An unknown error occurred - ' . $rawBody . '-' . $status, $status);
            }
        }

        return $this->format($rawBody, $asArray);
    }

    /**
     * Simply take the data string and return as JSON
     * @param  string $data JSON String
     * @param  bool   $asArray Whether or not to return results as an object or array
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
    public function getDebugInfo()
    {
        return $this->debug;
    }

}
