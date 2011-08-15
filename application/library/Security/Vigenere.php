<?php
    /**
     * This is our Vigenère Cipher encryption class
     *
     * @url http://en.wikipedia.org/wiki/Vigenère_cipher
    **/
    class Security_Vigenere extends Security {

        ////////////////////////////////////////////////////////////////////////
        //////////      Properties    /////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        protected $sTable = null;   // This is our character table
        protected $iMod   = null;   // This is our modulus

        ////////////////////////////////////////////////////////////////////////
        //////////      Singleton Experience    ///////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This sets the singleton pattern instance
         *
         * @return object @property self::$oInstance
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
         * @return object @property self::$oInstance
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
         * @return object $this for a fluid and chain-loadable interface
        **/
		public function __construct() {

            // Set our character map
            $this->setTabe('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

            // Set our key mod
            $this->setMod(strlen($this->getTable()));

            // Return instance
            return $this;
        }

        /////////////////////////////////////////////////////////////////////////
        //////////  Public  ////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////

        public function alphabetPosition($iTablePosition) {

            // Set the position
            $iAlphabetPosition = (($iTablePosition >= 0) ? $iTablePosition : (strlen($this->getTable()) + $iTablePosition));

            // Return the position
            return $this->getTable($iAlphabetPosition);
        }

        public function characterPosition($iChar, $bUseKey = false) {

            // Grab the character position
            $iPosition = (integer) ($iChar % strlen(($bUseKey === true) ? $this->getKey() : $this->getSource()));

            // Return the position
            return $this->getSource($iPosition);
        }

        /**
         * This method runs all the actions necessary
         * to decrypt the encrypted string.
         *
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function decrypt() {

            // Source placeholder
            $sSource = (string) '';

            // Reset the source
            $this->setSource(null);

            for ($iChar = (integer) 0; $iChar < strlen($this->getCipher()); $iChar ++) {

                $iShift    = (integer) ($this->tablePosition($this->characterPosition($iChar)) - $this->tablePosition($this->characterPosition($iChar, true)));

                $iPosition = (integer) ($iShift % $this->getMod());

                $sSource  .= (string) $this->alphabetPosition($iPosition);
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
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function encrypt() {

            // Hash placeholder
            $sCipher = (string) '';


            for ($iChar = (integer) 0; $iChar < strlen($this->getSource()); $iChar ++) {

                // Get the table position
                $iShift    = (integer) ($this->tablePosition($this->characterPosition($iChar)) + $this->tablePosition($this->characterPosition($iChar, true)));

                // Get the table position we want to modify
                $iPosition = (integer) ($iShift % $this->getMod());

                // Append to the cipher
                $sCipher  .= (string) $this->alphabetPosition($iPosition);
            }

            // Set the cipher
            $this->setCipher($sCipher);

            // Return instance
            return $this;
        }

        public function tablePosition($sChar) {

            // Return the position
            return strpos($this->getTable(), $sChar);
        }

        ////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method is responsible for setting
         * our key mod into memory
         *
         * @param integer $iMod is the mod
         * @return Security_Vigenere $this for a fluid and chain-loadable interface
        **/
        public function setMod($iMod) {

            // Set our mod
            $this->iMod = (integer) $iMod;

            // Return instance
            return $this;
        }

        /**
         * This method sets our character map into memory
         *
         * @param string $sTable is our character map
         * @return Security_Vigenere $this for a fluid and chain-loadable interface
        **/
        public function setTabe($sTable) {

            // Set our character table
            $this->sTable = (string) $sTable;

            // Return instance
            return $this;
        }

        ////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method retrieves our key mod
         *
         * @return integer @property iMod
        **/
        public function getMod() {

            // Return our mod
            return $this->iMod;
        }

        /**
         * This returns our character table
         *
         * @return string @property sTable
        **/
        public function getTable($iCharacter = null) {

            if (is_null($iCharacter)) {

                // Return our character table
                return $this->sTable;
            } else {

                // Return the character position
                return $this->sTable{$iCharacter};
            }
        }

    }