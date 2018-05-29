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
 * Class SimpleJSONElement
 *
 * @package O2System\Curl\Response
 */
class SimpleJSONElement extends SplArrayObject
{
    /**
     * SimpleJSONElement::__construct
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $key => $value) {
            if (strpos($key, '_') !== false) {
                $elements[ camelcase($key) ] = $value;
                unset($elements[ $key ]);
            }
        }

        parent::__construct($elements);
    }
}