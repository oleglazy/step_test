<?php


namespace App\Service;


use voku\helper\HtmlDomParser;

class ParserMusic
{
    const URL =  'https://tophit.ua/ru';

    /**
     * @return array
     */
    public function parseTopChart()
    {
        $dom = HtmlDomParser::file_get_html(self::URL);

        $elementsFirst = $dom->findMulti('.b-hot-tracks-wrap .list .hot-track-preview');
        $elementsSecond = $dom->findMulti('.b-hot-tracks-wrap .list .b-hot-track');

        $musicTop = array_merge($this->getMusicFromDom($elementsFirst), $this->getMusicFromDom($elementsSecond));

        return $musicTop;
    }

    /**
     * @param HtmlDomParser $elements
     *
     * @return array
     */
    private function getMusicFromDom($elements)
    {
        $musicTop = [];

        foreach ($elements as $element) {
            $title = $element->findOne('.title');
            $author = $element->findOne('.author');
            $musicTop[] = $title->nodeValue . ' - ' . $author->nodeValue;
        }

        return $musicTop;
    }

}