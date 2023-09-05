<?php
class mikananime implements ISite, IRss, ISearch, IVerify, IDownload, IPostProcess {
    const SITE = "https://mikanani.me";
    private $url;

    /*
     * mikananime()
     * @param {string} $url
     * @param {string} $username
     * @param {string} $password
     * @param {string} $meta
     */
    public function __construct($url = null, $username = null, $password = null, $meta = NULL) {
        $this->url = $url;
    }

    /*
     * ReadRss()
     * @return {array} RssFeed array
     */
    public function ReadRss() {
        $url = $this->url;

        // 仅支持来自 mikanani.me 的 RSS
        if (strpos($url, mikananime::SITE) === false) {
            return array();
        }
        $rss = array();

        // 加载页面
        $file_contents = mikananime::CurlGet($url);

        // 使用 XPath 定位
        $dom = new DOMDocument();
        @$dom->loadHTML($file_contents, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        $query = $xpath->query('//item');

        // 依次提取搜索结果
        foreach ($query as $node) {
            $feed = new RssFeed();
            $feed->link = $node->getElementsByTagName("link")->item(0)->nodeValue;
            $feed->title = $node->getElementsByTagName("title")->item(0)->nodeValue;

            $rss[] = $feed;
        }

        return $rss;
    }

    static function CurlGet($url) {

        // 测试下来只有 curl 可以正常通过代理访问
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);

        // 编码转换
        $file_contents = html_entity_decode($file_contents, ENT_QUOTES, "UTF-8");

        return $file_contents;
    }

    static function Precision_Size_String_to_Bytes_Integer($size) {
        $size = trim($size);
        $unit = strtoupper(substr($size, -2));
        $size = floatval($size);
        switch ($unit) {
            case "KB":
                return intval($size * 1024);
            case "MB":
                return intval($size * 1048576);
            case "GB":
                return intval($size * 1073741824);
            case "TB":
                return intval($size * 1099511627776);
            default:
                return intval($size);
        }
    }

    static function Datetime_String_to_PHP_DateTime($datetime) {
        return new DateTime("$datetime +0800");
    }

    /*
     * Search()
     * @param {string} $keyword
     * @param {integer} $limit
     * @param {string} $category
     * @return {array} SearchLink array
     */
    public function Search($keyword, $limit, $category) {
        $url = mikananime::SITE . "/Home/Search?searchstr=$keyword";
        $found = array();

        // 加载页面
        $file_contents = mikananime::CurlGet($url);

        // 使用 XPath 定位
        $dom = new DOMDocument();
        @$dom->loadHTML($file_contents, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        $query = $xpath->query('//*[@id="sk-container"]/div[2]/table/tbody/tr');

        // 依次提取搜索结果
        foreach ($query as $node) {
            $link = new SearchLink();
            $link->src = mikananime::SITE;
            $link->link = $link->src . $node->getElementsByTagName("a")->item(0)->getAttribute("href");
            $link->name = $node->getElementsByTagName("a")->item(0)->nodeValue;

            $size = $node->getElementsByTagName("td")->item(1)->nodeValue;
            $link->size = mikananime::Precision_Size_String_to_Bytes_Integer($size);

            $datetime = $node->getElementsByTagName("td")->item(2)->nodeValue;
            $link->time = mikananime::Datetime_String_to_PHP_Datetime($datetime);

            // $link->seeds = 0;
            // $link->peers = 0;
            $link->category = "动画";
            $link->enclosure_url = $node->getElementsByTagName("a")->item(1)->getAttribute("data-clipboard-text");

            $found[] = $link;
        }

        return $found;
    }

    /*
     * Verify()
     * @return {boolean}
     */
    public function Verify() {
    }

    /*
     * GetDownloadLink()
     * @return {mixed} DownloadLink object or DownloadLink array
     */
    public function GetDownloadLink() {
    }

    /*
     * RefreshDownloadLink()
     * @param {DownloadLink} $dlink
     * @return {DownloadLink} DownloadLink object
     */
    public function RefreshDownloadLink($dlink) {
        return $dlink;
    }

    /*
     * PostProcess()
     * @param {string} $path download temp path
     * @param {array} $files temp file list
     * @param {string} $meta caller metadata
     * @return {array} file list in the $path
     */
    static public function PostProcess($path, $files, $meta) {
        return $files;
    }
}
