<?php
/**
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = array();

$FunctionList['workflow_statuses'] = array( 'name' => 'workflow_statuses',
                                            'operation_types' => array( 'read' ),
                                            'call_method' => array( 'class' => 'eZWorkflowFunctionCollection',
                                                                    'method' => 'fetchWorkflowStatuses' ),
                                            'parameter_type' => 'standard',
                                            'parameters' => array( ) );

$FunctionList['workflow_type_statuses'] = array( 'name' => 'workflow_type_statuses',
                                                 'operation_types' => array( 'read' ),
                                                 'call_method' => array( 'class' => 'eZWorkflowFunctionCollection',
                                                                         'method' => 'fetchWorkflowTypeStatuses' ),
                                                 'parameter_type' => 'standard',
                                                 'parameters' => array( ) );

?>
