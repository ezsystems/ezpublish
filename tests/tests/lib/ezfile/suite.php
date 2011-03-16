<?php
/**
 * File containing the eZFileTestSuite class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU General Public License v2
 * @package tests
 */

class eZFileTestSuite extends ezpTestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZFile Test Suite" );
        $this->addTestSuite( 'eZDirTestInsideRoot' );
        $this->addTestSuite( 'eZDirTestOutsideRoot' );
    }

    public static function suite()
    {
        return new self();
    }

}

?>
