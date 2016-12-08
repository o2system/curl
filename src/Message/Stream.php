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

namespace O2System\Curl\Message;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\StreamInterface;

/**
 * Class Stream
 *
 * @package O2System\Curl\Message
 */
class Stream implements StreamInterface
{
    /**
     * Stream Resource
     *
     * @var resource
     */
    public $context;

    /**
     * Stream::__construct
     */
    public function __construct ()
    {
        $this->context = fopen( 'php://temp', 'r+' );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::__toString
     *
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString ()
    {
        $this->seek( 0 );

        return (string) stream_get_contents( $this->context );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::seek
     *
     * Seek to a position in the stream.
     *
     * @see http://www.php.net/manual/en/function.fseek.php
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical to the built-in
     *                    PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *                    offset bytes SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset.
     *
     * @throws \RuntimeException on failure.
     */
    public function seek ( $offset, $whence = SEEK_SET )
    {
        if ( fseek( $this->context, $offset, $whence ) == -1 ) {
            throw new \RuntimeException( 'Not seekable' );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::close
     *
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close ()
    {
        if ( is_resource( $this->context ) ) {
            fclose( $this->context );
        }

        $this->detach();
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::detach
     *
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach ()
    {
        $result = clone $this->context;
        $this->context = null;

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::getSize
     *
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize ()
    {
        $stats = fstat( $this->context );

        if ( isset( $stats[ 'size' ] ) ) {
            return (int) $stats[ 'size' ];
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::tell
     *
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell ()
    {
        if ( false !== ( $position = ftell( $this->context ) ) ) {
            return $position;
        }

        throw new \RuntimeException( 'cannot find pointer' );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::eof
     *
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof ()
    {
        return feof( $this->context );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::isSeekable
     *
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable ()
    {
        return (bool) $this->getMetadata( 'seekable' );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::getMetadata
     *
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string $key Specific metadata to retrieve.
     *
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata ( $key = null )
    {
        $metadata = stream_get_meta_data( $this->context );

        if ( empty( $key ) ) {
            return $metadata;
        } elseif ( isset( $metadata[ $key ] ) ) {
            return $metadata[ $key ];
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::rewind
     *
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @see http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind ()
    {
        if ( rewind( $this->context ) === false ) {
            throw new \RuntimeException( 'Cannot rewind stream' );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::isWritable
     *
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable ()
    {
        if ( null !== ( $uri = $this->getMetadata( 'uri' ) ) ) {
            if ( is_writable( $uri ) ) {
                return true;
            } elseif ( null !== ( $mode = $this->getMetadata( 'mode' ) ) ) {
                preg_match_all( '/[a-z]\D?/', $mode, $matches );

                if ( isset( $matches[ 0 ] ) ) {
                    foreach ( $matches[ 0 ] as $match ) {
                        if ( in_array( $match, [ 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+' ] ) ) {
                            return true;

                            break;
                        }
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::write
     *
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     *
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write ( $string )
    {
        return fwrite( $this->context, $string );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::isReadable
     *
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable ()
    {
        if ( null !== ( $mode = $this->getMetadata( 'mode' ) ) ) {
            preg_match_all( '/[a-z]\D?/', $mode, $matches );

            if ( isset( $matches[ 0 ] ) ) {
                foreach ( $matches[ 0 ] as $match ) {
                    if ( in_array( $match, [ 'r', 'r+', 'w+', 'a+', 'x+', 'c+' ] ) ) {
                        return true;
                        break;
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::read
     *
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if underlying stream
     *                    call returns fewer bytes.
     *
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read ( $length )
    {
        if ( null !== ( $data = fread( $this->context, $length ) ) ) {
            return $data;
        }

        throw new \RuntimeException( 'Cannot read' );
    }

    // ------------------------------------------------------------------------

    /**
     * StreamInterface::getContents
     *
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read.
     * @throws \RuntimeException if error occurs while reading.
     */
    public function getContents ()
    {
        return $this->context ? stream_get_contents( $this->context ) : '';
    }
}