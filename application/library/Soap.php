<?php

abstract class Soap {

    ////////////////////////////////////////////////////////////////////////
    //////////      The Properties    /////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance  = null;    // This is our singleton instance

    ////////////////////////////////////////////////////////////////////////
    //////////      Singleton Experience    ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This sets the singleton pattern instance
     *
     * @return Security @property self::$oInstance
    **/
    abstract public static function setInstance();

    /**
     * This gets the singleton instance
     *
     * @return Security @property self::$oInstance
    **/
    abstract public static function getInstance();

    /**
     * This resets the singleton instance to null
     *
     * @return void
    **/
    abstract public static function resetInstance();

    ////////////////////////////////////////////////////////////////////////
    //////////      Construct    //////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////
    //////////      Construct    //////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    abstract public function __construct();

    ////////////////////////////////////////////////////////////////////////
    //////////      Public    /////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
}