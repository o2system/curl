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

namespace O2System\Curl\Response;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Headers
 *
 * @package O2System\Curl\Response
 */
class Headers extends SplArrayObject
{
    /**
     * Headers::__construct
     *
     * @param array $headers Array of curl headers.
     */
    public function __construct(array $headers)
    {
        foreach ($headers as $key => $value) {

            if (strpos($key, '_') !== false or strpos($key, '-') !== false) {
                $headers[ camelcase($key) ] = $value;
                unset($headers[ $key ]);
            }
        }

        parent::__construct($headers);
    }
}