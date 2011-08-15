<style type="text/css">

    .<?php echo($this->loadConfigVar('systemSettings', 'logicalNameSpace')) ?>-info, .success, .warning, .error, .validation {
        border: 1px solid;
        margin: 10px 0px;
        padding:15px 10px 15px 50px;
        background-repeat: no-repeat;
        background-position: 10px center;
    }

    .<?php echo($this->loadConfigVar('systemSettings', 'logicalNameSpace')) ?>-error {
        color: #d8000c;
        background-color: #FFBABA;
        background-image: url('/static/images/icons/error.png');
    }
</style>
<div id="home-content">
    <div id="full-width-content">
        <div class="pad">
            <div class="one-col">
                <h2><?php echo($this->loadConfigVar('siteFeatures', 'errorHeader')) ?></h2>
                <div class="<?php echo($this->loadConfigVar('systemSettings', 'logicalNameSpace')) ?>-info <?php echo($this->loadConfigVar('systemSettings', 'logicalNameSpace')) ?>-error">
					<?php echo($this->getError()) ?>
				</div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>