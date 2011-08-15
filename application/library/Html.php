<?php 
	
	/**
	 * This class generates HTML elements and tags
	 * 
	 * @author Travis Brown <tmbrown6@gmail.com>
	 * @package Framework
	**/
	class Html extends Framework {
		
		////////////////////////////////////////////////////////////////////////
        //////////      The Properties    /////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
		protected static $oInstance = null;
		protected $sHtml            = null;
		
		////////////////////////////////////////////////////////////////////////
        //////////      The Singleton Experience    ///////////////////////////
        //////////////////////////////////////////////////////////////////////
        
		/**
         * This sets the singleton pattern instance
         *
         * @return Html
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
         * @return Html
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
        //////////      Public    /////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
		
		/**
		 * This method generates an HTML anchor tag
		 * 
		 * @param string $sHref is the hyperlink for the anchor
		 * @param string $sValue is the value of the anchor
		 * @param string $sId is the client side id of the anchor tag
		 * @param string $sName is the name of the anchor tag
		 * @param array $aAttributes is an array of non-standard attributes to append to the anchor tag
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateAnchor($sHref, $sValue, $sId = null, $sName = null, $aAttributes = array()) {
			
			// Start the element
			$sHtml = (string) "<a href=\"{$sHref}\" name=\"{$sName}\" id=\"{$sId}\" ";
			
			// Check for attributes
			if (!empty($aAttributes)) {
				
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sAttributeValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sAttributeValue}\" ";
				}
			}
			
			// Finish off the tag
			$sHtml .= (string) ">{$sValue}</a>";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}

        /**
         * This method generates an HTML button
         *
         * @param string $sName is the name of the element
         * @param sting $sId is the client side identifier of the element
         * @param string $sLabel is the value of the button
         * @param array $aAttributes is an array of extra attributes
         * @return Html $this for a fluid and chain-loadable interface
        **/
        public function generateButton($sName, $sId, $sLabel, $aAttributes = null) {

            // Start the element
            $sHtml = (string) "<button name=\"{$sName}\" id=\"{$sId}\" ";

            // Check for attributes
            if (!empty($aAttributes)) {

                // Loop through each of the attributes
                foreach ($aAttributes as $sName => $sValue) {

                    // Append the attribute
                    $sHtml .= (string) "{$sName}=\"{$sValue}\" ";
                }
            }

            // Finish off the tag
            $sHtml .= (string) ">{$sLabel}</button>";

            // Set the HTML to the system
            $this->setHtml($sHtml);

            // Return instance
            return $this;
        }
		
		/**
		 * This method generates an HTML div with elements
		 * 
		 * @param array $aElements is an array of elemnents the div should contain
		 * @param array $aAttributes is an array of non-standard attributes to append to the div
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateDiv($aElements = array(), $aAttributes = array()) {

			// Start the element
			$sHtml = (string) "<div ";
			
			// Check for attributes
			if (!empty($aAttributes)) {
			
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Close the opening tag
			$sHtml .= (string) ">";
			
			// Check for elements
			if (!empty($aElements)) {
				
				// Loop through the elements
				foreach ($aElements as $sElement) {
				
					// Append the element
					$sHtml .= (string) $sElement;
				}
			}
			
			// Finish the tag
			$sHtml .= (string) "</div>\n";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}

        /**
         * This method generates an HTML
         * drop down select form element
         *
         * @param string $sName is the name of the element
         * @param string $sId is the client side identifier of the element
         * @param array $aDataProvider is an array of options
         * @param array $aAttributes is an array of extra attributes
         * @return Html $this for a fluid and chain-loadable interface
        **/
        public function generateDropdown($sName, $sId, $aDataProvider = array(), $aAttributes = array(), $aSelected = array()) {
            // Start the HTML
            $sHtml = (string) "<select name=\"{$sName}\" id=\"{$sId}\" ";

            // Check for extra attributes
            if (!empty($aAttributes)) {

                // Loop through the attributes
                // and append them to the string
                foreach ($aAttributes as $sKey => $sVal) {

                    // Append the attribute
                    $sHtml .= (string) "{$sKey}=\"{$sVal}\" ";
                }
            }

            // Close the opening tag
            $sHtml .= (string) ">";

            // Check to see if the Data Provider
            // is an array of objects or an
            // array of arrays
            if (!empty($aDataProvider)) {

                // Parse the data provider
                foreach ($aDataProvider as $sLabel => $sValue) {
                    // Option placeholder
                    $sOption = (string) "<option value=\"{$sValue}\" ";
	                // Check for default selected values
	                if (!empty($aSelected) && is_array($aSelected)) {
		                // Loop through the selected array
		                foreach ($aSelected as $sSelectedValue) {
			                // Match the selected value
			                if ($sSelectedValue == $sValue) {
				                // Make this option selected
				                $sOption .= (string) "selected=\"selected\" ";
			                }
		                }
	                } elseif(!empty($aSelected) && !is_array($aSelected)) {
		                // We just have one selected option
		                if ($aSelected == $sValue) {
			                // Make this option selected
			                $sOption .= (string) "selected=\"selected\" ";
		                }
	                }
                    // Finish the option tag
                    $sOption .= (string) ">{$sLabel}</option>";
                    // Append the option to the select
                    $sHtml .= (string) $sOption;
                }
            }

            // Finish the dropdown
            $sHtml .= (string) "</select>";

            // Set the HTML
            $this->setHtml($sHtml);

            // Return instance
            return $this;
        }
		
		/**
		 * This method generates an HTML fieldset with elements
		 * 
		 * @param array $aElements is an array of elemnents the fieldset should contain
		 * @param array $aAttributes is an array of non-standard attributes to append to the fieldset
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateFieldset($aElements = array(), $aAttributes = array()) {
			
			// Start the element
			$sHtml = (string) "<fieldset ";
			
			// Check for attributes
			if (!empty($aAttributes)) {
			
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Close the opening tag
			$sHtml .= (string) ">";
			
			// Check for elements
			if (!empty($aElements)) {
				
				// Loop through the elements
				foreach ($aElements as $sElement) {
				
					// Append the element
					$sHtml .= (string) $sElement;
				}
			}
			
			// Finish the tag
			$sHtml .= (string) "</fieldset>\n";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method generates an HTML form with elements
		 * 
		 * @param string $sAction is the form's action
		 * @param string $sMethod is the form's request method
		 * @param string $sName is the form's name
		 * @param string $sId is the client side id of the form
		 * @param array $aElements is an array of elemnents the form should contain
		 * @param array $aAttributes is an array of non-standard attributes to append to the form
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateForm($sAction, $sMethod, $sName, $sId, $aElements = array(), $aAttributes = array()) {
			
			// Start the element
			$sHtml = (string) "<form action=\"{$sAction}\" method=\"{$sMethod}\" name=\"{$sName}\" id=\"{$sId}\" ";
			
			// Check for attributes
			if (!empty($aAttributes)) {
			
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Close the opening tag
			$sHtml .= (string) ">";
			
			// Check for elements
			if (!empty($aElements)) {
				
				// Loop through the elements
				foreach ($aElements as $sElement) {
				
					// Append the element
					$sHtml .= (string) $sElement;
				}
			}
			
			// Finish the tag
			$sHtml .= (string) "</form>\n";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method generates and img tag
		 * 
		 * @param string $sSource is the image to load
		 * @param array $aAttributes is an array of non-standard attributes to append to the image tag
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateImage($sSource, $aAttributes = array()) {
			
			
			// Start the element
			$sHtml = (string) "<img src=\"{$sSource}\" ";
			
			// Check for attributes
			if (!empty($aAttributes)) {
				
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Finish off the tag
			$sHtml .= (string) ">";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method generates an HTML input
		 * 
		 * @param string $sType is the type of input we want to generate
		 * @param string $sName is the name of the input tag
		 * @param string $sId is the client side id of the input tag
		 * @param array $aAttributes is an array of non-standard attributes to append to the input tag
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateInput($sType, $sName, $sId, $aAttributes = array()) {
			
			// Allowed types
			$aAllowedTypes = array('checkbox', 'button', 'file', 'hidden', 'image', 'password', 'radio', 'submit', 'text');
			
			// Check to make sure the caller wants
			// to generate a valid HTML input
			if (in_array($sType, $aAllowedTypes)) {
			
				// Start the element
				$sHtml = (string) "<input type=\"{$sType}\" name=\"{$sName}\" id=\"{$sId}\" ";
				
				// Check for extra attributes
				if (!empty($aAttributes)) {
					
					// Loop through each of the attributes
					foreach ($aAttributes as $sName => $sValue) {
						
						
						// Append the attribute
						$sHtml .= (string) "{$sName}=\"{$sValue}\" ";	
					}
				}
				
				// Close the tag
				$sHtml .= (string) ">";
				
				// Set the HTML to the system
				$this->setHtml($sHtml);
				
				// Return instance
				return $this;
			
			// Throw an exception	
			} else {
				
				// Throw
				throw new Exception("'{$sType}' is not a valid HTML input type.");
			}
		}
		
		/**
		 * This method generates a link tag which are
		 * primarily used in the head of the page for
		 * loading in cascading stylesheets
		 * 
		 * @param string $sRelative is the what the link is relative to
		 * @param string $sType is the type of content the tag is loading
		 * @param string $sHref is the source of the link
		 * @param array $aAttributes are extra attributes to append to the tag
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function generateLink($sRelative, $sType, $sHref, array $aAttributes = array()) {
			
			// Start the element
			$sHtml = (string) "<link rel=\"{$sRelative}\" type=\"{$sType}\" href=\"{$sHref}\" ";
			
			// Check for extra attributes
			if (!empty($aAttributes)) {
				
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Close the tag
			$sHtml .= (string) ">\n";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method generates an HTML meta tag
		 * 
		 * @param string $sName is the name attribute of the meta tag
		 * @param string $sContent is the content attribute of the meta tag
		 * @param array $aAttributes are extra attributes to append to the tag
		 * @return object $this for a fluid and chain-loadable interface
		 */
		public function generateMetaTag($sName, $sContent, array $aAttributes = array()) {
			
			// Start the element
			$sHtml = (string) "<meta name=\"{$sName}\" content=\"{$sContent}\" ";
			
			// Check for extra attributes
			if (!empty($aAttributes)) {
				
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Close the tag
			$sHtml .= (string) ">\n";
			
			// Set the HTML to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}
		
		/**
		 * This method generates an HTML script tag
		 * 
		 * @param string $sType is the type attribute of the script tag
		 * @param string $sSrc is the src attribute of the script tag
		 * @param string $sInline is the inline script inside the tags
		 * @param array $aAttributes are any other attributes to be appended
		 * @return object $this for a fluid and chain-loadable interface
		 */
		public function generateScript($sType, $sSrc = null, $sInline = null, array $aAttributes = array()) {
			
			// Start the element
			$sHtml = (string) "<script type=\"{$sType}\" ";

			// Check to see if we have a source
			if (!is_null($sSrc) && ($sSrc !== '')) {
				$sHtml .= (string) "src=\"{$sSrc}\" ";
			}
			
			// Check for other attributes
			if (!empty($aAttributes)) {
				
				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {
					
					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}
			
			// Close the opening tag
			$sHtml .= (string) ">";
			
			// Check for inline code
			if (null !== $sInline) {
				
				// Append the inline code
				$sHtml .= (string) $sInline;
			}
			
			// Finish the element
			$sHtml .= (string) "</script>\n";
			
			// Set the Html to the system
			$this->setHtml($sHtml);
			
			// Return instance
			return $this;
		}

        /**
         * This method generates an HTML textarea
         *
         * @param string $sName is the name of the textarea
         * @param string $sId is the unique client side identifier
         * @param string $sValue is the value to append to the textarea
         * @param array $aAttributes are any other attributes to be appended
         * @return Html $this for a fluid and chain-loadable interface
        **/
        public function generateTextarea($sName, $sId, $sContent = null, $aAttributes = array()) {

            // Start the element
            $sHtml = (string) "<textarea name=\"{$sName}\" id=\"{$sId}\" ";

            // Check for extra attributes
            if (!empty($aAttributes)) {

                // Loop through each of the attributes
                foreach ($aAttributes as $sName => $sValue) {


                    // Append the attribute
                    $sHtml .= (string) "{$sName}=\"{$sValue}\" ";
                }
            }

            // Close the tag
            $sHtml .= (string) ">";

            // Check for a value
            if (!is_null($sContent)) {

                // Add the value then
                $sHtml .= (string) $sContent;
            }

            // Close the tag
            $sHtml .= (string) "</textarea>";

            // Set the HTML to the system
            $this->setHtml($sHtml);

            // Return instance
            return $this;
        }

        public function generateTag($sType, $bSelfClosing, $aElements = array(), $aAttributes = array()) {

            // Start the element
			$sHtml = (string) "<{$sType} ";

			// Check for attributes
			if (!empty($aAttributes)) {

				// Loop through each of the attributes
				foreach ($aAttributes as $sName => $sValue) {

					// Append the attribute
					$sHtml .= (string) "{$sName}=\"{$sValue}\" ";
				}
			}

			// Close the opening tag
			$sHtml .= (string) ">";

			// Check for elements
			if (!empty($aElements)) {

				// Loop through the elements
				foreach ($aElements as $sElement) {

					// Append the element
					$sHtml .= (string) $sElement;
				}
			}
            
            // Check to see if the tag closes itself
            if ($bSelfClosing == true) {

                // Finish the tag
                $sHtml .= (string) " />";
            } else {

                // Finish the tag
                $sHtml .= (string) "</{$sType}\n";
            }

			// Set the HTML to the system
			$this->setHtml($sHtml);

			// Return instance
			return $this;
        }
		
		////////////////////////////////////////////////////////////////////////
        //////////      Setters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
		/**
		 * This method sets the generated HTML
		 * string into the system for later use
		 * 
		 * @param string $sHtml is the generated html
		 * @return object $this for a fluid and chain-loadable interface
		**/
		public function setHtml($sHtml) {
			
			// Set the html
			$this->sHtml = (string) str_replace(' >', '>', $sHtml);
			
			// Return instance
			return $this;
		}
		
		////////////////////////////////////////////////////////////////////////
        //////////      Getters    ////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////
        
		/**
		 * This method retrieves the current 
		 * generated HTML string
		 * 
		 * @param boolean $bToScreen determines whether to print the HTML directly to the screen or not
		 * @return string @property sHtml is the generated HTML string
		**/
		public function getHtml($bToScreen = false) {
			
			// See if the caller wishes to print the HTML directly
			if ($bToScreen === true) {
			
				// Return the current html
				return print($this->sHtml);
			} else {
				
				// Return the current html
				return $this->sHtml;
			}
		}
	}