<?php

/**
 * Class HttpRequest
 */
class HttpRequest
{
    /**
     * @var (String)
     */
    public $url;
    /**
     * @var (String)
     */
    public $request;
    /**
     * @var (Array)
     */
    public $headers;
    /**
     * @var (String)
     */
    public $content;

    /**
     * HttpRequest constructor.
     * @param $url
     * @param $request
     * @param $headers
     * @param $body
     */
    public function __construct($url, $request, $headers, $body)
    {
        $this->url = $url;
        $this->request = $request;
        $this->headers = $headers;
        $this->content = $body;
    }

    /**
     * validates the json string. Returns a boolean.
     * @param $jsonData
     * @return boolean
     */
    public function isJson($jsonData)
    {
        json_decode($jsonData, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * executes the http request and returns the results as an array
     * @return array
     */
    public function doRequest()
    {
        $response = "";
        if (isset($this->request)) {
            if (strlen($this->url) === 0) {
                return $this->fetchResults($http_response_header, $response, true, "URL is not provided", "1");
            }

            if (strtoupper($this->getRequest()) == "GET") {
                try {
                    $response = file_get_contents($this->getUrl());
                } catch
                (Exception $e) {
                    throw new RuntimeException("Error: \n" . $e->getMessage());
                }
            } else {
                $header = array();
                $cnt = 0;
                foreach ($this->getHeaders() as $key => $value) {
                    $header[$cnt] = $value["htk" . $cnt] . ": " . $value["htv" . $cnt];
                    $cnt++;
                }

                $options = array(
                    'http' => array(
                        'header' => $header,
                        'method' => strtoupper($this->getRequest()),
                        'content' => $this->getContent() === null ? "" : json_encode($this->getContent())
                    )
                );

                try {
                    $context = stream_context_create($options);
                    $response = file_get_contents($this->getUrl(), false, $context);
                } catch
                (Exception $e) {
                    throw new RuntimeException("Error: \n" . $e->getMessage());
                }

            }
        }
        return $this->fetchResults($http_response_header, $response);
    }

    /**
     * @param $http_response_header
     * @param $response
     * @return array
     */
    public function fetchResults($http_response_header, $response, $error = false, $errorMsg = null, $errorCode = null)
    {
        $retArray = array();

        if ($error) {
            $retArray['error'] = true;
            $retArray['errorMsg'] = $errorMsg;
            return $retArray;
        }
        // extract the response status code with regex
        $status_line = $http_response_header[0];
        preg_match('{HTTP/\S*\s(\d{3})}', $status_line, $match);
        $status = $match[1];
        $retArray['responseCode'] = $status;

        // set the http response header to the return array item
        $retArray['responseHeader'] = $http_response_header;
        if ($this->isJson($response)) {
            // return JSON payload as associative array
            $json_array = json_decode($response, true);
            $retArray['response'] = $json_array;
        } else {
            // if response is not a json string
            $retArray['response'] = $response;
        }
        return $retArray;
    }


    /**
     * @return String
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return String
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return String
     */
    public function getContent()
    {
        return $this->content;
    }
}

?>