<?php

class Soap_Client extends Soap {

    ////////////////////////////////////////////////////////////////////////
    //////////      The Properties    /////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance = null;
    protected $oSoapInstance    = null;
    protected $sWsdl            = null;

    ////////////////////////////////////////////////////////////////////////
    //////////      The Singleton Experience    ///////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This sets the singleton pattern instance
     *
     * @return Soap_Client
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
     * @return Soap_Client
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

    /**
     * This method creates a new connection
     * to an existing SOAP server
     *
     * @param string $sWsdl is the URL to the WSDL
     * @return bool|Soap_Client $this for success, false for failure
    **/
    public function Connect($sWsdl) {

        // Check for a WSDL URL
        if (empty($sWsdl)) {

            // Set the system error
            Framework::getInstance()->setError(
                Framework::getInstance()->loadConfigVar(
                    'errorMessages',
                    'noWsdlUrlGiven'
                )
            );

            // Return
            return false;

        } else {

            // Set the WSDL URL
            // into the system
            $this->setWsdl($sWsdl);
        }

        // Get our new SOAP instance
        $oSoapInstance = @new SoapClient($this->getWsdl(), array(
            'trace'      => 1,
            'exceptions' => 0
        ));

        // Set our SOAP instance
        // into our system
        $this->setSoapInstance($oSoapInstance);

        // Return instance
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Setters    ////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This method sets our SOAP instance
     *
     * @param SoapClient $oSoapInstance is our instance of SoapClient
     * @return Soap_Client $this
    **/
    public function setSoapInstance(SoapClient $oSoapInstance) {

        // Set our soap instance
        $this->oSoapInstance = $oSoapInstance;

        // Return instance
        return $this;
    }

    /**
     * This method sets the URL to
     * the WSDL into the system
     *
     * @param string $sUrl
     * @return Soap_Client $this
    **/
    public function setWsdl($sUrl) {

        // Set the WSDL URL
        $this->sWsdl = (string) $sUrl;

        // Return instance
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Getters    ////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This method gets our SOAP instance
     *
     * @return SoapClient @property $oSoapInstance
    **/
    public function getSoapInstance() {

        // Return our soap instance
        return $this->oSoapInstance;
    }

    /**
     * This method gets the URL to
     * the WSDL from the system
     *
     * @return string @propert $sWsdl
    **/
    public function getWsdl() {

        // Return our WSDL URL
        return $this->sWsdl;
    }
}