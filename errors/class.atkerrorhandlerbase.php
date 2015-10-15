<?php

/**
 * The atkErrorHandlerObject handles the creation of the error handlers and
 * serves as a base class for them as well.
 *
 * @author Mark Wittens
 */
abstract class Atk_ErrorHandlerBase
{
    protected $params = array();

    /**
     * Constructor. Params are used to pass handler specific data to the handlers.
     *
     * @param array $params
     */
    public function __construct($params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }
        $this->params = $params;
    }

    /**
     * Returns an error handler by name, params are passed to the handler.
     *
     * @param string $handlerName
     * @param array $params
     * @return atkErrorHandlerObject
     */
    static public function get($handlerName, $params)
    {
        $handlerFileName = Atk_Config::getGlobal('atkroot') . 'atk/errors/class.atk' . strtolower($handlerName) . 'errorhandler.php';
        if (file_exists($handlerFileName)) {
            require_once($handlerFileName);
            $handlerClassName = 'Atk_' . ucfirst($handlerName) . 'ErrorHandler';
            return new $handlerClassName($params);
        } else {
            Atk_Tools::atkwarning("Could not find script file for error handler '$handlerName': $handlerFileName");
        }
    }

    /**
     * Implement the handle() function in a derived class to add customized error handling
     *
     * @param string $errorMessage
     * @param string $debugMessage
     */
    abstract public function handle($errorMessage, $debugMessage);
}
