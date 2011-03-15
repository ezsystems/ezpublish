<?php
/**
 * File containing the eZGZIPCompressionHandler class.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package lib
 */

/*! \file
*/

/*!
  \class eZGZIPCompressionHandler ezgzipcompressionhandler.php
  \brief Handles files compressed with gzip

  This class is a wrapper of the eZGZIPZLIBCompressionHandler and
  eZGZIPShellCompressionHandler classes.

  Duplication of this handler is done by the eZForwardCompressionHandler class.
*/

class eZGZIPCompressionHandler extends eZForwardCompressionHandler
{
    /*!
     See eZCompressionHandler::eZCompressionHandler and eZForwardCompressionHandler::eZForwardCompressionHandler.
    */
    function eZGZIPCompressionHandler()
    {
        if ( eZGZIPZLIBCompressionHandler::isAvailable() )
            $handler = new eZGZIPZLIBCompressionHandler();
        else if ( eZGZIPShellCompressionHandler::isAvailable() )
            $handler = new eZGZIPShellCompressionHandler();
        else
            $handler = new eZNoCompressionHandler();
        $this->eZForwardCompressionHandler( $handler,
                                            'GZIP', 'gzip' );
    }

    /*!
     Forwards the compression level to the current handler.
    */
    function setCompressionLevel( $level )
    {
        $handler =& $this->handler();
        if ( method_exists( $handler, 'setCompressionLevel' ) )
            $handler->setCompressionLevel( $level );
    }

    /*!
     Forwards the request for compression level to the current handler and returns the value.
    */
    function compressionLevel()
    {
        $handler =& $this->handler();
        if ( method_exists( $handler, 'compressionLevel' ) )
            return $handler->compressionLevel();
        return false;
    }
}

?>
