<?php
    class ArgumentException extends Exception {
        function __construct($message = null, $argumentName = null) {
            parent::__construct($message);
            $this->argumentName = $argumentName;
        }

        public function getArgumentName() {
            return $this->argumentName;
        }

        private $argumentName;
    }
