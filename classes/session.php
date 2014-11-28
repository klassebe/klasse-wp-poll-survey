<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 5/06/14
 * Time: 16:13
 */

namespace kwps_classes;


class Session {
    public static function myStartSession() {
        if (!isset($_SESSION))
        {
            // If we are run from the command line interface then we do not care
            // about headers sent using the session_start.
            if (PHP_SAPI === 'cli')
            {
                $_SESSION = array();
            }
            elseif (!headers_sent())
            {
                if(!session_id()) {
                    if (!session_start())
                    {
                        throw new Exception(__METHOD__ . 'session_start failed.');
                    }
                }
            }
            else
            {
                throw new Exception(__METHOD__ . 'Session started after headers sent.');
            }
        }

    }

    public static function myEndSession() {
        session_destroy ();
    }

    public static function set_version_info($version_id){
        // set session information for version
        $bits = 50;
        $uid = bin2hex(openssl_random_pseudo_bytes($bits));

        if (!isset($_SESSION['kwps_version_' . $version_id])) {
            $_SESSION['kwps_version_' . $version_id] = $uid;
        }
    }

    public static function get_version_info($version_id){
        return $_SESSION['kwps_version_' . $version_id];
    }

    public static function unset_version_info($version_id){
        if (isset($_SESSION['kwps_version_' . $version_id])) {
            unset( $_SESSION['kwps_version_' . $version_id] );
        }
    }
} 