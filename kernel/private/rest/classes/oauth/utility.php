<?php
/**
 * File containing the ezpOuathUtility class
 *
 * @copyright Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 *
 */

/**
 * Functionality for working against the draft 10 of the oauth2 spec.
 *
 * @package rest
 */
class ezpOauthUtility
{
    const AUTH_HEADER_NAME     = 'Authorization';
    const AUTH_CGI_HEADER_NAME = 'HTTP_AUTHORIZATION';

    /**
     * Retrieving token as per section 5 of draft-ietf-oauth-v2-10
     *
     * Token can be present inside the Authorize header, inside a URI query
     * parameter, or in the HTTP body.
     *
     * According to section 5.1 the header is the preferred way, and the query
     * component and HTTP body are only looked at if no such header can be found.
     *
     * @TODO A configuration mechanism should alternatively let us select which
     * method to use: 1. header, 2. query component, 3. http body, in other words
     * to override the default behaviour according to spec.
     *
     * @param string $ezcMvcRequest
     * @return void
     */
    public static function getToken( ezcMvcRequest $request )
    {
        // 1. Should first extract required token from the request object
        //    as we know that the request parser does not support this at the
        //    moment we, will skip to the fallback right away. That is to say,
        //    ideally the request parser would make this header available to us,
        //    when available, automatically.

        $token = null;
        $checkStack = array( 'header', 'get', 'post' );

        foreach ( $checkStack as $step )
        {
            switch ( $step )
            {
                case 'header':
                    $token = self::getTokenFromAuthorizationHeader();
                break;

                case 'get':
                    $token = self::getTokenFromQueryComponent( $request );
                break;

                case 'post':
                    $token = self::getTokenFromHttpBody( $request );
                break;
            }

            if ( isset( $token ) ) // Go out of the loop if we find the token during the iteration
            {
                break;
            }
        }

        return $token;
    }


    /**
     * Extracts the OAuth token from the HTTP header, Authorization.
     *
     * The token is transmitted via the OAuth Authentication scheme ref.
     * Section 5.1.1.
     *
     * PHP does not expose the Authorization header unless it uses the 'Basic'
     * or 'Digest' schemes, and it is therefore extracted from the raw Apache
     * headers.
     *
     * On systems running CGI or Fast-CGI PHP makes this header available via
     * the <var>HTTP_AUTHORIZATION</var> header.
     * @link http://php.net/manual/en/features.http-auth.php
     * @throws ezpOauthInvalidRequestException
     * @return string The access token string.
     */
    protected static function getTokenFromAuthorizationHeader()
    {
        $token = null;
        $authHeader = null;
        if ( function_exists( 'apache_request_headers' ) )
        {
            $apacheHeaders = apache_request_headers();
            if ( isset( $apacheHeaders[self::AUTH_HEADER_NAME] ) )
                $authHeader = $apacheHeaders[self::AUTH_HEADER_NAME];
        }
        else
        {
            if ( isset( $_SERVER[self::AUTH_CGI_HEADER_NAME] ) )
                $authHeader = $_SERVER[self::AUTH_CGI_HEADER_NAME];
        }

        if ( isset( $authHeader ) )
        {
            $tokenPattern = "/^(?P<authscheme>OAuth)\s(?P<token>[a-zA-Z0-9]+)$/";
            $match = preg_match( $tokenPattern, $authHeader, $m );
            if ( $match > 0 )
            {
                $token = $m['token'];
            }
        }


        return $token;
    }

    /**
     * Extracts OAuth token query component aka GET parameter.
     *
     * For more information See section 5.1.2 of  oauth2.0 v10
     *
     * @throws ezpOauthInvalidRequestException
     * @param ezcMvcRequest $request
     * @return string The access token string
     */
    protected static function getTokenFromQueryComponent( ezpRestRequest $request )
    {
        $token = null;
        if( isset( $request->get['oauth_token'] ) )
        {
            //throw new ezpOauthInvalidRequestException( "OAuth token not found in query component." );
            $token = $request->get['oauth_token'];
        }

        return $token;
    }

    /**
     * Extracts OAuth token fro HTTP Post body.
     *
     * For more information see section 5.1.3 oauth2.0 v10
     * @param ezpRestRequest $request
     * @return string The access token string
     */
    protected static function getTokenFromHttpBody( ezpRestRequest $request )
    {
        $token = null;
        if ( isset( $request->post['oauth_token'] ) )
        {
            $token = $request->post['oauth_token'];
        }

        return $token;
    }
}
?>
