<?php
/**
 * File containing the eZAsynchronousPublishingFilterInterface interface.
 *
 * @copyright Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 * @since 4.5
 */

/**
 * Base interface for eZ Asynchronous Publishing filter classes
 * @package kernel
 */
interface eZAsynchronousPublishingFilterInterface
{
    /**
     * Hook called by the kernel to check for async acceptance of an object
     *
     * @return bool true if the object can be published asynchronously, false if it shouldn't
     */
    public function accept();
}
?>