<?php
/**
 * File containing the internal_drafts_cleanup.php.php cronjob
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

/*! \file
*/

if ( !$isQuiet )
    $cli->output( "Cleaning up internal drafts..." );

// Remove all temporary internal drafts
$ini = eZINI::instance( 'content.ini' );
$internalDraftsCleanUpLimit = $ini->hasVariable( 'VersionManagement', 'InternalDraftsCleanUpLimit' ) ?
                                 $ini->variable( 'VersionManagement', 'InternalDraftsCleanUpLimit' ) : 0;
$durationSetting = $ini->hasVariable( 'VersionManagement', 'InternalDraftsDuration' ) ?
                      $ini->variable( 'VersionManagement', 'InternalDraftsDuration' ) : array( 'hours' => 24 ); // by default, only remove drafts older than 1 day

$isDurationSet = false;
$duration = 0;
if ( is_array( $durationSetting ) )
{
    if ( isset( $durationSetting[ 'days' ] ) and is_numeric( $durationSetting[ 'days' ] ) )
    {
        $duration += $durationSetting[ 'days' ] * 60 * 60 * 24;
        $isDurationSet = true;
    }
    if ( isset( $durationSetting[ 'hours' ] ) and is_numeric( $durationSetting[ 'hours' ] ) )
    {
        $duration += $durationSetting[ 'hours' ] * 60 * 60;
        $isDurationSet = true;
    }
    if ( isset( $durationSetting[ 'minutes' ] ) and is_numeric( $durationSetting[ 'minutes' ] ) )
    {
        $duration += $durationSetting[ 'minutes' ] * 60;
        $isDurationSet = true;
    }
    if ( isset( $durationSetting[ 'seconds' ] ) and is_numeric( $durationSetting[ 'seconds' ] ) )
    {
        $duration += $durationSetting[ 'seconds' ];
        $isDurationSet = true;
    }
}

if ( $isDurationSet )
{
    $expiryTime = time() - $duration;
    $processedCount = eZContentObjectVersion::removeVersions( eZContentObjectVersion::STATUS_INTERNAL_DRAFT, $internalDraftsCleanUpLimit, $expiryTime );

    if ( !$isQuiet )
        $cli->output( "Cleaned up " . $processedCount . " internal drafts" );
}
else
{
    if ( !$isQuiet )
        $cli->output( "Lifetime is not set for internal drafts (see your ini-settings, content.ini, VersionManagement section)." );
}

?>
