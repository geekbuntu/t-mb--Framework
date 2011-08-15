<?php

	/**
	 * This class is responsible for
	 * establishing and maintaining
	 * a database connection as well
	 * as running queries against said
	 * database
	 *
	 * @author Travis Brown <tmbrown6@gmail.com>
	 * @package Framework
     * @name MySql
	 * @uses PDO
	 */
	class MySql extends Framework {

		////////////////////////////////////////////////////////////////////////
        //////////      The Properties    /////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

		protected static $oInstance  = null;    // This is our singleton instance
		protected $oDatabaseInstance = null;    // This is our current database connection
        protected $oStatement        = null;    // This is our current working statement

		////////////////////////////////////////////////////////////////////////
        //////////      Singleton Experience    ///////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This sets the singleton pattern instance
         *
         * @return MySql
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
         * @return MySql
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
        //////////      Construct    //////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method currently does nothing
         *
         * @return MySql $this for a fluid and chain-loadable interface
        **/
		protected function __construct() {

            // Return instance
            return $this;
        }

		////////////////////////////////////////////////////////////////////////
        //////////      Public    /////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method sets up the database connection
         *
         * @param array $aDbConfig are the database configuration settings
         * @return MySql $this for a fluid and chain-loadable interface
         */
        public function createConnection(array $aDbConfig) {

			// Setup the database connection
	    	$this->setDatabaseInstance(
                new PDO("mysql:dbname={$aDbConfig['sDatabase']};host={$aDbConfig['sHost']}",
                $aDbConfig['sUsername'],
                $aDbConfig['sPassword']
            ));

	       	// Return instance
	       	return $this;
		}

        public function doSanitize($sData, $sCustomPattern = null) {

            // Check to see if the caller
            // wants to run a custom
            // pattern on the string
            if (is_null($sCustomPattern)) {

                // No custom pattern was provided
                // run a standard HTML/PHP strip
                return strip_tags($sData);
            } else {

                // Run the custom pattern
                return preg_replace($sCustomPattern, null, $sData);
            }
        }

        /**
         * This method executes a query
         *
         * @param string $sQuery is the SQL to execute
         * @return MySql $this for a fluid and chain-loadable interface
        **/
        public function executeQuery($sQuery) {

            // Try to set the statement
            try {
                // Prepare the query
                $this->setStatement($this->getDatabaseInstance()->prepare($sQuery));

            // Catch any and all errors
            } catch (PDOException $oException) {

                // Set the system error
                Framework::getInstance()->setError($oException->getMessage());
            }

            // Try to execute the statement
            try {

                // Execute the query
                $this->getStatement()->execute();

            // Catch any and all errors
            } catch (PDOException $oException) {

                // Set the system error
                Framework::getInstance()->setError($oException->getMessage());

                // Return
                return false;
            }

            // Return instance
            return $this;
        }

        /**
         * This method fetches the next row from an iteration
         *
         * @param boolean $bType determines whether to return an object or an array (default:  object)
         * @return mixed is the next row in the resultset
        **/
        public function fetchResult($bArray = false) {

            // Determine the type of result
            // to return to the caller
            switch ($bArray) {

                case false :    // Object

                    // Return
                    return $this->getStatement()->fetch(PDO::FETCH_OBJ);

                    // End
                    break;

                case true :    // Array

                    // Return
                    return $this->getStatement()->fetch(PDO::FETCH_ASSOC);

                    // End
                    break;
            }
        }

         /**
          * This method fetches all the results from a query
          *
          * @param boolean $bType determines whether to return an object or an array (default:  object)
          * @return array $aResultSet is the array of results
         **/
        public function fetchResultSet($bArray = false) {

            // Results placeholder
            $aResultSet = array();

            // Determine the type of result
            // to return to the caller
            if ($bArray === true) {

                // Loop through the results
                while ($aRow = $this->getStatement()->fetch(PDO::FETCH_ASSOC)) {

                    // Append the row to the set
                    $aResultSet[] = $aRow;
                }
            } else {

                // Loop through the results
                while ($oRow = $this->getStatement()->fetch(PDO::FETCH_OBJ)) {

                    // Append the row to the set
                    $aResultSet[] = $oRow;
                }
            }

            // Return the resultset
            return $aResultSet;
        }

        /**
         * This method gets the last inserted
         * ID in the database
         *
         * @return integer
        **/
        public function lastInsertId() {

            // Return the last inserted ID
            return $this->getDatabaseInstance()->lastInsertId();
        }

        /**
         * This method returns a query based on the ID
         *
         * @param string $sId is the ID of the query to load
         * @return string query to be used
        **/
        public function loadQuery($sQuery) {

            // Check to see that the query the
            // caller wants is actually defined
            if (Framework::getInstance()->loadConfigVar('sqlQueries', $sQuery)) {

                // Return the query
                return (string) Framework::getInstance()->loadConfigVar('sqlQueries', $sQuery);

            // The query does not exist
            } else {

                // Set the system error
                $this->setError("The query '{$sQuery}' does not exist.");
            }
        }

        /**
         * This method prepares a query for execution
         *
         * @param  $sQuery is the query to process
         * @param array $aPlaceholders are the variables to load in
         * @return string $sQuery is the prepared query
        **/
        public function prepareQuery($sQuery, $aPlaceholders = array(), $bEscape = true) {

            // Loop through each of the placeholders
            foreach ($aPlaceholders as $sVariable => $sValue) {

                // Check to see if we must
                // escape the placeholders
                if ($bEscape === true) {

                    // We must escape the data
                    // and load in the placeholders
                    $sQuery = (string) str_replace(":{$sVariable}", $this->getDatabaseInstance()->quote($sValue), $sQuery);

                } else {

                    // We do not need to escape anything
                    // now load in the placeholders
                    $sQuery = (string) str_ireplace(":{$sVariable}", $sValue, $sQuery);
                }
            }

            // Return the query
            return (string) $sQuery;
        }

        public function setupEncryption() {

        }

		////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

		/**
		 * This method sets the current working
		 * MySQL database connection using PDO
		 *
		 * @package Setters
		 * @param PDO $oPdo is an instance of PDO
		 * @return MySql $this for a fluid and chain-loadable interface
		**/
		public function setDatabaseInstance(PDO $oPdo) {

			// Set our database connection
			$this->oDatabaseInstance = $oPdo;

			// Return instance
			return $this;
		}

        /**
         * This method sets our current working statement
         *
         * @param PDOStatement $oStatement is the statement to store
         * @return MySql $this for a fluid and chain-loaadable interface
        **/
        public function setStatement(PDOStatement $oStatement) {

            // Set our current instance
            $this->oStatement = $oStatement;

            // Return instance
            return $this;
        }

		////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

		/**
		 * This method retrieves the current
		 * work database connection
		 *
		 * @package Getters
		 * @return MySql @property oDatabaseInstance is the current working database connection
		**/
		public function getDatabaseInstance() {

			// Return our connection
			return $this->oDatabaseInstance;
		}

        /**
         * Thsi method returns the current working statement
         *
         * @return PDOStatement @property $oStatement is the current working statement
         */
        public function getStatement() {

            // Return the current statement
            return $this->oStatement;
        }
	}
