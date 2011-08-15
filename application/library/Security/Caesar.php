<?php
    /**
     * This is our Caesar Cipher encryption class
     *
     * @url http://en.wikipedia.org/wiki/Caesar_cipher
    **/
    class Security_Caesar extends Security {

        ////////////////////////////////////////////////////////////////////////
        //////////      Singleton Experience    ///////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This sets the singleton pattern instance
         *
         * @return Security_Caesar @property self::$oInstance
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
         * @return Security_Caesar @property self::$oInstance
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

        /**
         * This method currently does nothing
         *
         * @return Security_Caesar $this for a fluid and chain-loadable interface
        **/
		public function __construct() {

            // Return instance
            return $this;
        }

        /////////////////////////////////////////////////////////////////////////
        //////////  Public  ////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////

        /**
         * This method runs all the actions necessary
         * to decrypt the encrypted string.
         *
         * @return Security_Caesar $this for a fluid and chain-loadable interface
        **/
        public function decrypt() {

            // Source placeholder
            $sSource = (string) '';

            // Decrypt the hash
            for ($iChar = (integer) 0; $iChar < strlen($this->getKey()) ; $iChar ++) {

                // Grab the ASCII values
                $sCipherOrd = (integer) ord(substr($this->getCipher(), $iChar, 1));
                $sKeyOrd    = (integer) ord(substr($this->getKey(), ($iChar % strlen($this->getKey())), 1));

                // Check to see if we have a 0 index
                if (($sCipherOrd - $sKeyOrd) >= 0) {

                    // Append to source
                    $sSource .= (string) chr($sCipherOrd - $sKeyOrd);
                }

                if (($sCipherOrd - $sKeyOrd) < 0) {

                    // Append to source
                    $sSource .= (string) chr(($sCipherOrd - $sKeyOrd) + 256);
                }
            }

            // Set the source
            $this->setSource($sSource);

            // Return instance
            return $this;
        }

        /**
         * This method runs all the actions necessary
         * to encrypt the string
         *
         * @return Security_Caesar $this for a fluid and chain-loadable interface
        **/
        public function encrypt() {

            // Check for a cipher
            if (is_null($this->getKey())) {

                // Set a cipher
                $this->createKey();
            }

            // Hash placeholder
            $sCipher = (string) '';

            // Generate the hash
            for ($iChar = (integer) 0; $iChar < strlen($this->getSource()); $iChar ++) {

                // Grab ASCII values
                $sSourceOrd = (integer) ord(substr($this->getSource(), $iChar, 1));
                $sKeyOrd    = (integer) ord(substr($this->getKey(), ($iChar % strlen($this->getKey())), 1));

                // Check for 256 bit
                if (($sSourceOrd + $sKeyOrd) <= 256) {

                    // Append to the hash
                    $sCipher .= (string) chr($sSourceOrd + $sKeyOrd);
                }

                if (($sSourceOrd + $sKeyOrd) > 255) {

                    // Append to hash
                    $sCipher .= (string) chr(($sSourceOrd + $sKeyOrd) - 255);
                }
            }

            // Set the hash into the system
            $this->setCipher($sCipher);

            // Return instance
            return $this;
        }

    }