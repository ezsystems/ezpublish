<?php
/**
 * File containing the eZImageShellFactory class.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package lib
 */

class eZImageShellFactory extends eZImageFactory
{
    /*!
     Initializes the factory with the name \c 'shell'
    */
    function eZImageShellFactory()
    {
        $this->eZImageFactory( 'shell' );
    }

    /*!
     Creates eZImageShellHandler objects and returns them.
    */
    static function produceFromINI( $iniGroup, $iniFilename = false )
    {
        return eZImageShellHandler::createFromINI( $iniGroup, $iniFilename );
    }
}

?>
