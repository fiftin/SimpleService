<?php

class NullReferenceException extends Exception {
    function __construct($message = null) {
        parent::__construct($message);
    }
}
