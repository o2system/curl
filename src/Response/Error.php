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
 * Class Error
 *
 * @package O2System\Curl\Response
 */
class Error extends SplArrayObject
{
    /**
     * Error::__construct
     *
     * @param array $info Array of curl info.
     */
    public function __construct(array $info)
    {
        foreach ($info as $key => $value) {

            if (strpos($key, '_') !== false) {
                $info[ camelcase($key) ] = $value;
                unset($info[ $key ]);
            }
        }

        parent::__construct($info);
    }
}