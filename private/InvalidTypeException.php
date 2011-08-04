<?php
    class InvalidTypeException extends Exception {
        function __construct($message = null) {
            Exception::__construct($message);
        }
    }
?>
