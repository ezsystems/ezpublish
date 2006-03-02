<?php
//
// Definition of eZShippingManager class
//
// Created on: <16-Feb-2006 23:02:53 vs>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.8.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file ezvatmanager.php
*/

/*!
  \class eZVATManager ezvatmanager.php
  \brief The class eZVATManager does

*/

class eZVATManager
{
    /*!
     Constructor
    */
    function eZVATManager()
    {
    }

    /**
     * Get percentage of VAT type corresponding to the given product and country the user is from.
     *
     * \return Percentage, or null on error.
     * \public
     * \static
     */
    function getVAT( $object, $country )
    {
        // Load VAT handler.
        if ( !is_object( $handler = eZVATManager::loadVATHandler() ) )
        {
            if ( $handler === true )
                eZDebug::writeWarning( "No VAT handler specified but dynamic VAT charging is used." );

            return null;
        }

        // Check if user country must be specified.
        $requireUserCountry = true;
        $shopINI =& eZINI::instance( 'shop.ini' );
        if ( $shopINI->hasVariable( 'VATSettings', 'RequireUserCountry' ) )
            $requireUserCountry = ( $shopINI->variable( 'VATSettings', 'RequireUserCountry' ) == 'true' );

        // Determine user country if it's not spefified
        if ( $country === false )
            $country = eZVATManager::getUserCountry( $requireUserCountry );

        if ( !$country && $requireUserCountry )
            return null;

        return $handler->getVatPercent( $object, $country );
    }

    /**
     * Determine user's country.
     *
     * \private
     * \static
     */
    function getUserCountry( $requireUserCountry, $userObject = false )
    {
        if ( $userObject === false )
        {
            require_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
            $user = eZUser::currentUser();
            $userObject = $user->attribute( 'contentobject' );
        }

        $ini =& eZINI::instance( 'shop.ini' );
        if ( !$ini->hasVariable( 'VATSettings', 'UserCountryAttribute' ) )
        {
            if ( $requireUserCountry )
            {
                eZDebug::writeError( "Cannot find user country: please specify its attribute identifier " .
                                     "in the following setting: shop.ini.[VATSettings].UserCountryAttribute",
                                     'eZVATManager::getUserCountry' );
            }
            return null;
        }

        $countryAttributeName = $ini->variable( 'VATSettings', 'UserCountryAttribute' );
        if ( !$countryAttributeName )
        {
            if ( $requireUserCountry )
            {
                eZDebug::writeError( "Cannot find user country: empty attribute name specified " .
                                     "in the following setting: shop.ini.[VATSettings].UserCountryAttribute",
                                     'eZVATManager::getUserCountry' );
            }

            return null;
        }

        $userDataMap = $userObject->attribute( 'data_map' );
        if ( !isset( $userDataMap[$countryAttributeName] ) )
        {
            if ( $requireUserCountry )
            {
                eZDebug::writeError( "Cannot find user country: there is no attribute '$countryAttributeName' in object '" .
                                       $userObject->attribute( 'name' ) .
                                       "' of class '" .
                                       $userObject->attribute( 'class_name' ) . "'.",
                                     'eZVATManager::getUserCountry' );
            }
            return null;
        }

        $countryAttribute = $userDataMap[$countryAttributeName];
        $country = $countryAttribute->attribute( 'content' );

        if ( $country === null )
        {
            if ( $requireUserCountry )
            {
                eZDebug::writeError( "User country is not specified in object '" .
                                       $object->attribute( 'name' ) .
                                       "' of class '" .
                                       $object->attribute( 'class_name' ) . "'." ,
                                     'eZVATManager::getUserCountry' );
            }
            return null;
        }

        return $country;
    }


    /*!
     \return true if a VAT handler is specified in the ini setting, false otherwise.
     */
    function isDynamicVatChargingEnabled()
    {
        if ( isset( $GLOBALS['eZVATManager_isDynamicVatChargingEnabled'] ) )
            return $GLOBALS['eZVATManager_isDynamicVatChargingEnabled'];

        $enabled = is_object( eZVATManager::loadVATHandler() );
        $GLOBALS['eZVATManager_isDynamicVatChargingEnabled'] = $enabled;
        return $enabled;
    }

    /*!
     Load VAT handler (if specified).

     \private
     \static
     \return true if no handler specified,
             false if a handler specified but could not be loaded,
             handler object if handler specified and found.
     */
    function loadVATHandler()
    {
        // FIXME: cache loaded handler.

        $shopINI =& eZINI::instance( 'shop.ini' );

        if ( !$shopINI->hasVariable( 'VATSettings', 'Handler' ) )
            return true;

        $handlerName = $shopINI->variable( 'VATSettings', 'Handler' );
        $repositoryDirectories = $shopINI->variable( 'VATSettings', 'RepositoryDirectories' );
        $extensionDirectories = $shopINI->variable( 'VATSettings', 'ExtensionDirectories' );

        $baseDirectory = eZExtension::baseDirectory();
        foreach ( $extensionDirectories as $extensionDirectory )
        {
            $extensionPath = $baseDirectory . '/' . $extensionDirectory . '/vathandlers';
            if ( file_exists( $extensionPath ) )
                $repositoryDirectories[] = $extensionPath;
        }

        $foundHandler = false;
        foreach ( $repositoryDirectories as $repositoryDirectory )
        {
            $includeFile = "$repositoryDirectory/{$handlerName}vathandler.php";

            if ( file_exists( $includeFile ) )
            {
                $foundHandler = true;
                break;
            }
        }

        if ( !$foundHandler )
        {
            eZDebug::writeError( "VAT handler '$handlerName' not found, " .
                                 "searched in these directories: " .
                                 implode( ', ', $repositoryDirectories ),
                                 'eVATManager::loadVATHandler' );
            return false;
        }

        require_once( $includeFile );
        $className = $handlerName . 'VATHandler';
        return new $className;
    }
}
?>
