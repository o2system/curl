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
 * Class Error
 *
 * @package O2System\Libraries\CURL\Datastructures
 */
class Error extends SplArrayObject
{
    /**
     * Error constructor.
     *
     * @access public
     */
    public function __construct ()
    {
        parent::__construct(
            [
                'code'    => 444,
                'message' => o2system()->request->response->header->getStatusDescription( 444 ),
            ]
        );
    }

    // ------------------------------------------------------------------------

    /**
     * Set Error Code
     *
     * @access public
     *
     * @param int $code
     */
    public function setCode ( $code )
    {
        $this->__set( 'code', $code );
    }

    // ------------------------------------------------------------------------

    /**
     * Set Error Message
     *
     * @access public
     *
     * @param string $message
     */
    public function setMessage ( $message )
    {
        $this->__set( 'message', $message );
    }
}