<?php

abstract class Form {

    ////////////////////////////////////////////////////////////////////////
    //////////  Properties  ///////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    protected static $oInstance;
    protected $aElements;
    protected $aForm;
    protected $sHtml;

    ////////////////////////////////////////////////////////////////////////
    //////////      Singleton Experience    ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This method sets the
     * singleton instance
     *
     * @static $oInstance
     * @abstract Form
     * @param $oInstance is the instance to set
     * @return Form self
    **/
    public static abstract function setInstance($oInstance);

    /**
     * This method gets the
     * singleton instance
     *
     * @static $oInstance
     * @abstract Form
     * @return Form self
    **/
    public static abstract function getInstance();

    ////////////////////////////////////////////////////////////////////////
    //////////  Public  ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This method puts the form
     * together from the separate pieces
     *
     * @param array $aForm are the form settings
     * @return Form $this
    **/
    public function buildForm(array $aForm = null) {

    }

    /**
     * This method adds an element
     * to the form prior to build
     *
     * @param array $aElement
     * @return Form $this
    **/
    public function addElement(array $aElement) {

    }

    ////////////////////////////////////////////////////////////////////////
    //////////  Setters ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * This method appends an
     * element to the elements
     * array
     *
     * @param array $aElement
     * @return Form $this
    **/
    public function setElement(array $aElement) {

        // Append the element
        $this->aElements[] = $aElement;

        // Return instance
        return $this;
    }

    public function setForm(array $aForm){

    }
    public function setHtml($sHtml) {
        
    }

    ////////////////////////////////////////////////////////////////////////
    //////////  Getters ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    public abstract function getElement();
    public abstract function getElements();
    public abstract function getForm();
    public abstract function getHtml($bPrint = false);

}