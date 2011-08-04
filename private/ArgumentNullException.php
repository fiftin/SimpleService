<?php
    require_once "ArgumentException.php";
    class ArgumentNullException extends ArgumentException {
        function __construct($message = null, $argumentName = null) {
            parent::__construct($message, $argumentName);
        }
    }
