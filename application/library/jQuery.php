<?php

class jQuery {

    ////////////////////////////////////////////////////////////////////////
    //////////      Properties    /////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance = null; // This is our instance
    protected $sOutput          = null; // This is our string output

    ////////////////////////////////////////////////////////////////////////
    //////////      Singleton Experience    ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This sets the singleton pattern instance
     *
     * @return jQuery
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
     * @return jQuery
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
    //////////  Construct  /////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function __construct() {

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Public  ////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function Button($mElementId, $aParameters = array()) {

        // Start the jQuery
        $sJquery = (string) "jQuery(function() {";

        // Check to see if we
        // have multiple elements
        if (is_array($mElementId)) {

            // Loop through the element Ids
            foreach ($mElementId as $iIndex => $sValue) {

                // Check to see if we have
                // any parameters
                if (empty($aParameters[$sValue])) {

                    // We do not have any parameters
                    // create the button
                    $sJquery .= (string) "jQuery(\"{$sValue}\").button();";

                } else {

                    // We have parameters
                    // create the button
                    $sJquery .= (string) "jQuery(\"{$sValue}\").button(".json_encode($aParameters[$sValue]).");";
                }
            }

        } else {

            // Check to see if we have
            // any parameters to add
            if (empty($aParameters)) {

                // We do not have any parameters
                // create the button
                $sJquery .= (string) "jQuery(\"{$mElementId}\").button();";
            } else {

                // We have parameters
                // create the button
                $sJquery .= (string) "jQuery(\"{$mElementId}\").button(".json_encode($aParameters).")";
            }
        }

        // Close the jQuery
        $sJquery .= (string) "})";

        // Now generate the script
        Html::getInstance()->generateScript('text/javascript', null, $sJquery);

        // Set the Html into the system
        $this->setOutput(Html::getInstance()->getHtml());

        // Return instance
        return $this;
    }

    public function Dialog($mElementId, $aParameters = array()) {

        // Start the jQuery
        $sJquery = (string) "jQuery(document).ready(function() {";

        // Check to see if we
        // have multiple elements
        if (is_array($mElementId)) {

            // Loop through the element Ids
            foreach ($mElementId as $iIndex => $sValue) {

                // Check to see if we have
                // any parameters
                if (empty($aParameters[$sValue])) {

                    // We do not have any parameters
                    // create the button
                    $sJquery .= (string) "jQuery(\"{$sValue}\").dialog();";

                } else {

                    // We have parameters
                    // create the button
                    $sJquery .= (string) "jQuery(\"{$sValue}\").dialog(".json_encode($aParameters[$sValue]).");";
                }
            }

        } else {

            // Check to see if we have
            // any parameters to add
            if (empty($aParameters)) {

                // We do not have any parameters
                // create the button
                $sJquery .= (string) "jQuery(\"{$mElementId}\").dialog();";
            } else {

                // We have parameters
                // create the button
                $sJquery .= (string) "jQuery(\"{$mElementId}\").dialog(".json_encode($aParameters).")";
            }
        }

        // Close the jQuery
        $sJquery .= (string) "})";

        // Now generate the script
        Html::getInstance()->generateScript('text/javascript', null, $sJquery);

        // Set the Html into the system
        $this->setOutput(Html::getInstance()->getHtml());

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Setters  ///////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * This method sets the output
     * into the system
     *
     * @param string $sOutput is the output script
     * @return jQuery $this for a fluid interface
    **/
    public function setOutput($sOutput) {

        // Set system output
        $this->sOutput = (string) $sOutput;

        // Return instance
        return $this;
    }

    /////////////////////////////////////////////////////////////////////////
    //////////  Getters  ///////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * This method returns the
     * script output
     *
     * @param bool $bPrint tells the method whether to print the output directly or not
     * @return string html
    **/
    public function getOutput($bPrint = false) {

        // Determine if the caller
        // wishes to print the output
        if ($bPrint === true) {

            // Grab and print
            // the system output
            return print $this->sOutput;
        } else {

            // Grab the system output
            return $this->sOutput;
        }
    }
}