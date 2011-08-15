<?php

class Rpc_Server extends Rpc {

    ////////////////////////////////////////////////////////////////////////
    //////////      Properties    /////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance = null; // This is our instance
    protected $sInputMethod     = null; // This determines whether to read JSON or XML
    protected $sOutputMethod    = null; // This determines whether to output in JSON or XML
    protected $sRawInput        = null; // This is our raw input string
    protected $oRequest         = null; // This is our request object

    ////////////////////////////////////////////////////////////////////////
    //////////      Singleton Experience    ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This sets the singleton pattern instance
     *
     * @return Rpc_Server @property self::$oInstance
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
     * @return Rpc_Server @property self::$oInstance
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
     * @return Rpc_Server $this for a fluid and chain-loadable interface
    **/
    public function __construct() {

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Public  ////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function buildResponse($mData) {

        // Reset Framework errors so that
        // we do not output a view
        Framework::getInstance()->setError(null);

        // Check to see if we are building
        // a JSON or XML response
        if ($this->getOutputMethod() === 'json') {

            // Set the JSON to return
            $sResponse = json_encode(array(
                'mResponse' => $mData
            ));
        } else {

            // Return the XML encoded response
            $oXml = new SimpleXMLElement('<mResponse/>');

            // Check to see if we have
            // an array or object
            if (is_array($mData)) {         // Array

                // Run our converter
                $this->convertArrayToXml($mData, $oXml);

            } elseif (is_object($mData)) {  // Object

                // Run our converter
                $this->convertObjectToXml($mData, $oXml);

            } else {                        // Scalar
                $oXml->addChild('mScalar', $mData);
            }

            // Set the XML to return
            $sResponse = $oXml->asXML();
        }

        // Send the response
        return $sResponse;
    }

    /**
     * This method parses the input
     *
     * @param bool $bJson determines whether we are running JSON or XML
     * @return Rpc_Server $this
    **/
    public function parseInput() {

        // Grab the URL query params
        $oQueryParams = Framework::getInstance()->getQueryParams();

        // Check to see if we have
        // an empty request object
        if (empty($_POST['oData']) && !empty($oQueryParams->sMethod) && (Framework::getInstance()->loadConfigVar('systemSettings', 'enableRpcUrl') == true)) {

            // It's empty, however the user is
            // trying to access the RPC server via
            // GET, let them
            $this->setRequest($oQueryParams);
        } else {

            // Store the raw input
            $this->setRawInput($_POST['oData']);

            // Check to see if we are
            // decoding JSON or XML
            if ($this->getInputMethod() == 'json') {

                // We are decoding JSON
                $this->setRequest(json_decode($this->getRawInput()));
            } elseif ($this->getInputMethod() == 'xml') {

                // We are decoding XML
                $this->setRequest(simplexml_load_string($this->getRawInput(), 'SimpleXMLElement', LIBXML_NOEMPTYTAG));
            }

            Framework::getInstance()->showDebug($this);
        }

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Protected  /////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * This method converts an array
     * to a SimpleXMLElement Object
     *
     * @param array $aData
     * @param SimpleXMLElement $oXml
     * @return SimpleXMLElement $oXml
    **/
    protected function convertArrayToXml(array $aData, SimpleXMLElement $oXml) {

        // Loop through the array
        foreach ($aData as $sElement => $mValue) {

            // Check to see if we are working
            // with an array of arrays
            if (is_array($mValue)) {        // Array of Arrays

                // Run this method again with the new array
                $this->convertArrayToXml($mValue, $oXml->addChild(is_numeric($sElement) ? 'mEntity' : $sElement));
            } elseif (is_object($mValue)) { // Array of Objects

                // Run our object converter method
                $this->convertObjectToXml($mValue, $oXml->addChild(is_numeric($sElement) ? 'mEntity' : $sElement));

            } else {                        // Array of Scalar data

                // Add the element like normal
                $oXml->addChild((is_numeric($sElement) ? 'mEntity' : $sElement), $mValue);
            }
        }

        // Return the XML object
        return $oXml;
    }

    /**
     * This method converts an object
     * to a SimpleXMLElement Object
     *
     * @param object $oData
     * @param SimpleXMLElement $oXml
     * @return SimpleXMLElement $oXml
    **/
    protected function convertObjectToXml($oData, SimpleXMLElement $oXml) {

        // Loop through the object
        foreach ($oData as $sElement => $mValue) {

            // Check to see if we are working
            // with an array of arrays
            if (is_array($mValue)) {        // Array of Arrays

                // Run this method again with the new array
                $this->convertArrayToXml($mValue, $oXml->addChild(is_numeric($sElement) ? 'mEntity' : $sElement));
            } elseif (is_object($mValue)) { // Array of Objects

                // Run our object converter method
                $this->convertObjectToXml($mValue, $oXml->addChild(is_numeric($sElement) ? 'mEntity' : $sElement));

            } else {                        // Array of Scalar data

                // Add the element like normal
                $oXml->addChild((is_numeric($sElement) ? 'mEntity' : $sElement), $mValue);
            }
        }

        // Return the XML object
        return $oXml;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  RPC Methods  ///////////////////////////////////////////////
    /////////   These methods are setup like DataType_Method()    /////////
    //////////////////////////////////////////////////////////////////////
    //////////  JSON  ///////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////

    public function Json_Ping() {

        // Return the $_SERVER
        return $this->buildResponse($_SERVER);
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  XML  ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function Xml_Ping() {
        return $this->buildResponse($_SERVER);
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Setters  ///////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * This method sets which data type
     * to read and convert into the system
     *
     * @param string $sInputMethod
     * @return Rpc_Server $this
    **/
    public function setInputMethod($sInputMethod) {

        // Set the system input method
        $this->sInputMethod = (string) strtolower($sInputMethod);

        // Return instance
        return $this;
    }

    /**
     * This method sets which data type
     * to ouptut to the caller
     *
     * @param string $sOutputMethod
     * @return Rpc_Server $this
    **/
    public function setOutputMethod($sOutputMethod) {

        // Set the system output method
        $this->sOutputMethod = (string) strtolower($sOutputMethod);

        // Return instance
        return $this;
    }

    /**
     * This method sets the raw request
     * string into the system
     *
     * @param $sRawInput
     * @return Rpc_Server $this
    **/
    public function setRawInput($sRawInput) {

        // Set the raw input request
        $this->sRawInput = (string) $sRawInput;

        // Return instance
        return $this;
    }

    /**
     * This method sets the decoded
     * request into the system
     *
     * @param $oRequest
     * @return Rpc_Server $this
    **/
    public function setRequest($oRequest) {

        // Set our request
        $this->oRequest = $oRequest;

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Getters  ///////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * This method gets which data type
     * to read and convert from the system
     *
     * @param string $sInputMethod
     * @return string @property $sInputMethod
    **/
    public function getInputMethod() {

        // Return input method
        return $this->sInputMethod;
    }

    /**
     * This method gets which data type
     * to ouptut to the caller
     *
     * @return string @property $sOutputMethod
    **/
    public function getOutputMethod() {

        // Return output method
        return $this->sOutputMethod;
    }

    /**
     * This method gets the raw request
     * string fromt the system
     *
     * @return string @property $sRawInput
    **/
    public function getRawInput() {

        // Return request string
        return $this->sRawInput;
    }

    /**
     * This method gets the decoded
     * request from the system
     *
     * @return Object|SimpleXml @property $oRequest
    **/
    public function getRequest() {

        // Return request object
        return $this->oRequest;
    }
}