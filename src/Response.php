<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Curl;

// ------------------------------------------------------------------------

use O2System\Curl\Response\Error;
use O2System\Curl\Response\Headers;
use O2System\Curl\Response\Info;
use O2System\Curl\Response\SimpleJSONElement;
use O2System\Curl\Response\SimpleQueryElement;
use O2System\Curl\Response\SimpleSerializeElement;
use O2System\Curl\Response\SimpleXMLElement;

/**
 * Class Response
 *
 * @package O2System\Curl
 */
class Response
{
    /**
     * Response::$string
     *
     * Raw string response.
     *
     * @var string
     */
    protected $string;

    /**
     * Response::$info
     *
     * Response info object.
     *
     * @var Info
     */
    protected $info;

    /**
     * Response::$error
     *
     * Response error object.
     *
     * @var Error
     */
    protected $error;

    /**
     * Response::$headers
     *
     * Response headers object.
     *
     * @var Headers
     */
    protected $headers;

    /**
     * Response::$body
     *
     * Response body.
     *
     * @var SimpleJSONElement|SimpleQueryElement|SimpleXMLElement|SimpleSerializeElement|\DOMDocument|string
     */
    protected $body;

    // ------------------------------------------------------------------------

    /**
     * Response::__construct
     *
     * @param resource $curlHandle Curl handle resource.
     */
    public function __construct($curlHandle)
    {
        if (($errorNumber = curl_errno($curlHandle)) != 0) {
            $this->error = new Error([
                'code'    => curl_errno($curlHandle),
                'message' => curl_error($curlHandle),
            ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Response::setContent
     *
     * Sets response content manualy.
     *
     * @param string $content
     *
     * @return static
     */
    public function setContent($content)
    {
        $this->string = $content;
        $this->fetchHeader($content);
        $this->fetchBody($content);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Response::fetchHeader
     *
     * Fetch response header.
     *
     * @param string $response
     */
    protected function fetchHeader($response)
    {
        $headers = [];
        $headerSize = 0;
        $headerParts = explode(PHP_EOL, $response);

        foreach ($headerParts as $headerString) {

            $headerSize += strlen($headerString);

            $headerString = trim($headerString);
            if (empty($headerString)) {
                break;
            }

            if (strpos($headerString, ':') !== false) {
                $headerSize += strlen(PHP_EOL);
                $headerString = str_replace('"', '', $headerString);
                $headerStringParts = explode(':', $headerString);
                $headerStringParts = array_map('trim', $headerStringParts);

                $headers[ $headerStringParts[ 0 ] ] = $headerStringParts[ 1 ];
            } elseif (preg_match("/(HTTP\/[0-9].[0-9])\s([0-9]+)\s([a-zA-Z]+)/", $headerString, $matches)) {
                $headerSize += strlen(PHP_EOL);

                $this->info->httpVersion = $matches[ 1 ];
                $this->info->httpCode = $matches[ 2 ];
                $this->info->httpCodeDescription = $matches[ 3 ];
            }
        }

        $this->headers = new Headers($headers);

        // Update info
        if ($this->headers->offsetExists('contentType')) {
            $this->info->contentType = $this->headers->offsetGet('contentType');
        }

        $this->info->headerSize = $headerSize;
    }

    // ------------------------------------------------------------------------

    /**
     * Response::fetchBody
     *
     * Fetch response body.
     *
     * @param string $response
     */
    protected function fetchBody($response)
    {
        $body = substr($response, $this->info->headerSize);
        $body = trim($body);
        $jsonBody = json_decode($body, true);

        if (is_array($jsonBody) AND json_last_error() === JSON_ERROR_NONE) {
            $this->body = new SimpleJSONElement($jsonBody);
        } elseif (strpos($body, '?xml') !== false) {
            $this->body = new SimpleXMLElement($body);
        } elseif (strpos($body, '!DOCTYPE') !== false or strpos($body, '!doctype') !== false) {
            $DomDocument = new \DOMDocument();
            $DomDocument->loadHTML($body);
            $this->body = $DomDocument;
        } elseif (false !== ($serializeArray = unserialize($body))) {
            $this->body = new SimpleSerializeElement($serializeArray);
        } else {
            parse_str($body, $queryString);

            if (isset($queryString[ 0 ])) {
                $this->body = $body;
            } else {
                $this->body = new SimpleQueryElement($queryString);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Response::getInfo
     *
     * Gets response info object.
     *
     * @return \O2System\Curl\Response\Info
     */
    public function getInfo()
    {
        return $this->info;
    }

    // ------------------------------------------------------------------------

    /**
     * Response::setInfo
     *
     * Sets response info manualy.
     *
     * @param array $info
     *
     * @return static
     */
    public function setInfo(array $info)
    {
        $this->info = new Info($info);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Response::getHeaders
     *
     * Gets response headers object.
     *
     * @return \O2System\Curl\Response\Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    // ------------------------------------------------------------------------

    /**
     * Response::getBody
     *
     * Gets response body.
     *
     * @return SimpleJSONElement|SimpleQueryElement|SimpleXMLElement|SimpleSerializeElement|\DOMDocument|string
     */
    public function getBody()
    {
        return $this->body;
    }

    // ------------------------------------------------------------------------

    /**
     * Response::getError
     *
     * Gets response error.
     *
     * @return Error|false
     */
    public function getError()
    {
        if ($this->error instanceof Error) {
            return $this->error;
        }

        return false;
    }
}