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
 * Class Info
 *
 * @package O2System\Libraries\CURL\Datastructures
 */
class Info extends SplArrayObject
{
    /**
     * Info constructor.
     *
     * @param array $info
     */
    public function __construct ( array $info = [ ] )
    {
        if ( count( $info ) > 0 ) {
            foreach ( $info as $offset => $value ) {
                $this->offsetSet( $offset, $value );
            }
        }
    }

    // ------------------------------------------------------------------------
}