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

namespace O2System\Libraries\CURL\Handlers;

// ------------------------------------------------------------------------

use O2System\Core\SPL\ArrayIterator;
use O2System\Libraries\CURL;

/**
 * Class Requests
 *
 * @package O2System\Libraries\CURL\Handlers
 */
class Requests extends ArrayIterator
{
    /**
     * Requests Offset Set
     *
     * @param string $index
     *
     * @param string $value
     */
    public function offsetSet ( $index, $value )
    {
        if ( $value instanceof CURL ) {
            parent::offsetSet( $index, $value );
        }
    }
}