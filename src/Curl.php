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

namespace O2System\Libraries;

// ------------------------------------------------------------------------

use O2System\Core\Exceptions\ConfigException;
use O2System\Core\Exceptions\PHPModuleException;
use O2System\Core\View\Exceptions\FileNotFoundException;
use O2System\Libraries\CURL\Handlers\Requests;
use O2System\Libraries\CURL\Handlers\Response;
use O2System\Libraries\CURL\Metadata\Error;
use O2System\Libraries\CURL\Metadata\Info;

/**
 * CURL Library
 *
 * @package          o2curl
 * @subpackage
 * @category         bootstrap
 * @version          1.0
 * @author           O2System Developer Team
 * @copyright        Copyright (c) 2005 - 2014
 * @license          http://circle-creative.com/products/o2curl/license.html
 * @link             http://circle-creative.com
 */
class CURL
{
    protected $httpVersion = CURL_HTTP_VERSION_1_1;

    /**
     * CURL Timeout
     *
     * @access  protected
     * @type    int
     */
    protected $timeout = 5;

    /**
     * CURL Verify Peer
     *
     * @access  protected
     * @type    bool
     */
    protected $verifyPeer = false;

    /**
     * CURL Verify Host
     *
     * @access  protected
     * @type    int
     */
    protected $verifyHost = 0;

    /**
     * CURL Certificate Info
     *
     * @access  protected
     * @type    string
     */
    protected $certificateInfo = null;

    /**
     * CURL Response Encoding Type
     *
     * @access  protected
     * @type    string
     */
    protected $encoding = 'gzip';

    /**
     * CURL Response Max Redirects
     *
     * @access  protected
     * @type    string
     */
    protected $maxRedirects = 10;

    /**
     * CURL Auth
     *
     * @access  protected
     * @type    array
     */
    protected $auth = [
        'user'   => null,
        'pass'   => null,
        'method' => CURLAUTH_BASIC,
    ];

    /**
     * CURL Proxy
     *
     * @access  protected
     * @type    array
     */
    protected $proxy  = [
        'port'    => false,
        'tunnel'  => false,
        'address' => false,
        'type'    => CURLPROXY_HTTP,
        'auth'    => [
            'user'   => null,
            'pass'   => null,
            'method' => CURLAUTH_BASIC,
        ],
    ];

    protected $cookie = null;

    /**
     * CURL User Agent
     *
     * @access  protected
     * @type    string
     */
    protected $userAgent = 'O2System\Libraries\CURL\v1.0';

    /**
     * CURL Headers
     *
     * @access  protected
     * @type    array
     */
    protected $headers = [ ];

    /**
     * CURL Options
     *
     * @access  protected
     * @type    array
     */
    protected $options = [ ];

    /**
     * CURL Handle
     *
     * @access  protected
     * @type    Resource
     */
    protected $handle = null;

    /**
     * CURL Response
     *
     * @access  protected
     * @type    Response
     */
    protected $response;

    // ------------------------------------------------------------------------

    /**
     * CURL constructor.
     *
     * @throws \O2System\Core\Exceptions\PHPModuleException
     */
    public function __construct ()
    {
        if ( ! function_exists( 'curl_init' ) ) {
            throw new PHPModuleException( 'CURL_MODULENOTFOUND' );
        }
    }

    /**
     * Set Timeout
     *
     * @param $timeout
     *
     * @return $this
     */
    public function setTimeout ( $timeout )
    {
        $this->timeout = (int) $timeout;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set Max Redirects
     *
     * @param $maxRedirects
     */
    public function setMaxRedirects ( $maxRedirects )
    {
        $this->maxRedirects = (int) $maxRedirects;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set Verify CURL SSL
     *
     * @param bool $peer
     * @param int  $host
     * @param null $certificateInfo
     *
     * @return $this
     */
    public function setVerify ( $peer = true, $host = 2, $certificateInfo = null )
    {
        $this->verifyPeer = $peer;
        $this->verifyHost = is_int( $host ) ? $host : 2;

        if ( isset( $certificateInfo ) ) {
            $this->certificateInfo = $certificateInfo;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set CURL Response Encoding
     *
     * @param string $encoding
     *
     * @return \O2System\Libraries\CURL
     */
    public function setEncoding ( $encoding )
    {
        $this->encoding = $encoding;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set authentication method to use
     *
     * @param string     $username Authentication Username
     * @param string     $password Authentication Password
     * @param int|string $method   Authentication Method
     *
     * @return \O2System\Libraries\CURL
     */
    public function setAuth ( $username = '', $password = '', $method = CURLAUTH_BASIC )
    {
        $this->auth[ 'user' ] = $username;
        $this->auth[ 'pass' ] = $password;
        $this->auth[ 'method' ] = $method;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set proxy to use
     *
     * @param string      $address Proxy Address
     * @param int|string  $port    Proxy Port
     * @param int|string  $type    Proxy Type (Available options for this are CURLPROXY_HTTP, CURLPROXY_HTTP_1_0
     *                             CURLPROXY_SOCKS4, CURLPROXY_SOCKS5, CURLPROXY_SOCKS4A and
     *                             CURLPROXY_SOCKS5_HOSTNAME)
     * @param bool|string $tunnel  Enable/Disable Tunneling
     *
     * @return Curl
     */
    public function setProxy ( $address, $port = 1080, $type = CURLPROXY_HTTP, $tunnel = false )
    {
        $this->proxy[ 'type' ] = $type;
        $this->proxy[ 'port' ] = $port;
        $this->proxy[ 'tunnel' ] = $tunnel;
        $this->proxy[ 'address' ] = $address;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set a new default header to send on every request
     *
     * @param string $key   header name
     * @param string $value header value
     *
     * @return \O2System\Libraries\CURL
     */
    public function setHeader ( $key, $value )
    {
        $this->headers[ $key ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setUserAgent ( $userAgent )
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set COOKIE request to a URL
     *
     * @param string $url     URL to send the TRACE request to
     * @param string $path
     * @param array  $params
     * @param array  $headers additional headers to send
     *
     * @return Response
     * @throws \Exception
     */
    public function setCookie ( array $cookie )
    {
        foreach ( $cookie as $key => $value ) {
            $cookies[] = $key . '=' . $value;
        }

        $this->cookie = implode( '; ', $cookies );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Reset Headers
     *
     * @access  public
     * @return \O2System\Libraries\CURL
     */
    public function resetHeaders ()
    {
        $this->headers = [ ];

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set CURL Options
     *
     * @param   array $options
     *
     * @access  public
     * @return \O2System\Libraries\CURL
     */
    public function setOptions ( array $options )
    {
        $this->options = $options;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Set CURL Option
     *
     * @param   int   $index
     * @param   mixed $value
     *
     * @access  public
     * @return \O2System\Libraries\CURL
     */
    public function setOption ( $index, $value )
    {
        $this->options[ $index ] = $value;

        return $this;
    }

    /**
     * Set File
     *
     * @param   string $filename
     * @param   string $mimeType
     * @param   string $postname
     *
     * @return \CURLFile|string
     */
    public function setFile ( $filename, $mimeType = '', $postname = '' )
    {
        if ( function_exists( 'curl_file_create' ) ) {
            return curl_file_create( $filename, $mimeType = '', $postname = '' );
        } else {
            return sprintf( '@%s;filename=%s;type=%s', $filename, $postname ? : basename( $filename ), $mimeType );
        }
    }

    /**
     * Magic Method __call
     *
     * @param       $method
     *
     * @param array $args
     *
     * @return mixed|Response
     *
     * @throws ConfigException
     *
     * @throws FileNotFoundException
     */
    public function __call ( $method, $args = [ ] )
    {
        if ( strpos( $method, 'set' ) !== false ) {
            return call_user_func_array( [ $this, $method ], $args );
        } elseif ( in_array(
            strtoupper( $method ),
            [ 'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD', 'TRACE', 'CONNECT' ]
        ) ) {
            @list( $url, $params, $initOnly ) = $this->__parseArgs( $args );

            $params = empty( $params ) ? [ ] : $params;
            $initOnly = empty( $initOnly ) ? false : (bool) $initOnly;

            return $this->request( $url, $params, strtoupper( $method ), $initOnly );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Parse Args
     *
     * @access private
     *
     * @param array $args
     *
     * @return array
     *
     * @throws ConfigException
     */
    private function __parseArgs ( array $args )
    {
        @list( $url, $path, $params, $headers, $initOnly ) = $args;

        if ( empty( $url ) ) {
            throw new ConfigException( 'CURL_URLNOTSET' );
        } elseif ( is_string( $path ) ) {
            $url = $this->__parseUrl( $url, $path );
        } elseif ( is_array( $path ) ) {
            if ( is_numeric( key( $path ) ) ) {
                $url = $this->__parseUrl( $url, implode( '/', $path ) );
                $path = null;
            } else {
                $url = $this->__parseUrl( $url );
                $params = $path;
                $path = null;
            }
        }

        if ( isset( $headers ) AND is_bool( $headers ) ) {
            $initOnly = $headers;
            $headers = $params;
        } elseif ( isset( $params ) AND is_bool( $params ) ) {
            $initOnly = $params;
            $params = [ ];
        }

        if ( is_array( $headers ) ) {
            $this->setHeaders( $headers );
        }

        return [ $url, $params, $initOnly ];
    }

    // ------------------------------------------------------------------------

    /**
     * Parse Url
     *
     * @param      $url
     *
     * @param null $path
     *
     * @return string
     */
    private function __parseUrl ( $url, $path = null )
    {
        $parseURL = parse_url( $url );

        if ( isset( $parseURL[ 'query' ] ) ) {
            parse_str( $parseURL[ 'query' ], $parseURL[ 'query' ] );
        } else {
            $parseURL[ 'query' ] = [ ];
        }

        if ( isset( $path ) ) {
            $parsePath = parse_url( $path );

            if ( isset( $parsePath[ 'query' ] ) ) {
                parse_str( $parsePath[ 'query' ], $parsePath[ 'query' ] );
                $parseURL[ 'query' ] = array_merge( $parseURL[ 'query' ], $parsePath[ 'query' ] );
            }

            $parseURL[ 'path' ] = rtrim( $parseURL[ 'path' ], '/' ) . '/' . $parsePath[ 'path' ];
        }

        $parseURL[ 'query' ] = empty( $parseURL[ 'query' ] )
            ? null
            : '?' . http_build_query(
                $parseURL[ 'query' ],
                null,
                '&',
                PHP_QUERY_RFC3986
            );

        return $parseURL[ 'scheme' ] . '://' . $parseURL[ 'host' ] . $parseURL[ 'path' ] . $parseURL[ 'query' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Set default headers to send on every request
     *
     * @param array $headers headers array
     *
     * @return  \O2System\Libraries\CURL
     */
    public function setHeaders ( array $headers = [ ] )
    {
        $this->headers = array_merge( $this->headers, $headers );

        return $this;
    }

    /**
     * Make an HTTP Request
     *
     * @param       $url
     *
     * @param array $params
     *
     * @param       $method
     *
     * @param bool  $init_only
     *
     * @return $this|Response
     *
     * @throws ConfigException
     *
     * @throws FileNotFoundException
     */
    public function request ( $url, array $params = [ ], $method, $init_only = false )
    {
        if ( empty( $url ) ) {
            throw new ConfigException( 'CURL_URLNOTSET' );
        }

        //$this->_options[ CURLOPT_VERBOSE ]        = TRUE;
        $this->options[ CURLOPT_URL ] = $url;
        $this->options[ CURLOPT_HTTP_VERSION ] = $this->httpVersion;
        $this->options[ CURLOPT_TIMEOUT ] = $this->timeout;
        $this->options[ CURLOPT_CONNECTTIMEOUT ] = $this->timeout;
        $this->options[ CURLOPT_MAXREDIRS ] = $this->maxRedirects;
        $this->options[ CURLOPT_RETURNTRANSFER ] = true;
        $this->options[ CURLOPT_SSL_VERIFYPEER ] = $this->verifyPeer;
        $this->options[ CURLOPT_SSL_VERIFYHOST ] = $this->verifyHost;
        $this->options[ CURLOPT_USERAGENT ] = $this->userAgent;
        $this->options[ CURLOPT_ENCODING ] = $this->encoding;
        $this->options[ CURLOPT_HEADER ] = true;

        if ( isset( $this->certificateInfo ) ) {
            $this->options[ CURLOPT_CAINFO ] = $this->certificateInfo;
        }

        if ( count( $this->headers ) > 0 ) {
            $this->options[ CURLOPT_HEADER ] = true;
            $this->options[ CURLOPT_HTTPHEADER ] = $this->__buildHeaders();
        }

        if ( isset( $this->auth[ 'user' ] ) ) {
            $this->options[ CURLOPT_HTTPAUTH ] = $this->auth[ 'method' ];
            $this->options[ CURLOPT_USERPWD ] = $this->auth[ 'user' ] . ':' . $this->auth[ 'pass' ];
        }

        if ( $this->proxy[ 'address' ] !== false ) {
            $this->options[ CURLOPT_PROXYTYPE ] = $this->proxy[ 'type' ];
            $this->options[ CURLOPT_PROXY ] = $this->proxy[ 'address' ];
            $this->options[ CURLOPT_PROXYPORT ] = $this->proxy[ 'port' ];
            $this->options[ CURLOPT_HTTPPROXYTUNNEL ] = $this->proxy[ 'tunnel' ];
            $this->options[ CURLOPT_PROXYAUTH ] = $this->proxy[ 'auth' ][ 'method' ];
            $this->options[ CURLOPT_PROXYUSERPWD ] = $this->proxy[ 'auth' ][ 'user' ] . ':' . $this->proxy[ 'auth' ][ 'pass' ];
        }

        if ( isset( $this->cookie ) ) {
            $this->options[ CURLOPT_COOKIE ] = $this->cookie;
        }

        switch ( $method ) {
            case 'GET':
                break;

            case 'POST':
                $this->options[ CURLOPT_POST ] = true;
                $this->options[ CURLOPT_POSTFIELDS ] = $this->__buildQuery( $params );
                break;

            case 'DELETE':
                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'DELETE';

                if ( ! isset( $this->options[ CURLOPT_USERPWD ] ) ) {
                    $this->options[ CURLOPT_USERPWD ] = 'anonymous:user';
                }
                break;

            case 'PUT':
                if ( ! is_file( $params[ 'filename' ] ) ) {
                    throw new FileNotFoundException( 'CURL_FILENOTFOUND' );
                }

                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'PUT';
                $this->options[ CURLOPT_PUT ] = true;
                $this->options[ CURLOPT_INFILESIZE ] = filesize( $params[ 'filename' ] );
                $this->options[ CURLOPT_INFILE ] = fopen( $params[ 'filename' ], 'r' );

                if ( ! isset( $this->options[ CURLOPT_USERPWD ] ) ) {
                    $this->options[ CURLOPT_USERPWD ] = 'anonymous:user';
                }
                break;

            case 'HEAD':
                $this->options[ CURLOPT_HTTPGET ] = true;
                $this->options[ CURLOPT_HEADER ] = true;
                $this->options[ CURLOPT_NOBODY ] = true;
                break;

            case 'TRACE':
                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'TRACE';
                break;

            case 'OPTIONS':
                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'OPTIONS';
                break;

            case 'DOWNLOAD':
                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'DOWNLOAD';
                $this->options[ CURLOPT_BINARYTRANSFER ] = true;
                $this->options[ CURLOPT_RETURNTRANSFER ] = false;
                break;

            case 'PATCH':
                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'PATCH';
                break;

            case 'CONNECT':
                $this->options[ CURLOPT_CUSTOMREQUEST ] = 'CONNECT';
                break;
        }

        if ( in_array( $method, [ 'GET', 'PUT', 'DELETE' ] ) AND ! empty( $params ) ) {
            $this->options[ CURLOPT_URL ] .= '?' . $this->__buildQuery( $params );
        }

        //print_out($this);

        $this->handle = curl_init();
        curl_setopt_array( $this->handle, $this->options );

        if ( $init_only === false ) {
            $response = curl_exec( $this->handle );
            $error = curl_error( $this->handle );
            $info = curl_getinfo( $this->handle );

            if ( $response === false ) {
                $this->response = json_encode( [ 'status' => [ 'code' => 403, 'description' => 'Bad request' ] ] );
            }

            curl_close( $this->handle );

            $this->response = new Response();
            $this->response->setMetadata( $info );
            $this->response->setBody( $response );

            if ( $error ) {
                $this->response->setError( 500, $error );
            } elseif ( $info[ 'http_code' ] !== 200 ) {
                $error = o2system()->request->response->header->getStatusDescription( $info[ 'http_code' ] );
                $this->response->setError( $info[ 'http_code' ], $error );
            }

            return $this->response;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Build Headers
     *
     * @return array
     */
    private function __buildHeaders ()
    {
        $formattedHeaders = [ ];

        foreach ( $this->headers as $key => $value ) {
            $key = trim( $key );

            if ( strpos( $key, '-' ) !== false ) {
                $xKey = explode( '-', $key );
                $key = implode( '-', array_map( 'ucfirst', $xKey ) );
            } else {
                $xKey = explode( ' ', $key );
                $key = implode( ' ', array_map( 'ucfirst', $xKey ) );
            }

            $formattedHeaders[] = $key . ': ' . trim( $value );
        }

        return $formattedHeaders;
    }

    // ------------------------------------------------------------------------

    /**
     * Build Query
     *
     * @param array $params
     *
     * @return string
     */
    private function __buildQuery ( array $params = [ ] )
    {
        return http_build_query( $params, null, '&', PHP_QUERY_RFC3986 );
    }

    // ------------------------------------------------------------------------

    /**
     * Multi Request Method
     *
     * @param Requests $requests
     *
     * @return Response
     */
    public function multiRequest ( Requests $requests )
    {
        $this->handle = curl_multi_init();
        $handles = [ ];

        foreach ( $requests as $curl ) {
            curl_multi_add_handle( $this->handle, $handles[] = $curl->getHandle() );
        }

        //execute the handles
        $active = null;
        do {
            $multiExec = curl_multi_exec( $this->handle, $active );
        }
        while ( $multiExec == CURLM_CALL_MULTI_PERFORM );

        while ( $active AND $multiExec == CURLM_OK ) {
            while ( curl_multi_exec( $this->handle, $active ) === CURLM_CALL_MULTI_PERFORM ) {
                ;
            }
        }

        foreach ( $handles as $handle ) {
            $content = curl_multi_getcontent( $handle );

            if ( $content ) {
                $response = new Response();
                $response->setMetadata( curl_getinfo( $handle ) );
                $response->setBody( $content );
                $response->setError( 500, curl_error( $handle ) );

                $this->response[] = $response;
            }

            curl_multi_remove_handle( $this->handle, $handle );
        }

        curl_multi_close( $this->handle );

        return $this->response;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Handle
     *
     * @return Resource
     */
    public function getHandle ()
    {
        return $this->handle;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Info
     *
     * @return mixed|Info
     */
    public function getInfo ()
    {
        if ( isset( $this->response->info ) ) {
            return $this->response->info;
        }

        return new Info();
    }

    // ------------------------------------------------------------------------

    /**
     * Get Error
     *
     * @return mixed|Error
     */
    public function getError ()
    {
        if ( isset( $this->response->error ) ) {
            return $this->response->error;
        }

        return new Error(
            [
                'code'    => 444,
                'message' => o2system()->request->response->header->getStatusDescription( 444 ),
            ]
        );
    }

    // ------------------------------------------------------------------------

    /**
     * Prepare URL
     *
     * @param        $url
     *
     * @param string $path
     *
     * @param array  $params
     *
     * @return string
     */
    public function prepareUrl ( $url, $path = '', array $params = [ ] )
    {
        if ( $path ) {
            if ( isset( $path[ 0 ] ) AND $path[ 0 ] === '/' ) {
                $path = substr( $path, 1 );
            }

            $url .= $path;
        }

        if ( ! empty( $params ) ) {
            // does it exist a query string?
            $queryString = parse_url( $url, PHP_URL_QUERY );
            if ( empty( $queryString ) ) {
                $url .= '?';
            } else {
                $url .= '&';
            }

            // it needs to be PHP_QUERY_RFC3986. We want to have %20 between scopes
            $url .= $this->__buildQuery( $params );
        }

        return $url;
    }
}