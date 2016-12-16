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
use O2System\Libraries\CURL\Datastructures\Error;
use O2System\Libraries\CURL\Datastructures\Headers;
use O2System\Libraries\CURL\Datastructures\Info;
use O2System\Libraries\CURL\Datastructures\SimpleJSONElement;
use O2System\Libraries\CURL\Datastructures\SimpleQueryElement;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Response
 *
 * CURL Response Factory Class
 *
 * @package O2System\Libraries\CURL\Factory
 */
class Response extends SplArrayObject
{
    /**
     * Set Response Metadata
     *
     * @param   array $metadata
     *
     * @access  public
     */
    public function setMetadata ( array $metadata )
    {
        $this->info = new Info( $metadata );
    }

    // ------------------------------------------------------------------------
    /**
     * Set Response Body
     *
     * @param   string $body
     *
     * @access  public
     */
    public function setBody ( $body )
    {
        if ( is_string( $body ) ) {
            $this->body = (string) $body;
            $this->headers = $this->__parseHeaders( $body );
            $this->data = $this->__parseBody( $body );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * if PECL_HTTP is not available use a fall back function
     *
     * thanks to ricardovermeltfoort@gmail.com
     * http://php.net/manual/en/function.http-parse-headers.php#112986
     */
    private function __parseHeaders ( $body )
    {
        $rawHeaders = substr( $body, $this->info->headerSize );
        $rawHeaders = http_parse_headers( $body );
        $rawHeaders = empty( $rawHeaders ) ? [ ] : $rawHeaders;

        $headers = new Headers();

        foreach ( $rawHeaders as $key => $value ) {
            $key = $key === 0 ? 'http' : $key;

            $headers->offsetSet( $key, $value );
        }

        return $headers;
    }

    // ------------------------------------------------------------------------

    /**
     * Parse Response Body
     *
     * @param   string $rawBody
     *
     * @return  mixed
     */
    private function __parseBody ( $rawBody )
    {
        $contentType = $this->headers->offsetExists(
            'contentType'
        ) ? $this->headers->contentType : $this->info->contentType;
        $contentType = explode( ';', $contentType );
        $contentType = array_map( 'trim', $contentType );
        $contentType = reset( $contentType );
        $contentType = strtolower( $contentType );

        if ( ! empty( $contentType ) ) {
            if ( $contentType === 'application/json' ) {
                $jsonBody = json_decode( $rawBody, true );

                if ( json_last_error() === JSON_ERROR_NONE ) {
                    return new SimpleJSONElement( $jsonBody );
                }
            } elseif ( $contentType === 'application/xml' ) {
                return simplexml_load_string( $rawBody );
            }
        }

        $rawBody = trim( $rawBody );
        $substrRawBody = substr( $rawBody, $this->info->headerSize );

        $rawBody = empty( $substrRawBody ) ? $rawBody : $substrRawBody;

        $jsonBody = json_decode( $rawBody, true );

        if ( is_array( $jsonBody ) AND json_last_error() === JSON_ERROR_NONE ) {
            return new SimpleJSONElement( (array) $jsonBody );
        } else {
            parse_str( $rawBody, $queryString );

            if ( is_array( $queryString ) AND count( $queryString ) > 0 ) {
                return new SimpleQueryElement( (array) $queryString );
            } elseif ( ! empty( $rawBody ) ) {
                $DomDocument = new \DOMDocument();
                $DomDocument->loadHTML( $rawBody );

                return $DomDocument;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Set Error
     *
     * @param  int    $code
     *
     * @param  string $message
     */
    public function setError ( $code, $message )
    {
        $this->error = new Error();
        $this->error->setCode( $code );
        $this->error->setMessage( $message );
    }
}