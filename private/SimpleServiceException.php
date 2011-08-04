<?php
class SimpleServiceException extends Exception {
	function __construct($message, $code) {
		parent::__construct($message, $code);
	}
}