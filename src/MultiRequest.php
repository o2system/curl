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

use O2System\Psr\Patterns\Structural\Provider\AbstractProvider;
use O2System\Psr\Patterns\Structural\Provider\ValidationInterface;

/**
 * Class MultiRequest
 *
 * @package O2System\Curl
 */
class MultiRequest extends AbstractProvider implements ValidationInterface
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
    public function get()
    {
        $responses = [];
        $handle = curl_multi_init();

        foreach ($this as $request) {
            if ($request instanceof Request) {
                curl_multi_add_handle($handle, $curlHandles[] = $request->getHandle());
            }
        }

        if ( ! empty($curlHandles)) {
            // execute the handles
            $running = null;
            do {
                curl_multi_exec($handle, $running);
            } while ($running > 0);


            // get content and remove handles
            foreach ($curlHandles as $curlHandle) {
                $responses[] = curl_multi_getcontent($curlHandle);
                curl_multi_remove_handle($handle, $curlHandle);
            }

        }

        // all done
        curl_multi_close($handle);

        return $responses;
    }

    // ------------------------------------------------------------------------

    /**
     * MultiRequest::validate
     *
     * Checks if the object is a valid instance.
     *
     * @param object $object The object to be validated.
     *
     * @return bool Returns TRUE on valid or FALSE on failure.
     */
    public function validate($object)
    {
        if ($object instanceof Request) {
            return true;
        }

        return false;
    }
}