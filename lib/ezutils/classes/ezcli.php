<?php
//
// Definition of Command Line Interface functions
//
// Created on: <05-Aug-2003 13:00:00 amos>
//
// Copyright (C) 1999-2004 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file cli.php
*/

/*!
  \class eZCLI ezcli.php
  \brief CLI handling

  Provides functionality to work with the CLI (Command Line Interface).
  The CLI can be run from either a terminal (shell) or a web interface.

  A typical usage:
\code
$cli =& eZCLI::instance();

$cli->setUseStyles( true ); // enable colors

$cli->output( "This is a text string" );

\endcode

*/

include_once( 'lib/ezutils/classes/ezini.php' );
include_once( 'lib/ezutils/classes/ezdebug.php' );
include_once( 'lib/ezutils/classes/ezdebugsetting.php' );

define( 'EZ_CLI_TERMINAL_ENDOFLINE_STRING', "\n" );

class eZCLI
{
    /*!
     Initializes object and detects if the CLI is used.
    */
    function eZCLI()
    {
        $endl = "<br/>";
        $webOutput = true;
        if ( isset( $_SERVER['argv'] ) )
        {
            $endl = EZ_CLI_TERMINAL_ENDOFLINE_STRING;
            $webOutput = false;
        }
        $this->EndlineString = $endl;
        $this->WebOutput = $webOutput;
        $this->TerminalStyles = array( 'warning' => "\033[1;35m",
                                       'warning-end' => "\033[0;39m",
                                       'error' => "\033[1;31m",
                                       'error-end' => "\033[0;39m",
                                       'notice' => "\033[0;32m",
                                       'notice-end' => "\033[0;39m",
                                       'debug' => "\033[0;30m",
                                       'debug-end' => "\033[0;39m",
                                       'timing' => "\033[1;34m",
                                       'timing-end' => "\033[0;39m",
                                       'success' => "\033[1;32m",
                                       'success-end' => "\033[0;39m",
                                       'file' => "\033[1;38m",
                                       'file-end' => "\033[0;39m",
                                       'dir' => "\033[1;34m",
                                       'dir-end' => "\033[0;39m",
                                       'link' => "\033[0;36m",
                                       'link-end' => "\033[0;39m",
                                       'exe' => "\033[1;32m",
                                       'exe-end' => "\033[0;39m",
                                       'archive' => "\033[1;31m",
                                       'archive-end' => "\033[0;39m",
                                       'image' => "\033[1;35m",
                                       'image-end' => "\033[0;39m",

                                       'red' => "\033[1;31m",
                                       'red-end' => "\033[0;39m",
                                       'green' => "\033[1;32m",
                                       'green-end' => "\033[0;39m",
                                       'yellow' => "\033[1;33m",
                                       'yellow-end' => "\033[0;39m",
                                       'blue' => "\033[1;34m",
                                       'blue-end' => "\033[0;39m",
                                       'magenta' => "\033[1;35m",
                                       'magenta-end' => "\033[0;39m",
                                       'cyan' => "\033[1;36m",
                                       'cyan-end' => "\033[0;39m",
                                       'white' => "\033[1;37m",
                                       'white-end' => "\033[0;39m",
                                       'gray' => "\033[1;30m",
                                       'gray-end' => "\033[0;39m",

                                       'dark-red' => "\033[0;31m",
                                       'dark-red-end' => "\033[0;39m",
                                       'dark-green' => "\033[0;32m",
                                       'dark-green-end' => "\033[0;39m",
                                       'dark-yellow' => "\033[0;33m",
                                       'dark-yellow-end' => "\033[0;39m",
                                       'dark-blue' => "\033[0;34m",
                                       'dark-blue-end' => "\033[0;39m",
                                       'dark-magenta' => "\033[0;35m",
                                       'dark-magenta-end' => "\033[0;39m",
                                       'dark-cyan' => "\033[0;36m",
                                       'dark-cyan-end' => "\033[0;39m",
                                       'dark-white' => "\033[0;37m",
                                       'dark-white-end' => "\033[0;39m",
                                       'dark-gray' => "\033[0;30m",
                                       'dark-gray-end' => "\033[0;39m",

                                       'red-bg' => "\033[1;41m",
                                       'red-bg-end' => "\033[0;39m",
                                       'green-bg' => "\033[1;42m",
                                       'green-bg-end' => "\033[0;39m",
                                       'yellow-bg' => "\033[1;43m",
                                       'yellow-bg-end' => "\033[0;39m",
                                       'blue-bg' => "\033[1;44m",
                                       'blue-bg-end' => "\033[0;39m",
                                       'magenta-bg' => "\033[1;45m",
                                       'magenta-bg-end' => "\033[0;39m",
                                       'cyan-bg' => "\033[1;46m",
                                       'cyan-bg-end' => "\033[0;39m",
                                       'white-bg' => "\033[1;47m",
                                       'white-bg-end' => "\033[0;39m",

                                       'text' => "\033[0;39m",
                                       'text-end' => "\033[0;39m",
                                       'variable' => "\033[1;34m",
                                       'variable-end' => "\033[0;39m",
                                       'symbol' => "\033[0;37m",
                                       'symbol-end' => "\033[0;39m",
                                       'emphasize' => "\033[1;38m",
                                       'emphasize-end' => "\033[0;39m",
                                       'strong' => "\033[1;39m",
                                       'strong-end' => "\033[0;39m",
                                       'mark' => "\033[1;30m",
                                       'mark-end' => "\033[0;39m",
                                       'bold' => "\033[1;38m",
                                       'bold-end' => "\033[0;39m",
                                       'italic' => "\033[0;39m",
                                       'italic-end' => "\033[0;39m",
                                       'underline' => "\033[0;39m",
                                       'underline-end' => "\033[0;39m",
                                       'paragraph' => "\033[0;39m",
                                       'paragraph-end' => "\033[0;39m",
                                       'normal' => "\033[0;39m",
                                       'normal-end' => "\033[0;39m" );
        $this->WebStyles = array( 'warning' => "<font color=\"orange\">",
                                  'warning-end' => "</font>",
                                  'error' => "<font color=\"red\">",
                                  'error-end' => "</font>",
                                  'notice' => "<font color=\"green\">",
                                  'notice-end' => "</font>",
                                  'debug' => "<font color=\"brown\">",
                                  'debug-end' => "</font>",
                                  'timing' => "<font color=\"blue\">",
                                  'timing-end' => "</font>",
                                  'success' => "<font color=\"green\">",
                                  'success-end' => "</font>",
                                  'file' => "<i>",
                                  'file-end' => "</i>",
                                  'dir' => "<font color=\"blue\">",
                                  'dir-end' => "</font>",
                                  'link' => "<font color=\"cyan\">",
                                  'link-end' => "</font>",
                                  'symbol' => "<i>",
                                  'symbol-end' => "</i>",
                                  'emphasize' => "<i>",
                                  'emphasize-end' => "</i>",
                                  'strong' => "<b>",
                                  'strong-end' => "</b>",
                                  'mark' => "",
                                  'mark-end' => "",
                                  'bold' => "<b>",
                                  'bold-end' => "</b>",
                                  'italic' => "<i>",
                                  'italic-end' => "</i>",
                                  'underline' => "<u>",
                                  'underline-end' => "</u>",
                                  'paragraph' => "<p>",
                                  'paragraph-end' => "</p>",
                                  'normal' => "",
                                  'normal-end' => "" );
        $this->EmptyStyles = array();
        foreach ( $this->TerminalStyles as $styleName => $styleValue )
        {
            $this->EmptyStyles[$styleName] = false;
        }
        $this->UseStyles = false;
        $this->IsQuiet = false;
    }

    /*!
     \return the string used to end lines. This is either the \n if CLI is used
             or <br/> if the script is run in a webinterface.
    */
    function endlineString()
    {
        return $this->EndlineString;
    }

    /*!
     \return \c true if the current script is run in a webinterface.
    */
    function isWebOutput()
    {
        return $this->WebOutput;
    }

    /*!
     \return the style for the name \a $name. The style is specific for terminals.
    */
    function terminalStyle( $name )
    {
        return $this->TerminalStyles[$name];
    }

    /*!
     \return the style for the name \a $name. The style is specific for web.
    */
    function webStyle( $name )
    {
        return $this->WebStyles[$name];
    }

    /*!
     \return a hash with all terminal styles.
    */
    function terminalStyles()
    {
        return $this->TerminalStyles;
    }

    /*!
     \return a hash with all web styles.
    */
    function webStyles()
    {
        return $this->WebStyles;
    }

    /*!
     \return a hash with empty styles.
    */
    function emptyStyles()
    {
        return $this->EmptyStyles;
    }

    /*!
     \return the style for the name \a $name. The style is taken from the current interface type.
    */
    function style( $name )
    {
        if ( $this->UseStyles )
        {
            if ( $this->isWebOutput() )
                return $this->webStyle( $name );
            else
                return $this->terminalStyle( $name );
        }
        return false;
    }

    /*!
     \return the text \a $text wrapped in the style \a $styleName.
    */
    function stylize( $styleName, $text )
    {
        $preStyle = $this->style( $styleName );
        $postStyle = $this->style( $styleName . '-end' );
        return $preStyle . $text . $postStyle;
    }

    /*!
     Controls whether styles are to be used or not. If disabled
     empty strings are returned when asking for styles.
     \note This only controls the style() function.
    */
    function setUseStyles( $useStyles )
    {
        $this->UseStyles = $useStyles;
    }

    /*!
     \return \c true if styles are enabled.
    */
    function useStyles()
    {
        return $this->UseStyles;
    }

    /*!
     Outputs the string \a $string to the current interface.
     If \a $addEOL is true then the end-of-line string is added.
    */
    function output( $string = false, $addEOL = true )
    {
        if ( $this->isQuiet() )
            return;
        print( $string );
        if ( $addEOL )
            print( $this->endlineString() );
    }

    /*!
     Outputs the string \a $string to the current interface as a notice.
     If \a $addEOL is true then the end-of-line string is added.
    */
    function notice( $string = false, $addEOL = true )
    {
        if ( $this->isQuiet() )
            return;
        print( $string );
        if ( $addEOL )
            print( $this->endlineString() );
    }

    /*!
     Outputs the string \a $string to the current interface as an warning.
     If \a $addEOL is true then the end-of-line string is added.
    */
    function warning( $string = false, $addEOL = true )
    {
        if ( $this->isQuiet() )
            return;
        $string = $this->stylize( 'warning', $string );
        print( $string );
        if ( $addEOL )
            print( $this->endlineString() );
    }

    /*!
     Outputs the string \a $string to the current interface as an error.
     If \a $addEOL is true then the end-of-line string is added.
    */
    function error( $string = false, $addEOL = true )
    {
        if ( $this->isQuiet() )
            return;
        $string = $this->stylize( 'error', $string );
        print( $string );
        if ( $addEOL )
            print( $this->endlineString() );
    }

    /*!
     Sets whether the output(), notice(), warning() and error() methods should print out anything.
     \sa isQuiet, isLoud
    */
    function setIsQuiet( $isQuiet )
    {
        $this->IsQuiet = $isQuiet;
    }

    /*!
     \return \c true if output is not allowed.
     \sa isLoud
    */
    function isQuiet()
    {
        return $this->IsQuiet;
    }

    /*!
     \return \c true if output is allowed.
     \sa isQuiet
    */
    function isLoud()
    {
        return !$this->IsQuiet;
    }

    function parseOptionString( $configString, &$optionConfig )
    {
        $len = strlen( $configString );
        $i = 0;
        if ( !is_array( $optionConfig ) )
        {
            $optionConfig = array( 'list' => array(),
                                   'short' => array(),
                                   'long' => array() );
        }
        while ( $i < $len )
        {
            $option = $configString[$i];
            if ( $option == '[' )
            {
                $end = strpos( $configString, ']', $i + 1 );
                if ( $end === false )
                {
                    eZDebug::writeError( "Missing end marker ] in option string at position $i",
                                         'eZCLI::parseOptionString' );
                    return $optionConfig;
                }
                $optionList = substr( $configString, $i + 1, $end - $i - 1 );
                $i += 1 + ( $end - $i );
                $startMarkerPos = strpos( $optionList, '[' );
                if ( $startMarkerPos !== false )
                {
                    eZDebug::writeError( "Start marker [ found in option string at position, it should not be present. Skipping current option" . ( $i + 1 + $startMarkerPos ),
                                         'eZCLI::parseOptionString' );
                    continue;
                }
                $optionList = explode( '|', $optionList );
            }
            else
            {
                $text = $option;
                ++$i;
                if ( $i < $len and
                     in_array( $configString[$i], array( ':', ';' ) ) )
                {
                    $text .= $configString[$i];
                    ++$i;
                }
                if ( $i < $len and
                     in_array( $configString[$i], array( '?', '*', '+' ) ) )
                {
                    $text .= $configString[$i];
                    ++$i;
                }
                $optionList = array( $text );
            }
            $optionStoreName = false;
            unset( $optionConfigList );
            $optionConfigList = array();
            foreach ( $optionList as $optionItem )
            {
                $optionLen = strlen( $optionItem );
                $hasValue = false;
                $optionName = $optionItem;
                $quantifierText = false;
                $quantifier = array( 'min' => 0,
                                     'max' => 0 );
                if ( $optionLen > 0 and in_array( $optionName[$optionLen - 1], array( '?', '*', '+' ) ) )
                {
                    $quantifierText = $optionName[$optionLen - 1];
                    $optionName = substr( $optionName, 0, $optionLen - 1 );
                    --$optionLen;
                    if ( $quantifierText == '?' )
                        $quantifier = array( 'min' => 0,
                                             'max' => 1 );
                    else if ( $quantifierText == '*' )
                        $quantifier = array( 'min' => 0,
                                             'max' => false );
                    else if ( $quantifierText == '+' )
                        $quantifier = array( 'min' => 1,
                                             'max' => false );
                }
                if ( $optionLen > 0 and in_array( $optionName[$optionLen - 1], array( ':', ';' ) ) )
                {
                    $valueText = $optionName[$optionLen - 1];
                    $optionName = substr( $optionName, 0, $optionLen - 1 );
                    --$optionLen;
                    if ( $valueText == ':' )
                        $hasValue = true;
                    else if ( $valueText == ';' )
                        $hasValue = 'optional';
                }
                $optionLen = strlen( $optionName );
                if ( $optionLen == 0 )
                    continue;
                $optionStoreName = $optionName;
                $optionConfigItem = array( 'name' => $optionName,
                                           'has-value' => $hasValue,
                                           'quantifier' => $quantifier,
                                           'store-name' => false,
                                           'is-long-option' => strlen( $optionName ) > 1 );
                $optionConfigList[] = $optionConfigItem;
            }
            foreach ( array_keys( $optionConfigList ) as $optionConfigItemKey )
            {
                $optionConfigItem =& $optionConfigList[$optionConfigItemKey];
                $optionName = $optionConfigItem['name'];
                $optionConfigItem['store-name'] = $optionStoreName;
                $optionConfig['list'][] = $optionConfigItem;
                if ( $optionConfigItem['is-long-option'] )
                    $optionConfig['long'][$optionName] = $optionConfigItem;
                else
                    $optionConfig['short'][$optionName] = $optionConfigItem;
            }
        }
        return $optionConfig;
    }

    function getOptions( $config, $argumentConfig, $arguments = false )
    {
        if ( $arguments === false )
        {
            $arguments = $GLOBALS['argv'];
            $program = $arguments[0];
            array_shift( $arguments );
        }

        if ( isset( $this ) and get_class( $this ) == 'ezcli' )
            $cli =& $this;
        else
            $cli =& eZCLI::instance();

        if ( is_string( $config ) )
            $config = eZCLI::parseOptionString( $config, $optionConfig );
        if ( is_string( $argumentConfig ) )
            $argumentConfig = eZCLI::parseOptionString( $argumentConfig, $tmpArgumentConfig );

        $options = array();

        $helpOption = false;
        $helpText = false;
        if ( isset( $config['short']['h'] ) )
            $helpOption = '-h';
        else if ( isset( $config['short']['help'] ) )
            $helpOption = '--help';
        if ( $helpOption )
            $helpText = "\n" . "Try `$program $helpOption' for more information.";

        $options['arguments'] = array();

        $argumentCount = count( $arguments );
        for ( $i = 0; $i < $argumentCount; ++$i )
        {
            $argument = $arguments[$i];
            $argumentLen = strlen( $argument );
            if ( $argumentLen > 1 and
                 $argument[0] == '-' )
            {
                $argumentValue = false;
                if ( $argumentLen > 2 and
                     $argument[1] == '-' )
                {
                    $optionName = substr( $argument, 2 );
                    $assignPosition = strpos( $optionName, '=' );
                    if ( $assignPosition !== false )
                    {
                        $argumentValue = substr( $optionName, $assignPosition + 1 );
                        $optionName = substr( $optionName, 0, $assignPosition );
                    }
                    $optionType = 'long';
                    $optionPrefix = '--';
                    $checkNext = false;
                }
                else
                {
                    $optionName = $argument[1];
                    if ( $argumentLen > 2 )
                    {
                        $argumentValue = substr( $argument, 2 );
                    }
                    $optionType = 'short';
                    $optionPrefix = '-';
                    $checkNext = true;
                }
                $configItem =& $config[$optionType][$optionName];
                if ( isset( $configItem ) )
                {
                    $value = true;
                    $hasValue = $configItem['has-value'];
                    $hasMultipleValues = ( $configItem['quantifier']['min'] > 1 or
                                           $configItem['quantifier']['max'] === false or
                                           $configItem['quantifier']['max'] > 1 );
                    if ( $hasValue )
                    {
                        $hasArgumentValue = false;
                        if ( $argumentValue !== false )
                        {
                            $value = $argumentValue;
                        }
                        else if ( $checkNext and $configItem['has-value'] !== 'optional' )
                        {
                            ++$i;
                            if ( $i < $argumentCount )
                            {
                                $hasArgumentValue = true;
                            }
                            else
                            {
                                --$i;
                                $cli->error( "$program: option `$optionPrefix$optionName' requires an argument" . $helpText );
                                return false;
                            }
                            if ( $hasArgumentValue )
                                $value = $arguments[$i];
                        }
                        else
                        {
                            if ( $configItem['has-value'] !== 'optional' )
                            {
                                $cli->error( "$program: option `$optionPrefix$optionName' requires an argument" . $helpText );
                                return false;
                            }
                        }
                    }
                    $optionStoreName = $configItem['store-name'];
                    if ( !$optionStoreName )
                        $optionStoreName = $optionName;
                    if ( $hasMultipleValues )
                    {
                        if ( !isset( $options[$optionStoreName] ) )
                            $options[$optionStoreName] = array();
                        $options[$optionStoreName][] = $value;
                    }
                    else
                    {
                        $options[$optionStoreName] = $value;
                    }
                }
                else
                {
                    $cli->error( "$program: invalid option `$optionPrefix$optionName'" . $helpText );
                    return false;
                }
            }
            else
            {
                $options['arguments'][] = $argument;
            }
        }
        foreach ( $config['list'] as $configItem )
        {
            $optionStoreName = $configItem['store-name'];
            if ( $optionStoreName and !isset( $options[$optionStoreName] ) )
                $options[$optionStoreName] = null;
        }
        return $options;
    }

    /*!
     \return the unique instance for the cli class.
    */
    function &instance()
    {
        $implementation =& $GLOBALS['eZCLIInstance'];
        if ( !isset( $implementation ) or
             get_class( $implementation ) != 'ezcli' )
        {
            $implementation = new eZCLI();
        }
        return $implementation;
    }
}

?>
