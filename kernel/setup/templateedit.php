<?php
/**
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

// Redirect to visual module which is the correct place for this functionality
$module = $Params['Module'];
$parameters = $Params["Parameters"];

$visualModule = eZModule::exists( 'visual' );
if( $visualModule )
{
    return $module->forward( $visualModule, 'templateedit', $parameters );
}

?>
