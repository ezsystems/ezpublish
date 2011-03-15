<?php
/**
 * File containing the eZWordParser class.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZWordParser ezwordparser.php
  \ingroup eZKernel
  \brief The class eZWordParser handles parsing of Word files and returns the metadata

*/

class eZWordParser
{
    function parseFile( $fileName )
    {
        $binaryINI = eZINI::instance( 'binaryfile.ini' );

        $textExtractionTool = $binaryINI->variable( 'WordHandlerSettings', 'TextExtractionTool' );

        $tmpName = "var/cache/" . md5( time() ) . '.txt';
        $handle = fopen( $tmpName, "w" );
        fclose( $handle );

        $perm = octdec( eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) );
        chmod( $tmpName, $perm );

        exec( "$textExtractionTool $fileName > $tmpName", $ret );

        $metaData = "";
        if ( file_exists( $tmpName ) )
        {
            $fp = fopen( $tmpName, "r" );
            $metaData = fread( $fp, filesize( $tmpName ) );
            $metaData = strip_tags( $metaData );
            fclose( $fp );
            unlink( $tmpName );
        }

        return $metaData;
    }
}

?>
