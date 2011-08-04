<?php

    /**
     *
     */
    class FileNotFoundException extends Exception {
        function __construct($message = null, $filename = null) {
            parent::__construct($message);
            $this->filename = $filename;
        }
        
        public function getFileName() {
            return $this->filename;
        }

        private $filename;
    }

?>
