<?php
/**
 * @package MongoDbWrapper
 * @author Travis Brown <tmbrown6@gmail.com>
 * @copyright Travis Brown
 * @licence http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * MongoWrapper is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 *
 * @description MongoWrapper is a Mongo Database Connection Wrapper.  It is setup to follow
 * the singleton pattern for efficient resource usage as well as getter and setter
 * methods to easily allow for external modification as well as testing and mocking.
 */
class MongoDbWrapper {
	////////////////////////////////////////////////////////////////////////
	//////////	Properties	///////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	protected static $oInstance;	// Singleton
	protected $aAllResultSets;		// Array of result set objects
	protected $iCount;				// Record count
	protected $oDatabase;			// Database object
	protected $oDbCollection;		// Database Collection
	protected $oDbConnection;		// Database connection
	protected $sDbError;			// Mongo error
	protected $sError;				// Current error
	protected $iLimit;				// Record limit
	protected $oResultSet;			// Database result set object
	protected $sServer;			    // Server
	protected $aSortSet;			// Sort parameters
	////////////////////////////////////////////////////////////////////////
	//////////      Static Methods      ///////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This sets the singleton pattern instance
	 * @return MongoDbWrapper
	**/
	public static function setInstance() {
		// Try to set an instance
		try {
			// Set instance to new self
			// new self() is the same as new MongoWrapper()
			self::$oInstance = new self();
		// Catch any exceptions
		} catch (Exception $oException) {
			// Set error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return instance of class
		return self::$oInstance;
	}
	/**
	 * This gets the singleton instance
	 * @return MongoDbWrapper
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
	////////////////////////////////////////////////////////////////////////
	//////////      Public Methods      ///////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This is our constructor.  You can use this directly if you do
	 * not wish to access this class as a singleton
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	**/
	public function __construct() {
		// Try our connection
		try {
			// Set the default Server
			$this->setServer();
			// Connect to the Server
			$this->setDbConnection();
		// Catch any Mongo exceptions
		} catch (MongoConnectionException $oException) {
			// Set our error string
			$this->setError("Error:  Could not conenct to Mongo!:  {$oException->getMessage()}");
		// Catch any other Mongo exceptions while killing the connection
		} catch (MongoException $oException) {
			// Set our error string
			$this->setError("Error:  {$oException->getMessage()}");
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method allows us to close the Mongo database object
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	**/
	public function doClose() {
		// Try to close our connection
		try {
			// Grab the connection and close it
			$this->getDbConnection()->close();
		// Catch any Mongo exceptions
		} catch (MongoException $e) {
			// Set our error string
			$this->setError("Error:  {$e->getMessage()}");
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method inserts a record into the database collection
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params array $aDbObject is an array of data to store
	**/
	public function doInsert($aDbObject) {
		// Try to get our database collection and
		// insert a record
		try {
			// Grab the database collection and save the
			// array to the database
			$this->getDbCollection()->save($aDbObject);
		// Catch any Mongo exceptions
		} catch (MongoException $oException) {
			// Set our error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method updates a MongoDB record
	 * @param array $aDbObject
	 * @param array $aCriteria
	 * @return MongoDbWrapper $this
	**/
	public function doUpdate(array $aDbObject,array $aCriteria) {
		// Try to get our database
		// collection and update a
		// record
		try {
			// Grab the database collection and
			// save the record into the database
			$this->getDbCollection()->update($aDbObject, array(
				'$set' => $aCriteria
			));
		} catch (MongoException $oException) {
			// Set system error
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return instance
		return $this;
	}
	/**
	 * This method does a
	 * single join at a time
	 * @param str $sTable
	 * @param array $aParameters
	 * @return MongoDbWrapper|bool $this|false
	**/
	public function doJoin($sTable, array $aParameters, $bUseAliases = true) {
		// Result set placeholder
		$aResultSets = array();
		// Loop through the parent
		// keys (table names) in
		// the array
		// Check for needed data
		// Foreign Key
		if (empty($aParameters['ForeignKey'])) {
			// No foreign key, set system error
			Framework::getInstance()->setError(Framework::getInstance()->loadConfigVar('mongoErrorMessages', 'noJoinForeignKey'));
			// Return
			return false;
		}
		// Join On
		if (empty($aParameters['JoinOn'])) {
			// No join on field, set system error
			Framework::getInstance()->setError(Framework::getInstance()->loadConfigVar('mongoErrorMessages', 'noJoinOn'));
			// Return
			return false;
		}
		// Join With
		if (empty($aParameters['JoinWith'])) {
			// No join table, set system error
			Framework::getInstance()->setError(Framework::getInstance()->loadConfigVar('mongoErrorMessages', 'noJoinWith'));
			// Return
			return false;
		}
		// check for a WHERE clause
		if (empty($aParameters['Where'][$sTable])) {
			// No where clause
			$aWhere = array();
		} else {
			// There was a where clause
			$aWhere = $aParameters['Where'][$sTable];
		}
		// Check for fields to return
		if (empty($aParameters['Fields'][$sTable])) {
			// No fields parameters,
			// set to empty array
			$aFields = array();
		} else {
			// Set the array to
			// the fields parameters
			$aFields = $aParameters['Fields'][$sTable];
		}
		// Execute the query
		$this->setDbCollection($sTable)->goFind($aWhere, $aFields);
		// Get all of the result sets
		$this->setAllResultSets();
		// Loop through the results
		// and append them to the
		// result set array
		foreach ($this->getAllResultSets() as $aResultSet) {
			// Where clause
			if (empty($aParameters['Where'][$aParameters['JoinWith']])){
				// No where clause
				$aWhere = array(
					$aParameters['ForeignKey'] => $aResultSet[$aParameters['JoinOn']]
				);
			} else {
				// A Pre-existing where clause
				$aWhere                             = $aParameters['Where'][$aParameters['JoinWith']];
				$aWhere[$aParameters['ForeignKey']] = $aResultSet[$aParameters['JoinOn']];
			}
			// Fields to return
			if (empty($aParameters['Fields'][$aParameters['JoinWith']])) {
				// Set the fields to empty
				$aFields = array();
			} else {
				// Set the fields array
				$aFields = $aParameters['Fields'][$aParameters['JoinWith']];
			}
			// Get the join results
			$aJoinResults = $this->setDbCollection($aParameters['JoinWith'])->goFindOne($aWhere, $aFields)->getResultSet();
			// Check for a Primary Table Alias
			if ($bUseAliases === true) {
				if (empty($aParameters['Aliases'][$sTable])) {
					// Append the table name
					// as the alias
					$sAlias = $sTable;
				} else {
					// Set it to the custom alias
					$sAlias = $aParameters['Aliases'][$sTable];
				}
				// Check for a JoinWith Table Alias
				if (empty($aParameters['Aliases'][$aParameters['JoinWith']])) {
					// Append the table name
					// as the alias
					$sForeignAlias = $aParameters['JoinWith'];
				} else {
					// Set it to the custom alias
					$sForeignAlias = $aParameters['Aliases'][$aParameters['JoinWith']];
				}
			}
			// Set the placeholder array
			$aFinal = array();
			// Loop through the primary results
			// and append them to the array
			foreach ($aResultSet as $sField => $mValue) {
				// Append the field with the alias
				if ($bUseAliases === true) {
					$aFinal["{$sAlias}.{$sField}"] = $mValue;
				} else {
					$aFinal[$sField] = $mValue;
				}
			}
			// Loop through the join table
			// results and append them
			foreach ($aJoinResults as $sField => $mValue) {
				// Append the field with the alias
				if ($bUseAliases === true) {
					$aFinal["{$sForeignAlias}.{$sField}"] = $mValue;
				} else {
					$aFinal[$sField] = $mValue;
				}
			}
			// Append this result to the set
			$aResultSets[] = $aFinal;
		}
		// Check for single results
		foreach ($aResultSets as $sTable => $aFields) {
			// Check to see if there
			// is only one record
			if (count($aResultSets[$sTable]) === 1) {
				// Set the record
				$aResultSets[$sTable] = $aResultSets[$sTable][0];
			}
		}
		// Set the results into the system
		$this->aAllResultSets = $aResultSets;
		// Return instance
		return $this;
	}
	/**
	 * This method takes an array of data
	 * and performs a join on each of the
	 * parent keys
	 * @param array $aJoins
	 * @return MongoDbWrapper $this
	**/
	public function doJoins(array $aJoins) {
		// Result set placeholder
		$aResultSets = array();
		// Loop through the parent
		// keys (table names) in
		// the array
		foreach ($aJoins as $sTable => $aParameters) {
			// Check for needed data
			// Foreign Key
			if (empty($aParameters['ForeignKey'])) {
				// No foreign key, set system error
				Framework::getInstance()->setError(Framework::getInstance()->loadConfigVar('mongoErrorMessages', 'noJoinForeignKey'));
				// Return
				return false;
			}
			// Join On
			if (empty($aParameters['JoinOn'])) {
				// No join on field, set system error
				Framework::getInstance()->setError(Framework::getInstance()->loadConfigVar('mongoErrorMessages', 'noJoinOn'));
				// Return
				return false;
			}
			// Join With
			if (empty($aParameters['JoinWith'])) {
				// No join table, set system error
				Framework::getInstance()->setError(Framework::getInstance()->loadConfigVar('mongoErrorMessages', 'noJoinWith'));
				// Return
				return false;
			}
			// check for a WHERE clause
			if (empty($aParameters['Where'][$sTable])) {
				// No where clause
				$aWhere = array();
			} else {
				// There was a where clause
				$aWhere = $aParameters['Where'][$sTable];
			}
			// Check for fields to return
			if (empty($aParameters['Fields'][$sTable])) {
				// No fields parameters,
				// set to empty array
				$aFields = array();
			} else {
				// Set the array to
				// the fields parameters
				$aFields = $aParameters['Fields'][$sTable];
			}
			// Execute the query
			$this->setDbCollection($sTable)->goFind($aWhere, $aFields);
			// Get all of the result sets
			$this->setAllResultSets();
			// Loop through the results
			// and append them to the
			// result set array
			foreach ($this->getAllResultSets() as $aResultSet) {
				// Where clause
				if (empty($aParameters['Where'][$aParameters['JoinWith']])){
					// No where clause
					$aWhere = array(
						$aParameters['ForeignKey'] => $aResultSet[$aParameters['JoinOn']]
					);
				} else {
					// A Pre-existing where clause
					$aWhere                             = $aParameters['Where'][$aParameters['JoinWith']];
					$aWhere[$aParameters['ForeignKey']] = $aResultSet[$aParameters['JoinOn']];
				}
				// Fields to return
				if (empty($aParameters['Fields'][$aParameters['JoinWith']])) {
					// Set the fields to empty
					$aFields = array();
				} else {
					// Set the fields array
					$aFields = $aParameters['Fields'][$aParameters['JoinWith']];
				}
				// Get the join results
				$aJoinResults = $this->setDbCollection($aParameters['JoinWith'])->goFindOne($aWhere, $aFields)->getResultSet();
				// Check for a Primary Table Alias
				if (empty($aParameters['Aliases'][$sTable])) {
					// Append the table name
					// as the alias
					$sAlias = $sTable;
				} else {
					// Set it to the custom alias
					$sAlias = $aParameters['Aliases'][$sTable];
				}
				// Check for a JoinWith Table Alias
				if (empty($aParameters['Aliases'][$aParameters['JoinWith']])) {
					// Append the table name
					// as the alias
					$sForeignAlias = $aParameters['JoinWith'];
				} else {
					// Set it to the custom alias
					$sForeignAlias = $aParameters['Aliases'][$aParameters['JoinWith']];
				}
				// Set the placeholder array
				$aFinal = array();
				// Loop through the primary results
				// and append them to the array
				foreach ($aResultSet as $sField => $mValue) {
					// Append the field with the alias
					$aFinal["{$sAlias}.{$sField}"] = $mValue;
				}
				// Loop through the join table
				// results and append them
				foreach ($aJoinResults as $sField => $mValue) {
					// Append the field with the alias
					$aFinal["{$sForeignAlias}.{$sField}"] = $mValue;
				}
				// Append this result to the set
				$aResultSets[$sTable][] = $aFinal;
			}
		}
		// Check for single results
		foreach ($aResultSets as $sTable => $aFields) {
			// Check to see if there
			// is only one record
			if (count($aResultSets[$sTable]) === 1) {
				// Set the record
				$aResultSets[$sTable] = $aResultSets[$sTable][0];
			}
		}
		// Set all of the result sets
		$this->aAllResultSets = $aResultSets;
		// Return instance
		return $this;
	}
	/**
	 * This method deletes a record from the database collection
	 * @return MongoDbWrapper $this for chainloadable and fluent interface
	 * @params array $aDbObject is an array of data to remove
	**/
	public function goDelete($aDbObject) {
		// Try to get our database collection and
		// remove a record
		try {
			// Grab the database collection and remove the
			// record from the database
			$this->getDbCollection()->remove($aDbObject);
		// Catch any Mongo exceptions
		} catch (MongoException $oException) {
			// Set our error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method queries the database for all the
	 * records that meet the acceptance criteria
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params array $aQuery is an array of fields and values
	 * @params array $aFields is an array of fields to show
	**/
	public function goFind($aQuery = array(), $aFields = array()) {
		// Try to run the query
		try {
			// Set the initial result set
			if (empty($aFields)) {
				$this->setResultSet($this->getDbCollection()->find($aQuery));
			} else {
				$this->setResultSet($this->getDbCollection()->find($aQuery, $aFields));
			}
			// If sorting parameters are set
			if ($this->getSortSet()) {
				// Reset the result set to the sorted query
				$this->setResultSet($this->getResultSet()->sort($this->getSortSet()));
			}
			// If a limit is set
			if ($this->getLimit()) {
				// Reset the result set to the limited query
				$this->setResultSet($this->getResultSet()->limit($this->getLimit()));
			}
		// Catch any Mongo exceptions
		} catch (MongoException $oException) {
			// Set our error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method is used to authenticate a user
	 * for our set database collection
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params string $sUsername is the username (cleartext)
	 * @params string $sPassword is the password (cleartext)
	**/
	public function doAuthenticate() {
		// Try to authenticate the user
		try {
			// Grab our database collection and send it the
			// authentication information
			$this->getDbConnection()->authenticate(
				Framework::getInstance()->loadConfigVar(
					'database',
					'sUsername'
				),
				Framework::getInstance()->loadConfigVar(
					'database',
					'sPassword'
				)
			);
		// Catch any Mongo exceptions
		} catch (MongoException $oException) {
			// Set our error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method queries the database for one of
	 * the records that meet the acceptance criteria
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params array $aQuery is an array of fields and values
	 * @params array $aFields is an array of fields to show
	**/
	public function goFindOne($aQuery = array(), $aFields = array()) {
		// Try to find a record
		try {
			// Query the database collection with our
			// set parameters
			if (empty($aFields)) {
				$this->setResultSet($this->getDbCollection()->findOne($aQuery));
			} else {
				$this->setResultSet($this->getDbCollection()->findOne($aQuery, $aFields));
			}
		// Catch any Mongo exceptions
		} catch (MongoException $oException) {
			// Set our error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method gets the count of the records
	 * in the database collection based on query
	 * parameters
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params array $aQuery is an array of query parameters
	**/
	public function doCount($aQuery = array()) {
		// Try to get our count
		try {
			// Grab our database collection and
			// run the count and set our count
			$this->setCount($this->getDbCollection()->count($aQuery));
		// Catch any Mongo exceptions
		} catch (MongoException $oException) {
			// Set our error string
			Framework::getInstance()->setError($oException->getMessage());
		}
		// Return class instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Setter Methods      ///////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method takes the $_oResultSet iterator
	 * and iterates through and appends each object
	 * into and array
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	**/
	public function setAllResultSets() {
		// Create an empty array to store
		// the iterations
		$this->aAllResultSets = array();
		// Iterate through $_oResultSet
		foreach ($this->getResultSet() as $aSet) {
			// Push the current iteration to the
			// $_aAllResultSets array
			$this->aAllResultSets[] = $aSet;
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method sets the current database
	 * collection record count
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params integer $iCount is the actual number the count returns
	**/
	public function setCount($iCount) {
		// Set $_iCount to $iCount
		$this->iCount = $iCount;
		// Return class instance
		return $this;
	}
	/**
	 * This method selects the database in which
	 * we wish to use
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params string $sDbName is the name of the database to select
	**/
	public function setDatabase($sDbName) {
		// Set $_oDatabase to an instance of MongoDB with a
		// database of $sDbName
		$this->oDatabase = $this->getDbConnection()->selectDB($sDbName);
		// Return class instance
		return $this;
	}
	/**
	 * This method sets the database collection in
	 * which we wish to use
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params string $sDbName is the name of the database to select
	 * @params string $sDbCollection is the name of the database collection to select
	**/
	public function setDbCollection($sCollectionName, $sDbName = null) {
		// Check to see if we want to use
		// a custom db or the defined db
		if (empty($sDbName)) {
			$sDbName = Framework::getInstance()->loadConfigVar(
				'database',
				'sDatabase'
			);
		}
		// Set our database to $sDbName
		$this->setDatabase($sDbName);
		// Set our database collection to and instance
		// of MongoCollection with a database of $sDbName
		// and a collection of $sDbCollection
		$this->oDbCollection = $this->getDbConnection()->selectCollection($sDbName, $sCollectionName);
		// Return class instance
		return $this;
	}
	/**
	 * This method is responsible for creating the
	 * actual connection to the Mongo server
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params array $aOptions is an array of Mongo server options
	 * @params object $oDatabaseConnection is an instance of a pre-existing database connection
	 */
	public function setDbConnection($aOptions = array(), $oDbConnection = null) {
		// Check to see if we are providing a
		// pre-existing database connection
		if (empty($oDbConnection)) {
			// If not set $_oDbConnection to a new
			// instance of Mongo
			$this->oDbConnection = new Mongo($this->getServer(), $aOptions);
		} else {
			// If so set $_oDbConnection to $oDbConnection
			$this->oDbConnection = $oDbConnection;
		}
		// Return class instance
		return $this;
	}
	/**
	 * This method sets the current error
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params string $sError is the error description text
	**/
	public function setError($sError) {
		// Set $_sError to $sError
		$this->sError = $sError;
		// Return class instance
		return $this;
	}
	/**
	 * This method sets the limit of the current
	 * query result set
	 *
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params integer $iLimit is the actual limit of the record set
	**/
	public function setLimit($iLimit) {
		// Set $_iLimit to $iLimit
		$this->iLimit = $iLimit;
		// Return class instance
		return $this;
	}
	/**
	 * This method sets the current result set iterator
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params object $oResultSet is the current result set iterator
	**/
	public function setResultSet($oResultSet) {
		// Set $_oResultSet to $oResulSet
		$this->oResultSet = $oResultSet;
		// Return class instance
		return$this;
	}
	/**
	 * This method sets the address of the Mongo
	 * server in which we wish to connect to
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	**/
	public function setServer($sServer = null) {
		// Check to see if a server address
		// was actually sent
		if (empty($sServer)) {
			// No custom server was set,
			// load our connection from
			// the local configuration
			$this->sServer = 'mongodb://'.Framework::getInstance()->loadConfigVar(
				'database',
				'sHost'
			).'/';
		} else {
			// Set the custom server
			$this->sServer = $sServer;
		}
		// Return intance of class
		return $this;
	}
	/**
	 * This method sets the sort parameters for
	 * the current database collection
	 * @return MongoDbWrapper $this for a chainloadable and fluent interface
	 * @params array $aSortSet is an associative array of sorting parameters
	**/
	public function setSortSet($aSortSet) {
		// Set $_aSortSet to $aSortSet
		$this->aSortSet = $aSortSet;
		// Return class instance
		return $this;
	}
	////////////////////////////////////////////////////////////////////////
	//////////      Getter Methods      ///////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	/**
	 * This method returns the entire result set
	 * @return array $_aAllResultSets
	**/
	public function getAllResultSets() {
		// Return $_aAllResultSets
		return $this->aAllResultSets;
	}
	/**
	 * This method returns a MongoBinary
	 * binary data object
	 * @param resource $rData
	 * @param bool $bToString
	 * @return MongoBinData
	**/
	public function getBinary($rData, $iType = 2, $bToString = false) {
		// Set the data
		$oBinary = new MongoBinData($rData, $iType);
		// Check to see if we need
		// to return it as a string
		if ($bToString === true) {
			// Return string
			return $oBinary->__toString();
		} else {
			// Return binary object
			return $oBinary;
		}
	}
	/**
	 * This method returns the current database
	 * collection record count
	 * @return integer $_iCount
	**/
	public function getCount() {
		// Return $_iCount
		return $this->iCount;
	}
	/**
	 * This method returns the current database object
	 * @return MongoDB @property $oDatabase
	**/
	public function getDatabase() {
		return $this->oDatabase;
	}
	/**
	 * This method returns a
	 * MongoDB date object
	 * @param str $sDate
	 * @return MongoDate
	**/
	public function getDate($sDate = null) {
		// Check for a date to convert
		if (empty($sDate)) {
			// We want a new date for now
			return new MongoDate();
		} else {
			// Conver the date
			return new MongoDate(strtotime($sDate));
		}
	}
	/**
	 * This method returns the current database collection object
	 * @return MongoCollection @property $oDbCollection
	**/
	public function getDbCollection() {
		// Return $_oDbCollection
		return $this->oDbCollection;
	}
	/**
	 * This method returns the current database connection
	 * @return MongoDbWrapper $_oDbConnection instance of Mongo
	 */
	public function getDbConnection() {
		return $this->oDbConnection;
	}
	/**
	 * This method returns the current error
	 * @return string $_sError
	 */
	public function getError() {
		// Return $_sError string
		return $this->sError;
	}
	/**
	 * This method returns a
	 * MongoDB ID object
	 * @param str $sObjectId
	 * @param bool $btoString
	 * @return MongoId
	**/
	public function getId($sObjectId = null, $bToString = false) {
		// Check for an existing ID
		if (empty($sObjectId)) {
			// Create a new ID
			$oId = new MongoId();
		} else {
			// Convert the ID string
			$oId = new MongoId($sObjectId);
		}
		// See if we need to
		// return it to string
		if ($bToString === true) {
			// Return the string
			return $oId->__toString();
		} else {
			// Return the object
			return $oId;
		}
	}
	/**
	 * This method returns the current record set limit
	 * @return integer $_iLimit
	**/
	public function getLimit() {

		// Return $_iLimit
		return $this->iLimit;
	}
	/**
	 * This method return a
	 * MongoRegex pattern object
	 * @param str $sPattern
	 * @return MongoRegex
	**/
	public function getRegularExpression($sPattern) {
		return new MongoRegex($sPattern);
	}
	/**
	 * This method returns the current result set iterator
	 * @return MongoDbWrapper $_oResultSet instance of MongoCursor
	**/
	public function getResultSet() {
		// Return $_oResultSet
		return $this->oResultSet;
	}
	/**
	 * This method returns the address of the currently conected server
	 * @return string $_sServer
	**/
	public function getServer() {
		// Return $_sServer
		return $this->sServer;
	}
	/**
	 * This method returns the current result set
	 * sort parameters
	 * @return array $_aSortSet
	**/
	public function getSortSet() {
		// Return $_aSortSet
		return $this->aSortSet;
	}
}
