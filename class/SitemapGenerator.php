<?php
namespace TM;

class SitemapGenerator
{
    private $sitemap;
    private $urlset = [];

    public function __construct()
    {
        $this->sitemap = new \DOMDocument('1.0', 'UTF-8');
        $this->sitemap->preserveWhiteSpace = false;
        $this->sitemap->formatOutput = true;

        $this->urlset = $this->sitemap->appendChild( $this->sitemap->createElement("urlset") );
        $this->urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    public function add($params)
    {
        $url = $this->urlset->appendChild( $this->sitemap->createElement('url') );
        foreach($params as $key => $value){
            if(strlen($value)){
                $url->appendChild( $this->sitemap->createElement($key, htmlspecialchars($value)) );
            }
        }
    }

    public function generate($file=null)
    {
        if( is_null($file) ) {
            header("Content-Type: text/xml; charset=utf-8");
            echo $this->sitemap->saveXML();
        } else {
            $this->sitemap->save( $file );
        }
    }
}
