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

use O2System\Kernel\Http\Message\Uri;
use O2System\Psr\Http\Message\UriInterface;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadPhpExtensionCallException;

/**
 * Class Request
 *
 * @package O2System\Curl
 */
class Request
{
    /**
     * Request::$curlAutoClose
     *
     * Flag for automatic close the connection when it has finished processing
     * and not be pooled for reuse.
     *
     * @var bool
     */
    public $curlAutoClose = true;
    /**
     * Request::$uri
     *
     * Request uri instance.
     *
     * @var Uri
     */
    protected $uri;

    /**
     * Request::$curlOptions
     *
     * Request Curl handle options.
     *
     * @var array
     */
    protected $curlOptions;
    /**
     * Request::$curlHeaders
     *
     * Request Curl handle headers.
     *
     * @var array
     */
    protected $curlHeaders = [];

    // ------------------------------------------------------------------------

    /**
     * Request::__construct
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadPhpExtensionCallException
     */
    public function __construct()
    {
        if ( ! function_exists('curl_init')) {
            throw new BadPhpExtensionCallException('E_CURL_NOT_LOADED');
        }

        // default, TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        $this->curlOptions[ CURLOPT_RETURNTRANSFER ] = true;

        // default, TRUE to output verbose information.
        $this->curlOptions[ CURLOPT_VERBOSE ] = true;

        // default, TRUE to include the header in the output.
        $this->curlOptions[ CURLOPT_HEADER ] = true;

        // default, lets CURL decide which version to use.
        $this->curlOptions[ CURLOPT_HTTP_VERSION ] = CURL_HTTP_VERSION_NONE;

        // default, http user agent using o2system curl.
        $this->curlOptions[ CURLOPT_USERAGENT ] = 'Curl/1.0 (O2System PHP Framework 5.0.0)';

        // default, TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
        $this->curlOptions[ CURLOPT_AUTOREFERER ] = true;

        // default, FALSE to stop cURL from verifying the peer's certificate.
        $this->curlOptions[ CURLOPT_SSL_VERIFYPEER ] = false;

        // default, 0 to not check the names.
        $this->curlOptions[ CURLOPT_SSL_VERIFYHOST ] = 0;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setOptions
     *
     * Sets curl options.
     *
     * @see http://php.net/manual/en/function.curl-setopt.php
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setOption
     *
     * Sets custom curl option.
     *
     * @see http://php.net/manual/en/function.curl-setopt.php
     *
     * @param int   $option The curl option number.
     * @param mixed $value  The value of curl option.
     *
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->curlOptions[ $option ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setUri
     *
     * @param \O2System\Psr\Http\Message\UriInterface $uri
     *
     * @return static
     */
    public function setUri(UriInterface $uri)
    {
        $this->uri = $uri;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setHttpVersion
     *
     * Sets which http version to use (default, lets CURL decide which version to use).
     *
     * @param int $httpVersion Supported encodings are "CURL_HTTP_VERSION_NONE", "CURL_HTTP_VERSION_1_0",
     *                         "CURL_HTTP_VERSION_1_1", and "CURL_HTTP_VERSION_2".
     *
     * @return static
     */
    public function setHttpVersion($httpVersion)
    {
        if (in_array($httpVersion, [
                CURL_HTTP_VERSION_NONE,
                CURL_HTTP_VERSION_1_0,
                CURL_HTTP_VERSION_1_1,
                CURL_HTTP_VERSION_2,
            ]
        )) {
            $this->curlOptions[ CURLOPT_HTTP_VERSION ] = $httpVersion;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setUserAgent
     *
     * Sets the contents of the "User-Agent: " header to be used in a HTTP request.
     *
     * @param string $userAgent
     *
     * @return static
     */
    public function setUserAgent($userAgent)
    {
        $this->curlOptions[ CURLOPT_USERAGENT ] = trim($userAgent);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setReferer
     *
     * Sets the contents of the "Referer: " header to be used in a HTTP request.
     *
     * @param string $referer
     *
     * @return static
     */
    public function setReferer($referer)
    {
        $this->curlOptions[ CURLOPT_REFERER ] = trim($referer);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setEncoding
     *
     * Sets the contents of the "Accept-Encoding: " header. This enables decoding of the response.
     * Supported encodings are "identity", "deflate", and "gzip".
     * If an empty string, "", is set, a header containing all supported encoding types is sent.
     *
     * @param string $encoding Supported encodings are "identity", "deflate", and "gzip".
     *
     * @return static
     */
    public function setEncoding($encoding)
    {
        if (in_array($encoding, ['identity', 'deflate', 'gzip'])) {
            $this->curlOptions[ CURLOPT_ENCODING ] = $encoding;
        } else {
            $this->curlOptions[ CURLOPT_ENCODING ] = '';
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setTimeout
     *
     * Sets the maximum number of seconds to allow cURL functions to execute.
     *
     * @param int  $timeout        The number of seconds.
     * @param bool $isMilliseconds The number units is uses milliseconds format.
     *
     * @return static
     */
    public function setTimeout($timeout, $isMilliseconds = false)
    {
        if ($isMilliseconds) {
            $this->curlOptions[ CURLOPT_TIMEOUT_MS ] = (int)$timeout;
        } else {
            $this->curlOptions[ CURLOPT_TIMEOUT ] = (int)$timeout;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setConnectionTimeout
     *
     * Sets the number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
     *
     * @param int  $timeout        The number of seconds.
     * @param bool $isMilliseconds The number units is uses milliseconds format.
     *
     * @return static
     */
    public function setConnectionTimeout($timeout, $isMilliseconds = false)
    {
        if ($isMilliseconds) {
            $this->curlOptions[ CURLOPT_CONNECTTIMEOUT_MS ] = (int)$timeout;
        } else {
            $this->curlOptions[ CURLOPT_CONNECTTIMEOUT ] = (int)$timeout;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setMaximumRedirects
     *
     * Sets the maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
     *
     * @param string $maximum The numbers of maximum redirections.
     *
     * @return static
     */
    public function setMaximumRedirects($maximum)
    {
        $this->curlOptions[ CURLOPT_MAXREDIRS ] = (int)$maximum;
        $this->curlOptions[ CURLOPT_FOLLOWLOCATION ] = true;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setSslCaInfo
     *
     * Sets the name of a file holding one or more certificates to verify the peer with.
     * It's also set the curl options:
     * 1. CURLOPT_SSL_VERIFYPEER value into TRUE.
     * 2. CURLOPT_SSL_VERIFYHOST value into 2 to check the existence of a common name and also verify that it matches
     * the hostname provided.
     * 3. CURLOPT_SSL_VERIFYSTATUS value into TRUE to verify the certificate status.
     *
     * @param string $caInfoFilePath Path to ssl certificate file.
     *
     * @return static
     */
    public function setSslCaInfo($caInfoFilePath)
    {
        if (is_file($caInfoFilePath)) {
            $this->setSslVerify(2, true);
            $this->curlOptions[ CURLOPT_CAINFO ] = pathinfo($caInfoFilePath, PATHINFO_BASENAME);
            $this->curlOptions[ CURLOPT_CAPATH ] = dirname($caInfoFilePath);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setSslVerify
     *
     * Sets the SSL Verify Peer into TRUE.
     *
     * @param int  $verifyHost   0. To not check the names. In production environments the value of this option should
     *                           be kept at 2 (default value).
     *                           1. To check the existence of a common name in the SSL peer certificate.
     *                           2. To check the existence of a common name and also verify that it matches the
     *                           hostname provided.
     * @param bool $verifyStatus TRUE to verify the certificate's status.
     *
     * @return static
     */
    public function setSslVerify($verifyHost, $verifyStatus = false)
    {
        $this->curlOptions[ CURLOPT_SSL_VERIFYPEER ] = true;

        $verifyHost = in_array($verifyHost, range(0, 3)) ? $verifyHost : 0;
        $this->curlOptions[ CURLOPT_SSL_VERIFYHOST ] = (int)$verifyHost;
        $this->curlOptions[ CURLOPT_SSL_VERIFYSTATUS ] = (bool)$verifyStatus;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setAuthentication
     *
     * Sets the HTTP authentication method(s) to use.
     *
     * @param string     $username The HTTP authentication username.
     * @param string     $password The HTTP authentication password.
     * @param int|string $method   The HTTP authentication method. The options are:
     *                             1. CURLAUTH_BASIC
     *                             2. CURLAUTH_DIGEST
     *                             3. CURLAUTH_GSSNEGOTIATE
     *                             4. CURLAUTH_NTLM
     *                             5. CURLAUTH_ANY (default)
     *                             6. CURLAUTH_ANYSAFE
     *
     * @return static
     */
    public function setAuthentication($username = '', $password = '', $method = CURLAUTH_ANY)
    {
        if (defined('CURLOPT_USERNAME')) {
            $this->curlOptions[ CURLOPT_USERNAME ] = $username;
        }

        $this->curlOptions[ CURLOPT_USERPWD ] = "$username:$password";
        $this->curlOptions[ CURLOPT_HTTPAUTH ] = CURLAUTH_ANY;

        if (in_array($method, [
            CURLAUTH_BASIC,
            CURLAUTH_DIGEST,
            CURLAUTH_GSSNEGOTIATE,
            CURLAUTH_NTLM,
            CURLAUTH_ANY,
            CURLAUTH_ANYSAFE,
        ])) {
            $this->curlOptions[ CURLOPT_HTTPAUTH ] = $method;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setProxy
     *
     * Set the HTTP proxy to tunnel requests through.
     *
     * @param string     $address  The HTTP proxy address.
     * @param int|string $port     The HTTP proxy port.
     * @param int|string $type     The HTTP proxy type, available options:
     *                             1. CURLPROXY_HTTP
     *                             2. CURLPROXY_HTTP_1_0
     *                             3. CURLPROXY_SOCKS4
     *                             4. CURLPROXY_SOCKS5
     *                             5. CURLPROXY_SOCKS4A
     *                             6. CURLPROXY_SOCKS5_HOSTNAME
     *
     * @return static
     */
    public function setProxy($address, $port = 1080, $type = CURLPROXY_HTTP)
    {
        $this->curlOptions[ CURLOPT_PROXY ] = $address;
        $this->curlOptions[ CURLOPT_PROXYPORT ] = $port;
        $this->curlOptions[ CURLOPT_PROXYTYPE ] = $type;
        $this->curlOptions[ CURLOPT_HTTPPROXYTUNNEL ] = true;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setProxyAuthentication
     *
     * Sets the username and password to use for the connection to the proxy.
     *
     * @param string     $username The HTTP Proxy authentication username.
     * @param string     $password The HTTP Proxy authentication password.
     * @param int|string $method   The HTTP Proxy authentication method. The options are:
     *                             1. CURLAUTH_BASIC
     *                             2. CURLAUTH_NTLM
     *
     * @return static
     */
    public function setProxyAuthentication($username, $password, $method = CURLAUTH_BASIC)
    {
        if (array_key_exists(CURLOPT_HTTPPROXYTUNNEL, $this->curlOptions)) {
            $this->curlOptions[ CURLOPT_PROXYUSERPWD ] = "$username:$password";
        }

        $this->curlOptions[ CURLOPT_PROXYAUTH ] = CURLAUTH_BASIC;

        if (in_array($method, [
            CURLAUTH_BASIC,
            CURLAUTH_NTLM,
        ])) {
            $this->curlOptions[ CURLOPT_PROXYAUTH ] = $method;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setHeaders
     *
     * Sets curl request with headers.
     *
     * @param array $headers
     *
     * @return static
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::addHeader
     *
     * Add curl request header.
     *
     * @param string          $name  Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     */
    public function addHeader($name, $value)
    {
        $this->curlHeaders[ $name ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setCookie
     *
     * Sets the cookie contents to be used in the HTTP request.
     *
     * @param string $cookieFile The contents of the "Cookie: " header to be used in the HTTP request.
     *                           Note that multiple cookies are separated with a semicolon followed by a space
     *                           (e.g., "fruit=apple; colour=red")
     *
     * @return static
     */
    public function setCookie($cookie)
    {
        $this->curlOptions[ CURLOPT_COOKIE ] = $cookie;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::setCookieFile
     *
     * Sets the cookie file to be used in the HTTP request.
     *
     * @param string      $cookieFile The name of the file containing the cookie data.
     * @param string|null $cookieJar  The name of a file to save all internal cookies to when the handle is closed,
     *                                e.g. after a call to curl_close.
     *
     * @return static
     */
    public function setCookieFile($cookieFile, $cookieJar = null)
    {
        if (is_file($cookieFile)) {
            $cookieJar = empty($cookieJar) ? $cookieFile : $cookieJar;
            $this->curlOptions[ CURLOPT_COOKIEFILE ] = $cookieFile;
            $this->curlOptions[ CURLOPT_COOKIEJAR ] = $cookieJar;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::get
     *
     * Get response use HTTP GET request method.
     *
     * @param array $query Additional HTTP GET query.
     */
    public function get(array $query = [])
    {
        $this->uri = $this->uri->withQuery($query);

        $this->curlOptions[ CURLOPT_HTTPGET ] = true;

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::getResponse
     *
     * Get curl response.
     *
     * @return Response|bool
     */
    public function getResponse()
    {
        $handle = curl_init($this->uri->__toString());

        $headers = [];
        if (count($this->curlHeaders)) {
            foreach ($this->curlHeaders as $key => $value) {
                $headers[] = trim($key) . ': ' . trim($value);
            }

            $this->curlOptions[ CURLOPT_HTTPHEADER ] = $headers;
        }

        if (curl_setopt_array($handle, $this->curlOptions)) {
            $response = (new Response($handle))
                ->setInfo(curl_getinfo($handle))
                ->setContent(curl_exec($handle));

            if ($this->curlAutoClose) {
                curl_close($handle);
            }

            return $response;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Request::post
     *
     * Get response use HTTP POST request method.
     *
     * @param array $fields Additional HTTP POST fields.
     */
    public function post(array $fields = [])
    {
        $this->curlOptions[ CURLOPT_POST ] = true;
        $this->curlOptions[ CURLOPT_POSTFIELDS ] = http_build_query($fields, null, '&', PHP_QUERY_RFC3986);

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::delete
     *
     * Get response use custom HTTP DELETE request method.
     *
     * @param array $fields Additional HTTP POST fields.
     */
    public function delete(array $fields = [])
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'DELETE';

        if (count($fields)) {
            $this->curlOptions[ CURLOPT_POST ] = true;
            $this->curlOptions[ CURLOPT_POSTFIELDS ] = http_build_query($fields, null, '&', PHP_QUERY_RFC3986);
        }

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::head
     *
     * Get response use custom HTTP HEAD request method.
     */
    public function head()
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'HEAD';
        $this->curlOptions[ CURLOPT_HTTPGET ] = true;
        $this->curlOptions[ CURLOPT_HEADER ] = true;
        $this->curlOptions[ CURLOPT_NOBODY ] = true;

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::trace
     *
     * Get response use custom HTTP TRACE request method.
     */
    public function trace()
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'TRACE';

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::trace
     *
     * Get response use custom HTTP OPTIONS request method.
     */
    public function options()
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'OPTIONS';
    }

    // ------------------------------------------------------------------------

    /**
     * Request::patch
     *
     * Get response use custom HTTP PATCH request method.
     */
    public function patch()
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'PATCH';

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::connect
     *
     * Get response use custom HTTP CONNECT request method.
     */
    public function connect()
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'CONNECT';

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::download
     *
     * Get response use custom HTTP DOWNLOAD request method.
     */
    public function download()
    {
        $this->curlOptions[ CURLOPT_CUSTOMREQUEST ] = 'DOWNLOAD';
        $this->curlOptions[ CURLOPT_BINARYTRANSFER ] = true;
        $this->curlOptions[ CURLOPT_RETURNTRANSFER ] = false;

        return $this->getResponse();
    }

    // ------------------------------------------------------------------------

    /**
     * Request::getHandle
     *
     * Gets curl handle resource.
     *
     * @return resource
     */
    public function getHandle()
    {
        if ($this->curlAutoClose) {
            $this->curlOptions[ CURLOPT_FORBID_REUSE ] = true;
            $this->curlOptions[ CURLOPT_FRESH_CONNECT ] = true;
        }

        $this->curlOptions[ CURLOPT_URL ] = $this->uri->__toString();

        $handle = curl_init($this->uri->__toString());

        curl_setopt_array($handle, $this->curlOptions);

        return $handle;
    }
}