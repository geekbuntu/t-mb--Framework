<?php

class Sitemap {

    ////////////////////////////////////////////////////////////////////////
    //////////      The Properties    /////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance = null;
    protected $aControllers     = null;
    protected $aMap             = null;
    protected $aRouters         = null;
    protected $aViews           = null;
    protected $sXml             = null;

    ////////////////////////////////////////////////////////////////////////
    //////////      The Singleton Experience    ///////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This sets the singleton pattern instance
     *
     * @return Sitemap
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
     * @return Sitemap
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
        self::$oInsance = null;
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Constructor    ////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    public function __construct() {

        // Return instance
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Public    /////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    public function generateMap() {

        $this->loadControllers();
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Protected    //////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected function loadControllers() {

        $oFilesystem = new DirectoryIterator(APPLICATIONPATH.'/controllers');

        Framework::getInstance()->showDebug($oFilesystem);

    }

    protected function loadRouters() {

    }

    protected function loadViews() {

    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Setters    ////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    public function setControllers(array $aControllers) {

        // Set system controllers
        $this->aControllers = (array) $aControllers;

        // Return instance
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Getters    ////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    public function getControllers() {

        // Return controllers
        return $this->aControllers;
    }
}