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

namespace O2System\Libraries\CURL\Interfaces;

    // ------------------------------------------------------------------------

/**
 * CURL HTTP Method Registries
 *
 * http://www.iana.org/assignments/http-methods/http-methods.xhtml
 *
 * @package          o2curl
 * @subpackage       interfaces
 * @category         interfaces
 * @version          1.0
 * @author           O2System Developer Team
 * @copyright        Copyright (c) 2005 - 2014
 * @license          http://circle-creative.com/products/o2curl/license.html
 * @link             http://www.iana.org/assignments/http-methods/http-methods.xhtml
 */
interface Method
{
    // RFC7231
    const GET     = 'GET';

    const HEAD    = 'HEAD';

    const POST    = 'POST';

    const PUT     = 'PUT';

    const DELETE  = 'DELETE';

    const CONNECT = 'CONNECT';

    const OPTIONS = 'OPTIONS';

    const TRACE   = 'TRACE';

    // RFC3253
    const BASELINE = 'BASELINE';

    // RFC2068
    const LINK   = 'LINK';

    const UNLINK = 'UNLINK';

    // RFC3253
    const MERGE           = 'MERGE';

    const BASELINECONTROL = 'BASELINE-CONTROL';

    const MKACTIVITY      = 'MKACTIVITY';

    const VERSIONCONTROL  = 'VERSION-CONTROL';

    const REPORT          = 'REPORT';

    const CHECKOUT        = 'CHECKOUT';

    const CHECKIN         = 'CHECKIN';

    const UNCHECKOUT      = 'UNCHECKOUT';

    const MKWORKSPACE     = 'MKWORKSPACE';

    const UPDATE          = 'UPDATE';

    const LABEL           = 'LABEL';

    // RFC3648
    const ORDERPATCH = 'ORDERPATCH';

    // RFC3744
    const ACL = 'ACL';

    // RFC4437
    const MKREDIRECTREF     = 'MKREDIRECTREF';

    const UPDATEREDIRECTREF = 'UPDATEREDIRECTREF';

    // RFC4791
    const MKCALENDAR = 'MKCALENDAR';

    // RFC4918
    const PROPFIND  = 'PROPFIND';

    const LOCK      = 'LOCK';

    const UNLOCK    = 'UNLOCK';

    const PROPPATCH = 'PROPPATCH';

    const MKCOL     = 'MKCOL';

    const COPY      = 'COPY';

    const MOVE      = 'MOVE';

    // RFC5323
    const SEARCH = 'SEARCH';

    // RFC5789
    const PATCH = 'PATCH';

    // RFC5842
    const BIND   = 'BIND';

    const UNBIND = 'UNBIND';

    const REBIND = 'REBIND';
}
