<?php

class Convert {
    ////////////////////////////////////////////////////////////////////////
    //////////      The Properties    /////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected static $oInstance = null;
    protected $aFromDetails     = null;
    protected $aSchema          = null;
    protected $aToDetails       = null;

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

    public function toMongoFromMySql() {

        // Setup MongoDB Instance
        $this->setupMongo($this->aToDetails);

        // Setup MySQL Instance
        $this->setupMysql($this->aFromDetails);
        

    }

    public function toMySqlFromMongo() {


    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Protected    //////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    protected function setupMongo(array $aDetails) {

        // Setup the Mongo instance
        MongoDbWrapper::getInstance()
            ->setServer(
                $aDetails['sServer']
            );

        // Check for username
        // and password
        if (empty($aDetails['sUsername']) && empty($aDetails['sPassword'])) {

            // Set the DB connection
            // without credentials
            MongoDbWrapper::getInstance()->setDbConnection();

        } else {

            // Set the DB connection
            // with credentials
            MongoDbWrapper::getInstance()
                    ->setDbConnection(
                        array(
                            'username' => $aDetails['sUsername'],
                            'password' => $aDetails['sPassword']
                        )
                    );
        }

        // Set the collection
        MongoDbWrapper::getInstance()
            ->setDbCollection(
                $aDetails['sCollection'],
                $aDetails['sDatabase']
            );
    }

    protected function setupMysql(array $aDetails) {

        // Setup the MySQL instance
        MySql::getInstance()
            ->createConnection(
                array(
                    'sDatabase' => $aDetails['sDatabase'],
                    'sHost'     => $aDetails['sHost'],
                    'sPassword' => $aDetails['sPassword'],
                    'sUsername' => $aDetails['sUsername']
                )
            );
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Setters    ////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    public function setFromConnection(array $aCredentials) {

        // Set the credentials
        // and connection info
        $this->aFromDetails = (array) $aCredentials;

        // Return instance
        return $this;
    }

    public function setSchema(array $aSchema) {

        // Set the schema
        // into the system
        $this->aSchema = (array) $aSchema;

        // Return instance
        return $this;
    }

    public function setToConnection(array $aCredentials) {

        // Set the credentials
        // and connection info
        $this->aToDetails = (array) $aCredentials;

        // Return instance
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////
    //////////      Getters    ////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    public function getFromConnection() {

        // Get the credentials
        // and connection info
        return $this->aFromDetails;
    }

    public function getSchema() {

        // Get the schema
        // into the system
        return $this->aSchema;
    }

    public function getToConnection() {

        // Get the credentials
        // and connection info
        return $this->aToDetails;
    }
}