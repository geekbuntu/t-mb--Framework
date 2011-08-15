<?php

    /**
     * This class is our Security interface
    **/
    abstract class Security {

        ////////////////////////////////////////////////////////////////////////
        //////////      The Properties    /////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

		protected static $oInstance  = null;    // This is our singleton instance
		protected $sCipher           = null;    // This is our encryption cipher
        protected $sKey              = null;    // This is our encryption key
        protected $sSource           = null;    // This is our source string


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

        public abstract function __construct();

        ////////////////////////////////////////////////////////////////////////
        //////////      Public    /////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method checks to see if the configuration file
         * for Framework provides a securityCipher key.  If it
         * does not this method generates one.
         *
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function createKey() {

            // Check for a cryption key
            // in the config file
            if (class_exists('Framework') && Framework::getInstance()->loadConfigVar('security', 'securityCipher')!== false) {

                // Grab our key
                $sKey = (string) Framework::getInstance()->loadConfigVar('security', 'securityCipher');

            // If no key was found,
            // Generate a new one
            } else {

                // Set the string size
                $iLength     = (integer) 50;

                // Create a key on the fly
                $sValidChars = (string) '01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $sChars      = (string) '';

                // Generate the unencrypted base string
                for ($iChar = (integer) 0; $iChar < $iLength; $iChar ++) {

                    // Append the the string
                    $sChars .= (string) $sValidChars[(mt_rand(1, strlen($sValidChars)) - 1)];
                }

                // Encrypt the cipher
                $sKey = (string) crypt($sChars);
            }

            // Set our cipher into the system
            $this->setKey($sKey);

            // Return instance
            return $this;
        }

        public abstract function decrypt();
        public abstract function encrypt();

        ////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method sets the encrypted
         * hash into memory
         *
         * @param string $sHash is the encrypted source
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function setCipher($sCipher) {

            // Set our encrypted hash
            $this->sCipher = (string) $sCipher;

            // Return instance
            return $this;
        }

        /**
         * This method sets our encryption
         * and decryption Key
         *
         * @param string $sKey is the cryption key
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function setKey($sKey) {

            // Set our cipher
            $this->sKey = $sKey;

            // Return instance
            return $this;
        }

        /**
         * This method sets the salt string into the system
         *
         * @param string $sSalt is the salt string to set
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function setSalt($sSalt) {

            // Set the salt string into the system
            $this->sSalt = (string) $sSalt;

            // Return instance
            return $this;
        }

        /**
         * This method sets the string that is
         * to be encrypted into the system
         *
         * @param string $sSource is the string to be encrypted
         * @return Security $this for a fluid and chain-loadable interface
        **/
        public function setSource($sSource) {

            // Set our source string
            $this->sSource = (string) $sSource;

            // Return instance
            return $this;
        }

        ////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method returns the system's encrypted data
         *
         * @return string @property $sCipher
        **/
        public function getCipher() {

            // Return the system's cipher
            return $this->sCipher;
        }

        /**
         * This method returns the system's hash
         *
         * @return string @property $sHash
        **/
        public function getKey() {

            // Return the system's cipher
            return $this->sKey;
        }

        /**
         * This method returns the system's source string
         *
         * @return string @property $sSource
        **/
        public function getSource($iCharacter = null) {

            if (is_null($iCharacter)) {

                // Return the source string
                return $this->sSource;
            } else {

                // Return the character
                return $this->sSource{$iCharacter};
            }
        }
    }
