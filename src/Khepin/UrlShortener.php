<?php

namespace Khepin;

/**
 * A class to store shortened urls and retrieve them based on a given key.
 *
 * @author sebastien.armand
 */
class UrlShortener {

    /**
     * A list of urls with the url shortened name as a key
     * @var array
     */
    private $url_list = array();
    /**
     * A file name in 'ini' format from which the urls are retrieved and where they are then stored
     * @var string
     */
    private $url_file = '';
    /**
     * A regex pattern to validate urls
     */
    const url_regex = '^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%\$#_]*)?^';

    /**
     * Constructor
     * @param string $url_file_name
     */
    public function __construct($url_file_name) {
        $this->url_file = $url_file_name;
        $url_file = \parse_ini_file($url_file_name);
        foreach ($url_file as $slug => $url) {
            $this->url_list[$slug] = $url;
        }
    }

    /**
     * Retrieves a URL from a given url_slug / or url short name
     *
     * @param string $url_slug
     * @return string url
     */
    public function get($url_slug) {
        return $this->url_list[$url_slug];
    }

    /**
     * Validates that a url can be added with this short name and adds it to the
     * backend
     *
     * @param string $url_slug
     * @param string $url
     */
    public function add($url_slug, $url) {
        if (!\preg_match(self::url_regex, $url)) {
            throw new \Exception('Invalid url');
        }
        if (isset($this->url_list[$url_slug])) {
            throw new \Exception('Url short name already exists');
        }
        $this->url_list[$url_slug] = $url;
        $this->dump();
    }

    /**
     * Saves the current urls to the backend 'ini' file
     */
    private function dump() {
        $fh = fopen($this->url_file, 'w');
        foreach ($this->url_list as $url_slug => $url) {
            fwrite($fh, $url_slug . ' = ' . $url . "\n");
        }
        fclose($fh);
    }

    /**
     * Returns the complete url list for this shortener.
     *
     * @return array
     */
    public function getAll(){
        return $this->url_list;
    }
}
