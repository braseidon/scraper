<?php namespace Braseidon\Scraper;

use Exception;

class ScraperException extends Exception
{
    /**
     * Initialize
     *
     * @param string  $message
     * @param integer $code
     */
    public function __construct($message = "", $code = 0)
    { // For PHP < 5.3 compatibility omitted: , Exception $previous = null
        // Scraper::add_debug_msg($message);
        parent::__construct($message, $code);
    }
}
