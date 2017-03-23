<?php namespace Braseidon\Scraper;

use Braseidon\Scraper\Exceptions\ScraperException;
use Braseidon\Scraper\RollingCurl\RollingCurl;
use Braseidon\Scraper\Request;

class Scraper extends RollingCurl
{
    /**
     * Shows if console mode is activated
     *
     * @var boolean
     */
    protected static $console_mode = false;

    /**
     * Debug info
     *
     * @var array
     */
    public static $debug_info = [];

    /**
     * Debug log
     *
     * @var boolean
     */
    public static $debug_log = false;

    /**
     * Default options for every Curl request
     *
     * @var array
     */
    public $options = [
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_HEADER         => 0,
    ];

    /**
     * Array of keywords to search for
     *
     * @var array
     */
    public $keywords = [];

    /**
     * Starting connections function execution overload
     *
     * @throws ScraperException
     * @param int $window_size Max number of simultaneous connections
     * @return string|bool
     */
    public function execute($window_size = null)
    {
        # checking $window_size var
        if ($window_size == null) {
            self::addDebugMessage(" (!) Default threads amount value (5) is used");
        } elseif ($window_size > 0 && is_int($window_size)) {
            self::addDebugMessage(" # Threads set to:\t$window_size");
        } else {
            throw new ScraperException(" (!) Wrong threads amount in execute():\t$window_size");
        }

        # writing debug
        self::addDebugMessage(" # Curl C. Timeout ->\t".$this->options[CURLOPT_CONNECTTIMEOUT]." seconds");
        self::addDebugMessage(" # Curl Timeout ->\t".$this->options[CURLOPT_TIMEOUT]." seconds");
        self::addDebugMessage(" * Starting connections...");
        //var_dump($this->__get('requests'));

        $time_start = microtime(1);
        $result = parent::execute($window_size);
        $time_end = microtime(1);
        $time_taken = round($time_end-$time_start, 2);

        self::addDebugMessage(" * Finished in ".$time_taken." seconds");

        return $result;
    }

    /**
     * Create new Request, set a HTTP proxy, and add it to the Request queue
     *
     * @param string       $url
     * @param string       $proxy    Format: ip:port:user:pass (user/pass optional)
     * @param string       $method
     * @param array|string $postData
     * @param array        $headers
     * @param array        $options
     * @return RollingCurl
     */
    public function proxyRequest($url, $proxy, $method = 'GET', $postData = null, $headers = null, $options = null)
    {
        $request = new Request($url, $method);

        if ($postData) {
            $newRequest->setPostData($postData);
        }

        if ($headers) {
            $newRequest->setHeaders($headers);
        }

        if ($options) {
            $newRequest->setOptions($options);
        }

        if ($proxy) {
            $request->addOptions($this->setProxy($proxy));
        }

        $request->addOptions($this->newUserAgent());

        return $this->add($request);
    }

    /**
     * Function to generate a random user agent
     *
     * @param  array  $options The array of options to modify for a Request
     * @return string
     */
    public function newUserAgent(array $options = [])
    {
        $agents = [];
        $netclr = [];
        $sysntv = [];

        $ras1 = mt_rand(0, 9);
        $ras2 = mt_rand(0, 255);

        $date = date("YmdHis").$ras2;

        $netclr[0] = ".NET CLR 2.0.50727";
        $netclr[1] = ".NET CLR 1.1.4322";
        $netclr[2] = ".NET CLR 4.0.30319";
        $netclr[3] = ".NET CLR 3.5.2644";
        $netclr[4] = ".NET CLR 1.0.10322";
        $netclr[5] = ".NET CLR 3.5.11952";
        $netclr[6] = ".NET CLR 4.0.30319";
        $netclr[7] = ".NET CLR 2.0.65263";
        $netclr[8] = ".NET CLR 1.1.4322; .NET CLR 4.0.30319";
        $netclr[9] = ".NET CLR 4.0.30319; .NET CLR 2.0.50727";

        $sysntv[0] = "Windows NT 6.1; WOW64";
        $sysntv[1] = "Windows NT 5.1; rv:10.1";
        $sysntv[2] = "Windows NT 5.1; U; en";
        $sysntv[3] = "compatible; MSIE 10.0; Windows NT 6.2";
        $sysntv[4] = "Windows NT 6.1; U; en; OneNote.2; ";
        $sysntv[5] = "compatible; Windows NT 6.2; WOW64; en-US";
        $sysntv[6] = "compatible; MSIE 10.0; Windows NT 6.2; Trident/5.0; WOW64";
        $sysntv[7] = "Windows NT 5.1; en; FDM";
        $sysntv[8] = "Windows NT 6.2; WOW64; MediaBox 1.1";
        $sysntv[9] = "compatible; MSIE 11.0; Windows NT 6.2; WOW64";

        // Random user agents that are highly randomized
        $agents[0] = "Opera/9.80 (".$sysntv[$ras1].";".$netclr[$ras1].") Presto/2.10.".mt_rand(0, 999)." Version/11.62";
        $agents[1] = "Mozilla/5.0 (".$sysntv[$ras1].";".$netclr[$ras1].") Gecko/".$date." Firefox/23.0.".$ras1;
        $agents[2] = "Mozilla/5.0 (".$sysntv[$ras1].";".$netclr[$ras1].") AppleWebKit/535.2 (KHTML, like Gecko) Chrome/20.0.".mt_rand(0, 9999).".".mt_rand(0, 99)." Safari/535.2";
        $agents[3] = "Mozilla/5.0 (".$sysntv[$ras1].";".$netclr[$ras1].")";
        $agents[4] = "Mozilla/5.0 (".$sysntv[$ras1].") AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.".mt_rand(0, 9999).".".mt_rand(0, 99)." Safari/537.36)";
        $agents[5] = "Mozilla/5.0 (".$sysntv[$ras1].";".$netclr[$ras1].")";
        $agents[6] = "Opera/9.80 (".$sysntv[$ras1].") Presto/2.9.".$ras2." Version/12.50";
        $agents[7] = "Mozilla/5.0 (".$sysntv[$ras1].";".$netclr[$ras1].")";
        $agents[8] = "Mozilla/5.0 (".$sysntv[$ras1].") Gecko/".$date." Firefox/17.0";
        $agents[9] = "Mozilla/5.0 (".$sysntv[$ras1].";".$netclr[$ras1].")";

        // return the random user agent string
        $options[CURLOPT_USERAGENT] = $agents[$ras1];

        return $options;
    }

    /**
     * Returns this Object's keywords
     *
     * @return array $keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set the keywords array and flatten it
     *
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        if (! is_array($keywords)) {
            $keywords = explode(',', $keywords);
        }

        // Flatten the array into single column, then trim & lowercase the array
        $keywords = array_flatten($keywords);
        $keywords = array_map('trim', $keywords);
        $keywords = array_map('strtolower', $keywords);

        $this->keywords = $keywords;

        return $keywords;
    }

    /*
    |--------------------------------------------------------------------------
    | Proxies
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * Set a Request's proxy using format ip:port:user:pass
     *
     * @param  string  $proxy
     * @param  array   $options
     * @return array
     */
    public function setProxy($proxy, array $options = [])
    {
        // Sleep for the Proxy cooldown time if there's no Proxies
        if (empty($proxy)) {
            throw new ScraperException('A valid proxy must be included. Format: ip:port:user:pass');
        }

        $proxy = $this->parseProxy($proxy);

        // Set the Curl object's options for ip/port, then apply user and pass if not null
        $options[CURLOPT_PROXY]     = $proxy->ip;
        $options[CURLOPT_PROXYPORT] = $proxy->port;

        if (! is_null($proxy->user) && ! is_null($proxy->pass)) {
            $options[CURLOPT_PROXYUSERPWD] = $proxy->user.':'.$proxy->pass;
        }

        self::addDebugMessage(" + Set proxy ->\t {$proxy->ip}:{$proxy->port}:{$proxy->user}:{$proxy->pass}");

        return $options;
    }

    /**
     * Explode the proxy by colons and return object
     *
     * @param  string $proxy
     * @return stdClass
     */
    private function parseProxy($proxy)
    {
        $proxy = explode(':', trim($proxy));

        $proxy = [
            'ip'   => $proxy[0],
            'port' => $proxy[1],
            'user' => (! isset($proxy[2]) ? null : $proxy[2]),
            'pass' => (! isset($proxy[3]) ? null : $proxy[3]),
        ];

        return $this->toObject($proxy);
    }

    /**
     * Turn an array into an object
     *
     * @param  array    $array
     * @return stdClass
     */
    private function toObject(array $array)
    {
        return json_decode(json_encode($array));
    }

    /*
    |--------------------------------------------------------------------------
    | Debug Console
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * Initializing console mode
     *
     * @return void
     */
    public function initConsole()
    {
        self::$console_mode = true;

        echo "<pre>";

        # Internal Server Error fix in case no apache_setenv() function exists
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_flush();
        }
        ob_implicit_flush(1);

        # writing debug
        self::addDebugMessage("## Console mode activated");
    }

    /**
     * Logging method
     *
     * @param string $msg message
     * @return void
     */
    public static function addDebugMessage($msg)
    {
        if (self::$debug_log) {
            self::$debug_info[] = $msg;
        }

        if (self::$console_mode) {
            echo htmlspecialchars($msg)."\r\n";
        }
    }
}
