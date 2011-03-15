#!/usr/bin/env php
<?php
/**
 * File containing the ezgeneratetranslationcache.php script.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

// Generate caches for translations
// file  bin/php/ezgeneratetranslationcache.php


/**************************************************************
* script initializing                                         *
***************************************************************/

require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "\n" .
                                                        "This script will generate caches for translations.\n" .
                                                        "Default usage: ./bin/php/ezgeneratetranslationcache -s setup\n" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true,
                                     'user' => true ) );
$script->startup();

$scriptOptions = $script->getOptions( "[ts-list:]",
                                      "",
                                      array( 'ts-list' => "A list of translations to generate caches for, for example 'rus-RU nor-NO'\n".
                                                          "By default caches for all translations will be generated" ),
                                      false,
                                      array( 'user' => true )
                                     );
$script->initialize();

/**************************************************************
* process options                                             *
***************************************************************/

//
// 'ts-list' option
//
$translations = isset( $scriptOptions['ts-list'] ) ? explode( ' ', $scriptOptions['ts-list'] ) : array();
$translations = eZTSTranslator::fetchList( $translations );


/**************************************************************
* do the work
***************************************************************/

$cli->output( $cli->stylize( 'blue', "Processing: " ), false );

$ini = eZINI::instance();

foreach( $translations as $translation )
{
    $cli->output( "$translation->Locale ", false );

    $ini->setVariable( 'RegionalSettings', 'Locale', $translation->Locale );
    eZTranslationCache::resetGlobals();

    $translation->load( '' );
}

$cli->output( "", true );

$script->shutdown( 0 );

?>
