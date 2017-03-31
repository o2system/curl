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

use O2System\Psr\Patterns\AbstractObjectRegistryPattern;

/**
 * Class MultiRequest
 *
 * @package O2System\Curl
 */
class MultiRequest extends AbstractObjectRegistryPattern
{
    /**
     * MultiRequest::$curlHandle
     *
     * Multi request curl handle.
     *
     * @var resource
     */
    protected $curlHandle;

    // ------------------------------------------------------------------------

    /**
     * MultiRequest::__construct
     */
    public function __construct()
    {
        $this->curlHandle = curl_multi_init();
    }

    /**
     * MultiRequest::getResponse
     *
     * Get response from multiple curl request.
     *
     * @return array
     */
    public function getResponse()
    {
        $curlRequests = $this->getIterator();
        $curlHandles = [];

        foreach ( $curlRequests as $request ) {
            curl_multi_add_handle( $this->curlHandle, $curlHandles[] = $request->getHandle() );
        }

        // Execute all requests
        $activeCurlHandle = null;

        do {
            $multiExec = curl_multi_exec( $this->curlHandle, $activeCurlHandle );
        } while ( $multiExec == CURLM_CALL_MULTI_PERFORM );

        while ( $activeCurlHandle and $multiExec == CURLM_OK ) {
            if ( curl_multi_select( $this->curlHandle ) != -1 ) {
                do {
                    $mrc = curl_multi_exec( $this->curlHandle, $activeCurlHandle );
                } while ( $mrc == CURLM_CALL_MULTI_PERFORM );
            }
        }

        $responses = [];

        foreach ( $curlHandles as $curlHandle ) {
            $response = curl_multi_getcontent( $curlHandle );

            $responses[] = ( new Response( $curlHandle ) )
                ->setInfo( curl_getinfo( $curlHandle ) )
                ->setContent( $response );

            curl_multi_remove_handle( $this->handle, $curlHandle );
        }

        curl_multi_close( $this->handle );

        return $responses;
    }

    // ------------------------------------------------------------------------

    /**
     * MultiRequest::isValid
     *
     * Checks if the object is a valid instance.
     *
     * @param object $object The object to be validated.
     *
     * @return bool Returns TRUE on valid or FALSE on failure.
     */
    protected function isValid( $object )
    {
        if ( $object instanceof Request ) {
            return true;
        }

        return false;
    }
}