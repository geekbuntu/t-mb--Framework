<?php

class Rpc_Client extends Rpc {

    ////////////////////////////////////////////////////////////////////////
    //////////      Properties    /////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance = null; // This is our instance
    protected $sOutputMethod    = null; // This determines whether to output in JSON or XML
    protected $oRequest         = null; // This is our request object

    ////////////////////////////////////////////////////////////////////////
    //////////      Singleton Experience    ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This sets the singleton pattern instance
     *
     * @return Rpc_Client @property self::$oInstance
    **/
    public static function setInstance() {

        // Try to set an instance
        try {

            // Set instance to new self
            self::$oInstance = new self();

        // Catch any exceptions
        } catch (Exception $oException) {

            // Set error string
            die("Error:  {$oException->getMessage()}");
        }

        // Return instance of class
        return self::$oInstance;
    }

    /**
     * This gets the singleton instance
     *
     * @return Rpc_Client @property self::$oInstance
    **/
    public static function getInstance() {

        // Check to see if an instance has already
        // been created
        if (is_null(self::$oInstance)) {

            // If not, return a new instance
            return self::setInstance();
        } else {

            // If so, return the previously created
            // instance
            return self::$oInstance;
        }
    }

    /**
     * This resets the singleton instance to null
     *
     * @return void
    **/
    public static function resetInstance() {

        // Reset the instance
        self::$oInstance = null;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Constructor  ///////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * This method currently does nothing
     *
     * @return Rpc_Client $this for a fluid and chain-loadable interface
    **/
    public function __construct() {

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Public  ////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function buildRequest($aData) {
        
    }
}