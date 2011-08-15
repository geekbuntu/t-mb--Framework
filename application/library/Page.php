<?php 
	
	class Page {
		
		////////////////////////////////////////////////////////////////////////
        //////////      The Properties    /////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        protected $sBodyClass   = null;
		protected $aJavaScripts = null;
		protected $aMetaTags    = null;
		protected $aStyleSheets = null;
		protected $sTitle       = null;
		
		////////////////////////////////////////////////////////////////////////
        //////////      Constructor    ////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
		public function __construct() {
			
		}
		
		////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method sets the classes to the body tag
         *
         * @param string $sClass is the class(es) to append to the body
         * @return Page $this for a fluid and chain-loadable interface
         */
        public function setBodyClass($sClass) {

            // Set the body's class
            $this->sBodyClass = (string) $sClass;

            // Return instance
            return $this;
        }

		/**
		 * This methos sets the javascripts the caller
		 * wishes to load when the page loads
		 * 
		 * @param array $aJavaScripts is the array of scripts to load
		 * @return Page $this for a fluid and chain-loadable interface
		**/
		public function setJavaScripts(array $aJavaScripts) {
			
			// Set our javascripts
			$this->aJavaScripts = (array) $aJavaScripts;
			
			// Return instance
			return $this;
		}
		
		/**
		 * This methos sets the meta tags the caller
		 * wishes to load when the page loads
		 * 
		 * @param array $aMetaTags is the array of meta tags to load
		 * @return Page $this for a fluid and chain-loadable interface
		**/
		public function setMetaTags(array $aMetaTags) {
			
			// Set our javascripts
			$this->aMetaTags = (array) $aMetaTags;
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method sets the stylesheets the caller
		 * wishes to load when the page loads
		 * 
		 * @param array $aStyleSheets are the stylesheets
		 * @return Page $this for a fluid and chain-loadable interface
		**/
		public function setStyleSheets(array $aStyleSheets) {
			
			// Set our stylesheets
			$this->aStyleSheets = (array) $aStyleSheets;
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method sets the page title to what the caller
		 * wishes it to be
		 * 
		 * @param string $sPageTitle is the desired page title
		 * @return Page $this for a fluid and chain-loadable interface
		**/
		public function setTitle($sPageTitle) {
			
			// Set our page's title
			$this->sTitle = (string) '.:'.Framework::getInstance()->loadConfigVar('systemSettings', 'nameSpace').' - '.$sPageTitle.':.';
			
			// Return Instance
			return $this;
		}
		
		////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////

        /**
         * This method retreives the class(es) to append to the body tag
         *
         * @return string $sBodyClass is the class(es) to append to the body tag
        **/
        public function getBodyClass() {

            // Return the body class(es)
            return $this->sBodyClass;
        }

		/**
		 * This method generates the html for
		 * each of the javascript files to load
		 * 
		 * @return string $sHtml is the string of script tags
		**/
		public function getJavaScripts() {
			
			// Check to see if we need to load 
			// any javascripts
			if (is_null($this->aJavaScripts)) {
				
				// Return null
				return null;
				
			// Load stylesheets
			} else {
				// Html placeholder
				$sHtml = (string) '';
				
				// Loop through each of the scripts
				foreach ($this->aJavaScripts as $sScript) {
					
					// Generate the html and append 
					// it to the return string
					$sHtml .= (string) Html::getInstance()->generateScript('text/javascript', $sScript)->getHtml();
				}
				
				// Return the html string
				return $sHtml;
			}
		}
		
		/**
		 * This method generates the html for
		 * each of the meta tags to load
		 * 
		 * @return string $sHtml is the string of meta tags
		**/
		public function getMetaTags() {
			
			// Check to see if we need to load 
			// any meta tags
			if (is_null($this->aMetaTags)) {
				
				// Return null
				return null;
				
			// Load stylesheets
			} else {
				// Html placeholder
				$sHtml = (string) '';
				
				// Loop through each of the meta tags
				foreach ($this->aMetaTags as $sName => $sContent) {
					
					// Generate the html and append
					// it to the return string
					$sHtml .= (string) Html::getInstance()->generateMetaTag($sName, $sContent)->getHtml();
				}
				
				// Return the html string
				return $sHtml;
			}
		}
		
		/**
		 * This method generates the html for
		 * each of the stylesheets to load
		 * 
		 * @return string $sHtml is the string of stylesheets
		**/
		public function getStyleSheets() {
			
			// Check to see if we need to load 
			// any stylesheets
			if (is_null($this->aStyleSheets)) {
				
				// Return null
				return null;
				
			// Load stylesheets
			} else {
				
				// Html placeholder
				$sHtml = (string) '';
				
				// Loop through each of the stylesheets
				foreach ($this->aStyleSheets as $sStyleSheet) {
					
					// Generate the html and append
					// it to the return string
					$sHtml .= (string) Html::getInstance()->generateLink('stylesheet', 'text/css', $sStyleSheet)->getHtml();
				}
				
				// Return the html string
				return $sHtml;
			}
		}
		
		/**
		 * This method returns the current page title
		 * 
		 * @return string @property sTitle is the current page title
		**/
		public function getTitle() {
			
			// Return the page title
			return $this->sTitle;
		}
	}