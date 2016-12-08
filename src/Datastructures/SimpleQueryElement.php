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
 * Class SimpleQueryElement
 *
 * @package O2System\Libraries\CURL\Datastructures
 */
class SimpleQueryElement extends SplArrayObject
{
    /**
     * SimpleJSONElement constructor.
     *
     * @param array $elements
     */
    public function __construct ( array $elements = [ ] )
    {
        if ( count( $elements ) > 0 ) {
            foreach ( $elements as $offset => $value ) {
                $this->offsetSet( $offset, $value );
            }
        }
    }

    // ------------------------------------------------------------------------
}