<?php
//
// Definition of eZBenchmark class
//
// Created on: <18-Feb-2004 11:45:27 >
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.1.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2010 eZ Systems AS
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

/*! \file ezbenchmark.php
*/

/*!
  \class eZBenchmark ezbenchmark.php
  \brief eZBenchmark provides a framework for doing benchmarks

*/

require_once( 'lib/ezutils/classes/ezdebug.php' );

class eZBenchmark extends eZBenchmarkUnit
{
    /*!
     Initializes the benchmark with the name \a $name.
    */
    function eZBenchmark( $name )
    {
        $this->eZBenchmarkUnit( $name );
    }

    function addMark( &$mark )
    {
        if ( is_subclass_of( $mark, 'ezbenchmarkunit' ) )
        {
            $markList = $mark->markList();
            foreach ( $markList as $entry )
            {
                $entry['name'] = $mark->name() . '::' . $entry['name'];
                $this->addEntry( $entry );
            }
        }
        else
        {
            eZDebug::writeWarning( "Tried to add mark unit for an object which is not subclassed from eZBenchmarkUnit", __METHOD__ );
        }
    }
}

?>
