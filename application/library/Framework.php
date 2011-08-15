<?php 

	class Framework {
	
		////////////////////////////////////////////////////////////////////////
        //////////      The Properties    /////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
		
		protected static $oInstance = null;	// This is our singleton instance
        protected $oCache           = null; // This is our instance of Cache
		protected $aConfig          = null;	// This is our configuration
		protected $sConfigFile      = null;	// This is our configuration file
		protected $sController      = null; // This is the action to render
		protected $oDatabase        = null;	// This is our database object
		protected $sError           = null;	// This is our system error
		protected $sLayout          = null; // This is the layout we wish to load
		protected $oPage            = null; // This is the page class object
		protected $oPostData        = null;	// This is our POST object
		protected $oQueryParams     = null; // This is our custom $_GET object
        protected $sRouter          = null; // This is a prefix to append to the controllers and views
		protected $oServerData      = null;	// This is our SERVER object
		protected $sView            = null; // This is the current view to render
		protected $oView            = null; // This is the scope for the view files
	
		////////////////////////////////////////////////////////////////////////
        //////////      Singleton Experience    ///////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
        /**
         * This sets the singleton pattern instance
         *
         * @return Framework
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
         * @return Framework
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
         * This method runs all of the instantiation 
         * actions when the caller grabs an instance
         * 
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        protected function __construct() {
        	// Set the configuration file
        	$this->setConfigFile(APPLICATIONPATH.'/configurations/config.ini');
        	// Load the configuration
        	// into the system
        	$this->readConfig();
            // Setup the database
            // $this->setDatabase();
            // Setup our caching system
            // $this->setCache();
        	// Return instance
        	return $this;
        }
        
        ////////////////////////////////////////////////////////////////////////
        //////////      Config    /////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
        /**
         * This method loads a specified configuration
         * section and/or optional variable
         * 
         * @package Config
         * @param string $sSection is the section to look for the variable in
         * @param string $sVariable is the variable to return
         * @return mixed is the configuration secion or variable
        **/
        public function loadConfigVar($sSection, $sVariable = null) {
        	// Load configuration
        	$aConfig = $this->getConfig();
            // Make sure the section exists
            if (isset($aConfig[$sSection])) {
                // Check to see if the caller just
                // wants a section or if they want
                // a specific variable as well
                if (is_null($sVariable)) {
                    // Return the configuration
                    // section
                    return $aConfig[$sSection];
                // The caller wants a
                // specific variable
                } else {
                    // Check to see if the variable is set
                    if (isset($aConfig[$sSection][$sVariable]) && !is_null($aConfig[$sSection][$sVariable])) {
                        // Return the configuration
                        // section and variable
                        return $aConfig[$sSection][$sVariable];
                    // The variable does not exist
                    } else {
                        // Set the system error
                        $this->setError(
                            str_replace(
                                ':variableName',
                                $sVariable,
                                $aConfig['errorMessages']['configVariableDoesNotExist']
                            )
                        );
                        // Return
                        return false;
                    }
                }
            // The section does not exist
            } else {
                // Set the system error
                $this->setError(
                    str_replace(
                        ':sectionName',
                        $sSection,
                        $aConfig['errorMessages']['configSectionDoesNotExist']
                    )
                );
                // Return
                return false;
            }
        }
        
        /**
         * This method reads a configuration file
         * into the system
         * 
         * @package Config
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function readConfig() {
        	// Read the current configuration file
        	$this->setConfig(parse_ini_file($this->getConfigFile(), true));
        	// Return instance 
        	return $this;
        }
        
        ////////////////////////////////////////////////////////////////////////
        //////////      Debug    //////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
        /**
         * This method renders debug output of 
         * variables to the viewer
         * 
         * @package Debug
         * @param mixed $mVariable is the object or array to render
         * @param boolean $bTerminal determines whether the application should be terminated upon render or not
         * @return Framework $this for a fluid and chain-loadable interface
         */
        public function showDebug($mVariable, $bRecursive = false, $bTerminal = false) {
                // Start the outpur
	            echo('<pre>');
	            // Check for xDebug
	            if (function_exists('xdebug_var_dump') && $bRecursive === false) {
                    // Render the variable
        	        var_dump($mVariable);
	            } else {
		            // xDebug is unavailable
		            // use recursive
		            print_r($mVariable);
	            }
	            // End the output
	            echo('</pre>');
        	// Determine if the application
        	// should be terminated after 
        	// render is complete
        	if ($bTerminal === true) {
        		// This debug is terminal, 
        		// kill the application
        		exit;
        	}
        	// Return instance
        	return $this;
        }
        
        ////////////////////////////////////////////////////////////////////////
        //////////      The Router    /////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
        /**
         * This method builds the query and routes
         * the request to the proper view(s)
         * 
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function executeRoute() {
            // Load the URI
            $aTempQueryParams = explode('/', $_SERVER['REQUEST_URI']);
            // Query Parameters placeholder
            $aQueryParams = array();
            // Check for a trailing slash
            if (empty($aTempQueryParams[(count($aTempQueryParams) - 1)])) {
                // Remove trailing slash
                array_pop($aTempQueryParams);
            }
            // Remove the first key
            array_shift($aTempQueryParams);
            // Check for router
            if (!empty($aTempQueryParams[0]) && in_array($aTempQueryParams[0], $this->loadConfigVar('systemSettings', 'urlRouters'))) {
                // Set the router
                $this->setRouter($aTempQueryParams[0]);
                // Remove router
                array_shift($aTempQueryParams);
            }
            // Set the controller
            if (empty($aTempQueryParams) || ($aTempQueryParams[0] == '/')) {
                $this->setController('index');
            } else {
                $this->setController($aTempQueryParams[0]);
            }
            // Remove controller
            if (!empty($aTempQueryParams)) {
                array_shift($aTempQueryParams);
            }
            // Check for leftovers
            if (!empty($aTempQueryParams)) {
                // The rest are key value
                // variable pairs, loop
                // through them
                for ($iInc = 0; $iInc < count($aTempQueryParams); $iInc += 2) {
                    // Append the query parameter
                    $aQueryParams[$aTempQueryParams[$iInc]] = (isset($aTempQueryParams[($iInc + 1)]) ? $aTempQueryParams[($iInc + 1)] : null);
                    // Make it accessible from $_GET as well
                    $_GET[$aTempQueryParams[$iInc]] = (isset($aTempQueryParams[($iInc + 1)]) ? $aTempQueryParams[($iInc + 1)] : null);
                }
                // Set the Query Parameters
                $this->setQueryParams($aQueryParams);
            }
            // Set the layout
        	$this->setLayout();
        	// Set the view
        	$this->setView("{$this->getController()}.php");
        	// Load the controller
        	$this->runController();
        	// Return instance
        	return $this;
        }

        /**
         * This method logs the visit of each user
         *
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function logVisit() {
            // Log the visitor
            MongoDbWrapper::getInstance()
                    ->setServer()
                    ->setDbConnection(
                        array(
                            'username' => $this->loadConfigVar(
                                'database',
                                'sUsername'
                            ),

                            'password' => $this->loadConfigVar(
                                'database',
                                'sPassword'
                            )
                        )
                    )
                    ->setDbCollection('Visitors')
                    ->doInsert(
                        array(
                            'sVisitorId'  => MongoDbWrapper::getInstance()->getId(), 
                            'sIpAddress'  => $_SERVER['REMOTE_ADDR'],
                            'sRouter'     => $this->getRouter(),
                            'sController' => $this->getController(),
                            'sUri'        => $_SERVER['REQUEST_URI'],
                         // 'sReferrer'   => $_SERVER['HTTP_REFERER'],
                            'sUserAgent'  => $_SERVER['HTTP_USER_AGENT'],
                            'sVisited'    => MongoDbWrapper::getInstance()->getDate(),
                            'oServerData' => json_encode($_SERVER)
                        )
                    );
            // Return instance
            return $this;
        }
        
        /**
         * This method is responsible for loading
         * in the selected layout
         * 
         * @package Renderers
         * @param string $sLayout is the layout to load
         * @return Framework $this for a fluid and chainloadable interface
        **/
        public function renderLayout() {
        	// See if we need to autoload the layout
        	if (!is_null($this->getLayout())) {
                // Check for a router
                if (is_null($this->getRouter())) {
        		    // Set the layout file
        		    $sFile = APPLICATIONPATH."/layouts/{$this->loadConfigVar('systemSettings', 'defaultViewsFolder')}/{$this->getLayout()}";
                } else {
                    // Set the layout file with the router
                    $sFile = APPLICATIONPATH."/layouts/{$this->getRouter()}/{$this->getLayout()}";
                }
                // Check to see if the file exists
                if (file_exists($sFile)) {
                    // Load it
                    require_once($sFile);
                // File does not exist
                } else {
                    // Set the system error
                    $this->setError(
						str_replace(
							':layoutName', 
							$this->getLayout(), 
							$this->loadConfigVar(
								'errorMessages', 
								'layoutDoesNotExist'
							)
						)
					);
					// Run the error controller
					// $this->runError();
                }
        	}
        	// Return instance
        	return $this;
        }
        
        /**
         * This method renders a specific
         * view file
         * 
         * @package Renderers
         * @param string $sView is the view file to render
         * @param object $oScope is an instance of Framework
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function renderView($sView = null, $oScope = null) {
				
			// There are no errrors, now
            // see if we need to autoload the view
            if (empty($sView)) {

                // Check for a router
                if (is_null($this->getRouter())) {

                   	// Set the template file
                   	$sFile = APPLICATIONPATH."/views/{$this->loadConfigVar('systemSettings', 'defaultViewsFolder')}/{$this->getView()}";
                } else {

                   	// Set the template file with router
                   	$sFile = APPLICATIONPATH."/views/{$this->getRouter()}/{$this->getView()}";
               	}

            } else {

           		// Set the template file
               	$sFile = APPLICATIONPATH."/views/{$sView}";
           	}

           	// Check to see if the file exists
           	if (file_exists($sFile)) {

               	// Load it
               	require_once($sFile);

            	// File does not exist
            } else {

               	// Set the system error
               	$this->setError(
					str_replace(
						':viewName', 
						$this->getView(), 
						$this->loadConfigVar(
							'errorMessages', 
							'viewDoesNotExist'
						)
					)
				);

				// Run the error controller
				// $this->runError();
           	}
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method loads the controller and template into memory
         * 
         * @package Router
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function runController() {
            // Check for a router
            if (is_null($this->getRouter())) {
        	    // Set the method to load
        	    $sController = ucwords("{$this->getController()}Controller");
            } else {

                // Set the controller with the router
                $sController = ucwords("{$this->getController()}".ucfirst($this->getRouter())."Controller");
            }
        	// Check for class
        	if (class_exists($sController, true)) {
        		// Set a new instance of Page
        		$this->setPage(new Page());
        		// The class exists, load it
        		$oController = new $sController();
				// Now check for the proper method 
				// inside of the controller class
				if (method_exists($oController, $this->loadConfigVar('systemSettings', 'controllerLoadMethod'))) {
					// We have a valid controller, 
					// execute the initializer
					$oController->init($this);
					// Set the variable scope
	        		$this->setViewScope($oController);
	        		// Render the layout
	        		$this->renderLayout();
				} else {
					// The initializer does not exist, 
					// which means an invalid controller, 
					// so now we let the caller know
					$this->setError($this->loadConfigVar('errorMessages', 'invalidController'));
					// Run the error
					// $this->runError();
				}
        	// The class does not exist
        	} else {
				// Set the system error
	    		$this->setError(
					str_replace(
						':controllerName', 
						$sController, 
						$this->loadConfigVar(
							'errorMessages', 
							'controllerDoesNotExist'
						)
					)
				);
        		// Run the error
				// $this->runError();
        	}
        	// Return instance
        	return $this;
        }

		public function runError() {
			
			// Set the error into a session
			$this->setSession('sError', $this->getError());

			// Set the router
			redirect(
				$this->getUrl(
					array(
						'sRouter'     => 'error', 
						'sController' => 'index'
					)
				)
			);
		}

        /**
         * This method is just a simple wrapper
         * for the PHP mail() function
         *
         * @param string $sTo is the recipient
         * @param string $sFromName is the name of the sender
         * @param string $sFromEmail is the address of the sender
         * @param string $sSubject is the subject of the message
         * @param string $sContent is the body of the message
         * @return Framework $this for a fluid and chain-loadable interface
         **/
        public function sendMail($sTo, $sFromName, $sFromEmail, $sSubject, $sContent) {

            // Send the message
            mail($sTo, $sSubject, $sContent, "From:  {$sFromName} <{$sFromEmail}>");

            // Return instance
            return $this;
        }
        
        ////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
        /**
         * This method sets up our caching system
         *
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setCache() {
            
            // Set our cache to an instance of Cache
            $this->oCache = Cache::getInstance();
            
            // Return instance
            return $this;
        }
        
        /**
         * This method sets the current working
         * configuration into the system
         * 
         * @package Setters
         * @param array $aConfig is the read configuration file
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setConfig(array $aConfig) {
        	
        	// Set configuration
        	$this->aConfig = (array) $aConfig;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the location of the 
         * current working configuration file
         *
         * @package Setters
         * @param string $sConfigFile is the path to the config file
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setConfigFile($sConfigFile) {
        	
        	// Set our configuration file
        	$this->sConfigFile = (string) $sConfigFile;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the view controller which 
         * determines which template method to load
         * 
         * @package Setters
         * @param string $sController is the template method to load
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setController($sController) {
        	
        	// Set our controller
        	$this->sController = (string) strtolower($sController);
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the system database connection
         * 
         * @package Setters
         * @param MongoDB|PDO $oPdo is an instance of PDO
        **/
        public function setDatabase() {

            // Database configs
            $aDbConfig = $this->loadConfigVar('database');

            // Make sure there are database configurations
            if (empty($aDbConfig)) {

                // Set the system error
                $this->setError($this->loadConfigVar('errorMessages', 'noDatabaseConfiguration'));

            } else {

                // Determine the database connection type
                switch ($aDbConfig['sArchitecture']) {

                    case 'mongo' :

                        // Set our MongoDB instance
                        $this->oDatabase = MongoDb::getInstance();

                        // Set the server
                        $this->oDatabase->setServer()
                                ->setDbConnection();

                        // Try to authenticate
                        if (!$this->oDatabase->doAuthenticate()) {
                            return false;
                        }

                        break;

                    case 'mysql' :

                        // Set the database
                        $this->oDatabase = MySql::getInstance();

                        // Authenticate
                        $this->oDatabase->createConnection($aDbConfig);

                        break;
                }
            }
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the current 
         * system error
         * 
         * @package Setters
         * @param string $sError is the error text
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setError($sError) {
        	
        	// Set the system error
        	$this->sError = (string) $sError;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the layout to use
         *
         * @package Setters
         * @param string $sLayout is the layout to load
         * @return Framework $this for a fluid and chain-loadable interface
         */
        public function setLayout($sLayout = 'layout.php') {
        	
        	// Set our layout
        	$this->sLayout = (string) $sLayout;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the page instance
         * for the current controller
         * 
         * @param Page $oPage is the instance of the Page class
         * @return Framework $this for a fluid and chain-loadable interface
         */
        public function setPage(Page $oPage) {
        	
        	// Set our page
        	$this->oPage = $oPage;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the POST array to a POST object
         * 
         * @package Setters
         * @param array $aPostData is the $_POST array
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setPostData(array $aPostData) {
        	
        	// POST object place holder
        	$oPostData = new stdClass();
        	
        	// Loop through each of the 
        	// POST keys and append them 
        	// to the POST object
        	foreach ($aPostData as $sKey => $mValue) {
        		
        		// Append the property
        		// to the object
        		$oPostData->{$sKey} = $mValue;
        	}
        	
        	// Set the POST object
        	$this->oPostData = (object) $oPostData;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets our GET paramerters
         * 
         * @package Setters
         * @param array $aQueryParams is the GET array
         * @return Framework $this for a fluid and chain-loadable interface
         */
        public function setQueryParams(array $aQueryParams) {
        	
        	// GET object placeholder
        	$oQueryParams = new stdClass();
        	
        	// Loop through each of the
        	// GET keys and append them
        	// to the GET object
        	foreach ($aQueryParams as $sKey => $mValue) {
        		
        		// Append the property
        		// to the object
        		$oQueryParams->{$sKey} = $mValue;
        	}
        	
        	// Set the GET object
        	$this->oQueryParams = (object) $oQueryParams;
        	
        	// Return instance
        	return $this;
        }

        /**
         * This method sends a redirect to the
         * browser with the provided url
         *
         * @param string $sUri is the url to navigate to
         * @return void
         */
        public function setRedirect($sUri) {

            // Print out the script
            Html::getInstance()->generateScript('text/javascript', null, "self.location = '{$sUri}'")->getHtml(true);
        }

        /**
         * This method sets a router for nested instances of Framework
         *
         * @param string $sRouter is the prefix to append
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setRouter($sRouter) {

            // Set our router
            $this->sRouter = (string) $sRouter;

            // Return instance
            return $this;
        }
        
        /**
         * This method sets the SERVER array to a SERVER object
         * 
         * @package Setters
         * @param array $aServerData is the $_SERVER array
        **/
        public function setServerData(array $aServerData) {
        	
        	// SERVER object placeholder
        	$oServerData = new stdClass();
        	
        	// Loop through each of the 
        	// SERVER keys and append them
        	// to the SERVER object
        	foreach ($aServerData as $sKey => $mValue) {
        		
        		// Make all properties 
        		// lower case
        		$sKey = strtolower($sKey);
        		
        		// Append the property 
        		// to the object
        		$oServerData->{$sKey} = $mValue;
        	}
        	
        	// Set the SERVER object
        	$this->oServerData = (object) $oServerData;
        	
        	// Return instance
        	return $this;
        }

        /**
         * This method sets a session variable
         * section into the app
         *
         * @param string $sName is the name of the session key
         * @param array|object|string|integer|bool $mData is the data of the the session key
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setSession($sName, $mData) {

            // Set the session
            $_SESSION[$this->loadConfigVar('systemSettings', 'nameSpace')][$sName] = $mData;

            // Return instance
            return $this;
        }
        
        /**
         * This method sets the view the caller
         * wishes to render.  This is automatically
         * set when the @method executeRoute() is 
         * called, however the caller may manually
         * specify a view to render
         * 
         * @package Setters
         * @param string $sView is the view we need to render
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setView($sView) {
        	
        	// Set the view we 
        	// wish to render
        	$this->sView = (string) $sView;
        	
        	// Return instance
        	return $this;
        }
        
        /**
         * This method sets the variable scope
         * for the current view file
         * 
         * @package Setters
         * @param object $oScope
         * @return Framework $this for a fluid and chain-loadable interface
        **/
        public function setViewScope($oScope) {
        	
        	// Set our view scoper
        	$this->oView = $oScope;
        	
        	// Return the instance
        	return $this;
        } 
        
        ////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method returns our instance of Cache
         *
         * @return Cache @property oCache is our instance of Cache
         */
        public function getCache() {

            // return our instance of Cache
            return $this->oCache;
        }

        /**
         * This method retrieves the current
         * working configuration
         * 
         * @package Getters
         * @return array @property aConfig is the configuration
        **/
        public function getConfig() {
        	
        	// Return our configuration
        	return $this->aConfig;
        }
        
        /**
         * This method retrieves the path to the
         * current working configuration file
         * 
         * @package Getters
         * @return string @property sConfigFile is the path to the config file
        **/
        public function getConfigFile() {
        	
        	// Return our configuration file
        	return $this->sConfigFile;
        }
        
        /**
         * This method retrieves the current controller
         * 
         * @package Getters
         * @return string @property sController is the template method to load
        **/
        public function getController() {
        	
        	// Return our controller
        	return $this->sController;
        }
        
        /**
         * This method retrieves our current 
         * database object
         * 
         * @package Getters
         * @return Framework @property oDatabase is the current DBO
        **/
        public function getDatabase() {
        	
        	// Return our database object
        	return $this->oDatabase;
        }
        
        /**
         * This method retrieves the current 
         * system error
         * 
         * @package Getters
         * @return string @property sError is the current error text
        **/
        public function getError() {
        	
        	// Return the system error
        	return $this->sError;
        }
        
        /**
         * This method returns the current
         * layout to make the system pretty
         * 
         * @package Getters
         * @return string @property sLayout
         */
        public function getLayout() {
        	
        	// Return the current layout
        	return $this->sLayout;
        }
        
        /**
         * This method retrieves the current page instance
         * 
         * @return Page @property oPage is the current instance of page
         */
        public function getPage() {
        	
        	// Return our page instance
        	return $this->oPage;
        }
        
        /**
         * This method retrieves our current
         * POST data object
         * 
         * @package Getters
         * @return Framework @property oPostData is the current POST data
        **/
        public function getPostData() {
        	
        	// Return POST
        	return $this->oPostData;
        }
        
        /**
         * This method gets the current URL 
         * query params in the system
         * 
         * @package Getters
         * @return Framework @param oQueryParam is the $_GET object
        **/
        public function getQueryParams() {
        	
        	// Return our Query object
        	return $this->oQueryParams;
        }

        /**
         * This method returns the router
         *
         * @return string @property sRouter is the current router
        **/
        public function getRouter() {

            // Return our router
            return $this->sRouter;
        }
        
        /**
         * This method retrieves ther $_SERVER object
         * 
         * @package Getters
         * @return Framework @property oServerData is the $_SERVER array objectified
        **/
        public function getServerData() {
        	
        	// Return our server object
        	return $this->oServerData;
        }

        /**
         * This method returns a session variable
         *
         * @param string $sName is the session key
         * @return mixed|bool if the session key exists it's returned, if not false is returned
        **/
        public function getSession($sName) {

            // Check for a session
            if (empty($_SESSION[$this->loadConfigVar('systemSettings', 'nameSpace')][$sName])) {

                // Return false because
                // the session does not
                // exist
                return false;
            } else {

                // Return the session
                return $_SESSION[$this->loadConfigVar('systemSettings', 'nameSpace')][$sName];
            }
        }
        
        public function getUrl($aRoute = array()) {
        	
        	// Check to see if the route
        	// is empty
        	if (empty($aRoute)) {
        		
        		// Simply return the 
        		// current url
        		return $_SERVER['REQUEST_URI'];
        		
        	// We need to build a url
        	} else {

                // Start the url
                $sUrl = (string) '/';
        		
        		// Make sure there is a controller
        		if (isset($aRoute['sController']) && !is_null($aRoute['sController'])) {

                    // Check to see if there
                    // is a router
                    if (!is_null($this->getRouter()) || isset($aRoute['sRouter'])) {

                        // Add the router
                        $sUrl .= (string) (empty($aRoute['sRouter']) ? null : "{$aRoute['sRouter']}/");

                        // Remove the router from the array
                        if (isset($aRoute['sRouter'])) {
                            // Unset router
                            unset($aRoute['sRouter']);
                        }
                    }

        			// There is a controller 
        			// start building the url
        			$sUrl .= (string) "{$aRoute['sController']}";
        			
        			// Unset the controller
        			unset($aRoute['sController']);
        			
        			// See if we have variables
        			if (count($aRoute)) {
        				
        				// Loop through the variables
        				foreach ($aRoute as $sName => $sValue) {
        					
        					// Append to the url
        					$sUrl .= (string) "/{$sName}/{$sValue}";
        				}
        			}
        			
        			// Return the URL
        			return $sUrl;
        			
        		// No controller is set
        		} else {
        		
        			// No controller
        			return null;
        		}
        	}
        }
        
        /**
         * This method retrieves the current 
         * view that we wish or need to render
         * 
         * @package Getters
         * @return string @property sView is the current view to render
        **/
        public function getView() {
        	
        	// Return the current view
        	return $this->sView;
        }
        
        /**
         * This method retrieves the current 
         * working view scope
         * 
         * @package Getters
         * @return Framework @property oView
        **/
        public function getViewScope() {
        	
        	// Return our scope
        	return $this->oView;
        }

	}
