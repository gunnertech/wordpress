<?php
/*
  * @copyright 2010 Ceasar Feijen www.cfconsultancy.nl
  * @Youtubelist generator
  * @This is not free software
  */
// Live enviroment:
//error_reporting(0);
//ini_set('display_errors', 0);
//error_reporting(E_ALL);

class youtubelist
{
	protected $type = 'keywords';
	protected $cachexml = false;
	protected $cachelife = 86400; //24*60*60;

	protected $urldata = array('q' => 'HD nature', // videocode
								'safeSearch' => 'strict',
								'max-results' => 50,
								'orderby' => 'relevance', // sorteren
								'format' => 5,
								'prettyprint' => 'true',
								'webm' => 0,
								'time' => 'all_time',
								'v' => 2,
								'start-index' => 1	);
	protected $user;
	protected $playlist;
	protected $author;
	protected $favorites;
	protected $restriction;
	protected $lang;
	protected $caption;
	protected $xmlpath = './cache/';
	protected $curlinit;
	protected $descriptionlength = 300;
	protected $titlelength = 75;

	public function __construct($type)
	{
		$this->curlinit = function_exists('curl_init');
		$this->type = $type;
	}

	protected function truncate($string, $length = '', $replacement = ' ..', $start = 75) //alternative substr
	{
		if (strlen($string) <= $start)
			return $string;
		if ($length)
		{
			return substr_replace($string, $replacement, $start, $length);
		}
		else
		{
			return substr_replace($string, $replacement, $start);
		}
	}

	protected function q($q) // Make correct string
	{
		$q = strval($q); // We need typeof string
        $q = htmlspecialchars($q, ENT_QUOTES);
		$q = preg_replace('/[[:space:]]/', ' ', trim($q));
		//$q = urlencode($q);
		return $q;
	}

    protected function mbencoding($string)
    {
        if (function_exists('mb_convert_encoding'))
        {
            return mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8');
        }
        else
        {
            return htmlentities(utf8_encode($string));
        }
	}

	public function set_titlelength($titlelength) // Set title lenght
	{
		$this->titlelength = $titlelength;
	}

	public function set_descriptionlength($descriptionlength) // Set title lenght
	{
		$this->descriptionlength = $descriptionlength;
	}

	public function set_keywords($keywords) // Set keywords to search
	{
		$this->urldata['q'] = $this->q($keywords);
	}

	public function set_username($username) // Set username to search
	{
		$this->user = $this->q($username);
	}

	public function set_favorites($username) // Set username to search
	{
		$this->favorites = $this->q($username);
	}

	public function set_playlist($playlist) // Set playlist to search
	{
		$this->playlist = $this->q($playlist);
	}

	public function set_safeSearch($safeSearch) // Set safesearch possible: none, moderate, strict; default: strict
	{
		$mogelijk = array('none', 'moderate', 'strict');
		if(!in_array($safeSearch, $mogelijk))
		{
			throw new InvalidArgumentException('safesearch isn\'t of these: none, moderate, strict');
		}
		else
		{
			$this->urldata['safeSearch'] = $safeSearch;
		}
	}

	public function set_mobile($mobile) // Set search only mobile html5 supported
	{
		$this->urldata['webm'] = intval($mobile); // No check
	}

	public function set_time($time) // Valid values for this parameter are today (1 day), this_week (7 days), this_month (1 month) and all_time. The default value for this parameter is all_time.
	{
		$this->urldata['time'] = $time; // No check
	}

	public function set_max($max) // Set max results; default: 50
	{
		$this->urldata['max-results'] = intval($max); // We need typeof int
	}

	public function set_start($start) // Set start-index; default: 1
	{
		$this->urldata['start-index'] = intval($start); // We need typeof int
	}

	public function set_order($order) // set sorting order: relevance, published, viewCount, rating; default: relevance
	{
		$mogelijk = array('relevance', 'published', 'viewCount', 'rating');
		if(!in_array($order, $mogelijk))
		{
			throw new InvalidArgumentException('order isn\'t of these: relevance (default), published, viewCount, rating');
		}
		else
		{
			$this->urldata['orderby'] = $order;
		}
	}

	public function set_author($author) //Search only video's uploaded by a particular YouTube user - add this by $feedURL
	{
		$this->author = $author; // No check
	}

	public function set_restriction($restriction) // Set country restriction
	{
		$this->restriction = $restriction; // No check
	}

	public function set_lang($lang) // Set lang; default 'en', codes can be found here http://www.loc.gov/standards/iso639-2/php/code_list.php row ISO 639-1 Code
	{
		$this->lang = $lang; // No check
	}

	public function set_caption($caption) // Set captions true or false within quotes
	{
		$this->caption = $caption; // No check
	}

	public function set_cachexml ($cache) // Bool, 1 use cache, 0 don't use cache
	{
		if($cache === false || $cache === true)
		{
			$this->cachexml = $cache;
		}
		else
		{
			throw new InvalidArgumentException('set_cachexml can only be boolean');
		}
	}

	public function set_cachelife ($cachelife) // Lifetime of cache NOTE: USE SECONDS!
	{
		$this->cachelife = $cachelife; // No check
	}

	public function set_xmlpath ($path) // Set where to store xml files
	{
		$this->xmlpath = $path;
	}

	protected function build_url($type, $urldata) // Generate url
	{
		if(!is_null($this->author))
		{
			$urldata['author'] = $this->author;
		}
		if(!is_null($this->restriction))
		{
			$urldata['restriction'] = $this->restriction;
		}
		if(!is_null($this->lang))
		{
			$urldata['lr'] = $this->lang;
		}
		if(!is_null($this->caption))
		{
			$urldata['caption'] = $this->caption;
		}
		switch ($type)
		{
			case 'keywords':
                //echo http_build_query($urldata, '', '&');
				return 'http://gdata.youtube.com/feeds/api/videos?' . http_build_query($urldata, '', '&');
				break;
			case 'username':
				return 'http://gdata.youtube.com/feeds/api/users/' . $this->user . '/uploads?' . http_build_query($urldata, '', '&');
				break;
			case 'favorites':
                //echo http_build_query($urldata, '', '&');
				return 'http://gdata.youtube.com/feeds/api/users/' . $this->favorites . '/favorites?' . http_build_query($urldata, '', '&');
				break;
			case 'playlist':
				return 'http://gdata.youtube.com/feeds/api/playlists/' . $this->playlist . '?' . http_build_query($urldata, '', '&');
				break;
			default:
				throw new InvalidArgumentException('Build_url need right type');
		}
	}

	public function get_videos() // Use this function. You have to use the right type
	{
		$xmldoc = new DOMDocument();
		switch ($this->type)
		{
			case 'keywords':
				$url = $this->build_url('keywords', $this->urldata);
				break;
			case 'username':
				$temparray = $this->urldata;
				unset($temparray['q'], $temparray['safeSearch'], $temparray['format'], $temparray['lang']);
				$url = $this->build_url('username', $temparray);
				break;
			case 'favorites':
				$temparray = $this->urldata;
				unset($temparray['q'], $temparray['safeSearch'], $temparray['format'], $temparray['lang']);
				$url = $this->build_url('favorites', $temparray);
				break;
			case 'playlist':
				$temparray = $this->urldata;
				unset($temparray['q'], $temparray['safeSearch'], $temparray['lang'], $temparray['orderby']);
				$url = $this->build_url('playlist', $temparray);
				break;
			default:
				throw new InvalidArgumentException('get_videos need right type');
		}

		$file = realpath($this->xmlpath) . DIRECTORY_SEPARATOR . md5($url) . '.xml';

		if($this->cachexml && $this->cache_file($file))
		{
			if(!$xmldoc->load($file))
			{
				throw new Exception('Error loading');
			}
		}
		else
		{
			if($this->curlinit)
			{
				if(!$xmldoc->loadXML($this->curl_request($url)))
				{
					throw new Exception('Error loading');
				}
			}
			else
			{
				if(!$xmldoc->load($url))
				{
					throw new Exception('Error loading' . $url);
				}
			}

			if($this->cachexml)
			{
			@file_put_contents($file, $xmldoc->saveXML()); // Suppres error. User can't help this error... Should log
			}
		}
		$xpath = new DOMXPath($xmldoc);
		$xpath->registerNamespace('feaed', 'http://www.w3.org/2005/Atom');
		$query = '//feaed:feed/feaed:entry';
		$data = $xpath->query($query);
		$videodata = array();
		foreach($data as $dat)
		{
			$temparray = array();
			$query = 'media:group/media:description';
			$temparray['description'] = $this->mbencoding(ucfirst(strtolower($this->truncate($xpath->query($query, $dat)->item(0)->nodeValue,'',' ..',$this->descriptionlength))));

			$query = 'media:group/media:title';
			$temparray['title'] = $this->mbencoding($this->truncate($xpath->query($query, $dat)->item(0)->nodeValue,'',' ..',$this->titlelength));

			$query = 'media:group/yt:videoid';
			$temparray['videoid'] = $xpath->query($query, $dat)->item(0)->nodeValue;

			//$query = 'yt:statistics/@viewCount';
			//$temparray['vieuws'] = $xpath->query($query, $dat)->item(0)->nodeValue;

			//$query = 'media:group/media:thumbnail';
			//$temparray['thumbnail'] = $xpath->query($query, $dat)->item(0)->getAttribute('url');

			$query = 'media:group/yt:duration/@seconds';
			$temparray['time'] = $this->time($xpath->query($query, $dat)->item(0)->nodeValue);
			$videodata[] = $temparray;
		}
		return $videodata;
	}

	protected function curl_request($url) // Make a cURL request
	{
		$chf = curl_init();
		$timeout = 15; // set to zero for no timeout
		curl_setopt ($chf, CURLOPT_URL, $url);
		curl_setopt ($chf, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($chf, CURLOPT_CONNECTTIMEOUT, $timeout);
		$feedcontents = curl_exec($chf);
		curl_close($chf);
		return $feedcontents;
	}

	protected function time($length) // Translate time
	{
		$time = floor($length/60) . ":" . $length % 60;
        if (floor($length/60) < 10) {
           $time = "0" . floor($length/60) . ":" . $length % 60;
        }
		return $time;
	}

	protected function cache_file($file) // check for cache life time
	{
		return file_exists($file) && filemtime($file) > time() - $this->cachelife;
	}
}