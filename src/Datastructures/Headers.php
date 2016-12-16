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

namespace O2System\Libraries\CURL\Datastructures;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Headers
 *
 * @package O2System\Libraries\CURL\Datastructures
 */
class Headers extends SplArrayObject
{
    /**
     * Headers constructor.
     *
     * @param array $headers
     */
    public function __construct ( array $headers = [ ] )
    {
        if ( count( $headers ) > 0 ) {
            foreach ( $headers as $offset => $value ) {
                $this->offsetSet( $offset, $value );
            }
        }
    }

    // ------------------------------------------------------------------------
}