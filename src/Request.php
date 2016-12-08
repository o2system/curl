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

use O2System\Core\Http\Message\Stream;
use O2System\Core\Http\Uri;
use O2System\Psr\Http\Message\RequestInterface;
use O2System\Psr\Http\Message\StreamInterface;
use O2System\Psr\Http\Message\UriInterface;

/**
 * Class Request
 *
 * @package O2System\Curl
 */
class Request implements RequestInterface
{
    /**
     * Request Method
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Request HTTP Protocol Version
     *
     * @var int
     */
    protected $protocolVersion = CURL_HTTP_VERSION_1_1;

    /**
     * Request Headers
     *
     * @var array
     */
    protected $headers = [ ];

    /**
     * Request Body
     *
     * @var Stream
     */
    protected $body;

    /**
     * Request Uri
     *
     * @var Uri
     */
    protected $uri;

    /**
     * Request Options
     *
     * @access  protected
     * @type    array
     */
    protected $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
    ];

    /**
     * Request::getProtocolVersion
     *
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion ()
    {
        $httpVersions = [
            CURL_HTTP_VERSION_NONE => '1.0',
            CURL_HTTP_VERSION_1_0  => '1.0',
            CURL_HTTP_VERSION_1_1  => '1.1',
        ];

        return $httpVersions[ $this->protocolVersion ];
    }

    // ------------------------------------------------------------------------

    /**
     * Request::withProtocolVersion
     *
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     *
     * @return static
     */
    public function withProtocolVersion ( $version )
    {
        $httpVersions = [
            CURL_HTTP_VERSION_NONE => '1.0',
            CURL_HTTP_VERSION_1_0  => '1.0',
            CURL_HTTP_VERSION_1_1  => '1.1',
        ];

        if ( false !== ( $protocolVersion = array_search( $version, $httpVersions ) ) ) {
            $this->protocolVersion = $protocolVersion;
        } elseif ( array_key_exists( $version, $httpVersions ) ) {
            $this->protocolVersion = $version;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::getHeaders
     *
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ': ' . implode(', ', $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers.
     *     Each key MUST be a header name, and each value MUST be an array of
     *     strings for that header.
     */
    public function getHeaders ()
    {
        return $this->headers;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::hasHeader
     *
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader ( $name )
    {
        return (bool) isset( $this->headers[ $name ] );
    }

    // ------------------------------------------------------------------------

    /**
     * Request::getHeaderLine
     *
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine ( $name )
    {
        if ( isset( $this->headers[ $name ] ) ) {
            $this->headers[ $name ];
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * Request::withAddedHeader
     *
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string          $name  Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     * @throws \InvalidArgumentException for invalid header names.
     * @throws \InvalidArgumentException for invalid header values.
     */
    public function withAddedHeader ( $name, $value )
    {
        $lines = $this->getHeader( $name );
        $value = array_map( 'trim', explode( ',', $value ) );

        $lines = array_merge( $lines, $value );

        return $this->withHeader( $name, implode( ', ', $lines ) );
    }

    // ------------------------------------------------------------------------

    /**
     * Request::getHeader
     *
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader ( $name )
    {
        $lines = [ ];

        if ( isset( $this->headers[ $name ] ) ) {
            $lines = array_map( 'trim', explode( ',', $this->headers[ $name ] ) );
        }

        return $lines;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::withHeader
     *
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name  Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader ( $name, $value )
    {
        $this->headers[ $name ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::withoutHeader
     *
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     *
     * @return static
     */
    public function withoutHeader ( $name )
    {
        if ( isset( $this->headers[ $name ] ) ) {
            unset( $this->headers[ $name ] );
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::getBody
     *
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody ()
    {
        return $this->body;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::withBody
     *
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     *
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody ( StreamInterface $body )
    {
        $request = clone $this;
        $request->body = $body;

        return $request;
    }

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::getRequestTarget
     *
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget ()
    {
        $requestTarget = '/';

        if ( empty( $this->target ) ) {
            if ( $this->uri instanceof Uri ) {
                $requestTarget = $this->uri->getPath();

                if ( null !== ( $query = $this->uri->getQuery() ) ) {
                    $requestTarget .= '?' . $query;
                }
            }
        }

        return $requestTarget;
    }

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::withRequestTarget
     *
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     *
     * @param mixed $requestTarget
     *
     * @return static
     */
    public function withRequestTarget ( $requestTarget )
    {
        $requestTarget = trim( $requestTarget );
        $parseTarget = parse_url( $requestTarget );

        $uri = $this->uri;

        if ( isset( $parseTarget[ 'path' ] ) ) {
            $uri = $this->uri->withPath( $parseTarget[ 'path' ] );
        }

        if ( isset( $parseTarget[ 'query' ] ) ) {
            $uri = $this->uri->withPath( $parseTarget[ 'query' ] );
        }

        $this->uri = $uri;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::getMethod
     *
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod ()
    {
        return $this->method;
    }

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::withMethod
     *
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     *
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod ( $method )
    {
        $method = strtoupper( $method );

        if ( in_array(
            $method,
            [
                'OPTIONS',
                'GET',
                'HEAD',
                'POST',
                'PUT',
                'DELETE',
                'TRACE',
                'CONNECT',
            ]
        ) ) {
            $this->method = $method;

            return $this;
        }

        throw new \InvalidArgumentException( 'Invalid HTTP Method' );
    }

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::getUri
     *
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return Uri Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri ()
    {
        return $this->uri;
    }

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::withUri
     *
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri          New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @return static
     */
    public function withUri ( UriInterface $uri, $preserveHost = false )
    {
        $this->uri = $uri;

        if ( $preserveHost ) {
            if ( null !== ( $host = $uri->getHost() ) ) {
                if ( null !== ( $port = $uri->getPort() ) ) {
                    $host .= ':' . $port;
                }

                $this->withHeader( 'Host', $host );
            }
        }

        return $this;
    }
}