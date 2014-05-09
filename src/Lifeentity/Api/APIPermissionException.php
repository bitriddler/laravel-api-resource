<?php namespace Lifeentity\Api;

class APIPermissionException extends \Exception {

    public function __construct($message = "") {

        parent::__construct($message, 403);
    }

} 