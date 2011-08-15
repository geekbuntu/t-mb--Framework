<?php

    class Cache {

        ////////////////////////////////////////////////////////////////////////
        //////////      The Properties    /////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        protected static $oInstance = null; // This is our instance
        protected $sCacheDirectory  = null; // This is the directory where cache is stored
        protected $iCacheExpiration = null; // This is the base cache expiration interval

        ////////////////////////////////////////////////////////////////////////
        //////////      Singleton Experience    ///////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This sets the singleton pattern instance
         *
         * @return Cache
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
         * @return Cache
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
        //////////      The Construct    //////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This is our construct and it sets up
         * the default values of each of the
         * properties in the system
         *
         * @return Cache $this for a fluid and chain-loadable interface
        **/
        private function __construct() {

            // Purge the system of expired
            // cache files
            $this->purge();

            // Check to see if the cache
            // directory is is defined in
            // the configuration file
            if ($this->loadConfigVar('cache', 'directory') !== false) {

                // Set the cache directory
                $this->setCacheDirectory($this->loadConfigVar('cache', 'directory'));
            } else {

                // Set the cache directory
                $this->setCacheDirectory('/tmp');
            }

            // Check to see if the expiration
            // interval has been defined in
            // the configuration file
            if (class_exists('Framework') && (Framework::getInstance()->loadConfigVar('cache', 'expiration') !== false)) {

                // Set the cache expiration
                $this->setCacheExpiration(Framework::getInstance()->loadConfigVar('cache', 'expiration'));

            } else {

                // Set the cache expiration
                $this->setCacheExpiration(30 * 24 * 60 * 60);
            }

            // Return instance
            return $this;

        }

        ////////////////////////////////////////////////////////////////////////
        //////////      Public    /////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * Thie method checks to see if the provided
         * cache file has expired or not
         *
         * @param string $sKey is the cache data to look for
         * @return bool true for expired, false for not
        **/
        public function expired($sKey) {

            // Set the filename
            $sFilename = (string) "{$this->getCacheDirectory()}/{$sKey}.cache";

            // Check to see if the files exist
            // on the filesystem
            if (file_exists($sFilename)) {

                // Set the expire time
                $iExpiration = (integer) (filemtime($sFilename) + $this->getCacheExpiration());

                // Check to see whether the file
                // hs expired or not
                if ($iExpiration >= time()) {

                    // The file has yet to
                    // expire
                    return false;
                } else {

                    // The file has indeed expired
                    return true;
                }
            } else {

                // The file does not exist
                // on the filesystem
                return true;
            }
        }

        /**
         * This method returns the cached data
         *
         * @param string $sKey is the cache data to look for
         * @return bool|string false if the cache does not exist or is expired, else the data
        **/
        public function load($sKey) {

            // Set the filename
            $sFilename = (string) "{$this->getCacheDirectory()}/{$sKey}.cache";

            // Make sure the files actually exists
            if (file_exists($sFilename)) {

                // Check to make sure the cache
                // file hasn't expired
                if ($this->expired($sKey) === false) {

                    // If the file has not expired
                    // return the contents
                    return file_get_contents($sFilename);

                } else {

                    // Return
                    return false;
                }
            }
        }

        /**
         * This method goes through and deletes all expired
         * cache files in the cache directory
         *
         * @return Cache $this for a fluid and chain-loadable interface
        **/
        public function purge() {

            // Open the directory
            $rCacheDirectory = opendir($this->getCacheDirectory());

            // Read the contents
            while ($sFilename = readdir($rCacheDirectory)) {

                // Make sure we have a cache file
                if (strpos('.cache', $sFilename)) {

                    // We have a cache file, now
                    // see if it has expired
                    if ($this->expired(str_replace('.cache', null, $sFilename)) === true) {

                        // Delete the file
                        unlink($sFilename);
                    }
                }
            }

            // Return instance
            return $this;
        }

        /**
         * This method saves the cache data
         * to the filesystem
         *
         * @param string $sKey is the file to save it as
         * @param string $sContent is the data to write to the file
         * @return Cache $this for a fluid and chain-loadable interface
        **/
        public function save($sKey, $sContent) {

            // Set the filename
            $sFilename     = (string) "{$this->getCacheDirectory()}/{$sKey}.cache";

            // Check to see if the cache
            // directory exists
            if (!file_exists($sFilename)) {

                // Make the directory
                mkdir($this->getCacheDirectory());

                // Save the cache
                file_put_contents($sFilename, $sContent);

                // Touch the file
                touch($sFilename);
            }

            // Return instance
            return $this;
        }

        ////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method sets the directory where we will
         * store the cache files into the system
         *
         * @param string $sDirectory the directory path
         * @return Cache $this for a fluid and chain-loadable interface
        **/
        public function setCacheDirectory($sDirectory) {

            // Set our cache directory
            $this->sCacheDirectory = (string) $sDirectory;

            // Return instance
            return $this;
        }

        /**
         * This method sets the cache file expiration
         * interval into the system
         *
         * @param integer $iTime is the time interval to expire the cache files
         * @return Cache $this for a fluid and chain-loadable interface
        **/
        public function setCacheExpiration($iTime) {

            // Set cache expiration
            $this->iCacheExpiration = (integer) $iTime;

            // Return instance
            return $this;
        }

        ////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method returns the directory path
         * where we store the cache files
         *
         * @return string @property sCacheDirectory is the directory we store the cache files in
        **/
        public function getCacheDirectory() {

            // Return our cache directory
            return $this->sCacheDirectory;
        }

        /**
         * This method returns the system's file
         * expirtation interval
         *
         * @return integer @property iCacheExpiration is the data expiration interval
        **/
        public function getCacheExpiration() {

            // Return our cache expiration time
            return $this->iCacheExpiration;
        }
    }