<?php

// Set response headers
// switch ($this->sDataType) {

    // JSON
    // case 'json' : header('Content-type: application/json'); break;

    // XML
    // case 'xml'  : header('Content-type: text/xml');         break;

    // Anything else
    // default     : header('Content-type: text/plain');       break;
// }

// Check for output
if (empty($this->sRpcResponse)) {

    // Show the JSON
    $this->sRpcResponse = Rpc_Server::getInstance()->buildResponse(
        array(
            'bSuccess' => false,
            'sError'   => $this->loadConfigVar(
                'errorMessages',
                'emptyRpcResponse'
            )
        )
    );
}

// We have a response
// set, show the JSON
echo($this->sRpcResponse);