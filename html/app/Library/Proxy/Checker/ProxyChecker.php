<?php

namespace App\Library\Proxy\Checker;


class ProxyChecker
{

    /**
     * @var string
     */
    protected $url = "http://whatismyipaddress.com/";

    /**
     * @var array
     */
    protected $results;

    /**
     * Check proxy
     * @param string $ip
     * @param string $port
     * @param string $proxy_type
     * @param string|int $timeout
     * @return array[]|void
     */
    public function check(string $ip, string $port = '', string $proxy_type = '', string $timeout = '')
    {
        list($proxy_type, $timeout, $socksOnly) = $this->list_parameters($proxy_type, $timeout);

        return $this->check_single_proxy($ip, $port, $proxy_type, $timeout, $socksOnly);
    }

    /**
     * Check proxy from json
     * @param array $proxies
     * @param string $proxy_type
     * @param string|int $timeout
     * @return array[]|void
     */
    public function check_array(array $proxies, string $proxy_type = '', string $timeout = '')
    {
        list($proxy_type, $timeout, $socksOnly) = $this->list_parameters($proxy_type, $timeout);

        return $this->check_array_proxy($proxies, $proxy_type, $timeout);
    }

    /**
     * Check proxy from json
     * @param string $json
     * @param string $proxy_type
     * @param string|int $timeout
     * @return mixed
     */
    public function check_json(string $json, string $proxy_type = '', string $timeout = '')
    {
        list($proxy_type, $timeout, $socksOnly) = $this->list_parameters($proxy_type, $timeout);

        //Convert the file to a list of proxies
        $array = json_decode($json, true);

        return $this->check_multy_proxy($array, $proxy_type, $timeout);
    }

    /**
     * Check proxy list from file [$file_path]
     *  format IP:PORT
     * @param string $file_path
     * @param string $proxy_type
     * @param string|int $timeout
     * @return array[]|false|string|void
     */
    public function check_file(string $file_path, string $proxy_type = '', string $timeout = '')
    {
        //If we can't find the file, complain
        if (!file_exists($file_path)) {
            die("Could not find file '" . $file_path . "'");
        }

        list($proxy_type, $timeout, $socksOnly) = $this->list_parameters($proxy_type, $timeout);

        //Convert the file to a list of proxies
        $array = file($file_path);

        return $this->check_line_proxy($array, $proxy_type, $timeout);
    }

    /**
     * @param string|int $timeout
     * @param string $proxy_type
     * @return void
     */
    protected function list_parameters(string $proxy_type, string $timeout)
    {
        /**
         * Step 2 - Little extras go here
         * Default is 'false' not to confuse the IF-logic later on
         */
        $socksOnly = false;
        if (isset($proxy_type)) {
            if ($proxy_type == "socks") {
                $socksOnly = true;
                $proxy_type = "socks";
            } else {
                $proxy_type = "http(s)";
            }
        } else {
            $proxy_type = "http(s)";
        }

        return array($proxy_type, $timeout, $socksOnly);
    }

    /**
     * Check multy proxy
     * @param array $proxies
     * @param string $proxy_type
     * @param string|int $timeout
     * @param bool $socksOnly
     * @return array[]|false|string
     */
    public function check_line_proxy(array $proxies, string $proxy_type = "http(s)", string $timeout = '', bool $socksOnly = false)
    {
        $this->results = [];
        foreach($proxies as $proxy) {
            $parts = explode(':', trim($proxy));
            $url = strtok($this->cur_page_url(),'?');
            $data[] = $url . '?ip=' . $parts[0] . "&port=" . $parts[1] . "&timeout=" . $timeout . "&proxy_type=" . $proxy_type;
        }
        $this->results = $this->multi_request($data);

        return $this;
    }

    /**
     * Check array multi proxies
     * @param array $proxies
     * @param string $proxy_type
     * @param string|int $timeout
     * @param bool $socksOnly
     * @return array[]|false|string
     */
    public function check_array_proxy(array $proxies, string $proxy_type = "http(s)", string $timeout = '', bool $socksOnly = false)
    {
        $this->results = [];
        foreach($proxies as $proxy) {
            if (isset($proxy['proxy']) && isset($proxy['proxy']['ip']) && isset($proxy['proxy']['port'])) {
                $this->results[] = $this->check_single_proxy($proxy['proxy']['ip'], $proxy['proxy']['port'], $timeout, $proxy_type);
            }
        }

        return $this;
    }

    /**
     * Check proxy
     * @param string $ip
     * @param string|int $port
     * @param string $timeout
     * @param string $proxy_type
     * @param bool $socksOnly
     * @return array[]|false|string|void
     */
    public function check_single_proxy(string $ip, $port, string $timeout = '', string $proxy_type = "http(s)", bool $socksOnly = false)
    {
        $passByIPPort= $ip . ":" . $port;
    
        // You can use virtually any website here, but in case you need to implement other proxy settings (show annonimity level)
        // I'll leave you with whatismyipaddress.com, because it shows a lot of info.
        $url = $this->url;
    
        // Get current time to check proxy speed later on
        $loadingtime = microtime(true);
    
        $theHeader = curl_init($url);
        curl_setopt($theHeader, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($theHeader, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($theHeader, CURLOPT_PROXY, $passByIPPort);
    
        //If only socks proxy checking is enabled, use this below.
        if ($socksOnly) {
            curl_setopt($theHeader, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
    
        //This is not another workaround, it's just to make sure that if the IP uses some god-forgotten CA we can still work with it ;)
        //Plus no security is needed, all we are doing is just 'connecting' to check whether it exists!
        curl_setopt($theHeader, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($theHeader, CURLOPT_SSL_VERIFYPEER, 0);
    
        //Execute the request
        $curlResponse = curl_exec($theHeader);
    
        if ($curlResponse === false) {
            //If we get a 'connection reset' there's a good chance it's a SOCKS proxy
            //Just as a safety net though, I'm still aborting if $socksOnly is true (i.e. we were initially checking for a socks-specific proxy)
            if (curl_errno($theHeader) == 56 && !$socksOnly) {
                $this->check_single_proxy($ip, $port, $timeout, true, "socks");
                return;
            }
            $arr = array(
                "success" => false,
                "error" => curl_error($theHeader),
                "proxy" => array(
                    "ip" => $ip,
                    "port" => $port,
                    "type" => $proxy_type
                )
            );
        } else {
            $arr = array(
                "success" => true,
                "proxy" => array(
                    "ip" => $ip,
                    "port" => $port,
                    "speed" => floor((microtime(true) - $loadingtime)*1000),
                    "type" => $proxy_type
                )
            );
        }
        return $arr;
    }

    /**
     * @param array $data
     * @param array $options
     * @return array
     */
    public function multi_request(array $data, $options = array())
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();
    
        // multi handle
        $mh = curl_multi_init();
    
        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {
            $curly[$id] = curl_init();
    
            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL,            $url);
            curl_setopt($curly[$id], CURLOPT_HEADER,         0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
    
            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST,       1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }
    
        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while($running > 0);

        // get content and remove handles
        foreach($curly as $id => $c) {
            $result[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }
    
        // all done
        curl_multi_close($mh);
    
        return $result;
    }

    /**
     * @return string
     */
    public function cur_page_url() 
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" .
                $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * Return results as array
     * @return array
     */
    public function to_array()
    {
        return $this->results;
    }

    /**
     * @return string
     */
    public function to_json()
    {
        return json_encode($this->results, true);
    }

    public function to_ip()
    {
        $res = [];
        foreach ($this->results as $proxy) {
            $res[] = $proxy['proxy']['ip'] . ':' . $proxy['proxy']['port'];
        }
        return $res;
    }

    public function success_only()
    {
        $res = [];
        foreach ($this->results as $proxy) {
            $res[] = $proxy['proxy']['ip'] . ':' . $proxy['proxy']['port'];
        }
        return $res;
    }
}
