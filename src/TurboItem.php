<?php

namespace TurboApi;

/**
 * Элемент списка страниц в турбо-rss
 * Class TurboItem
 */
class TurboItem
{
    /** @var string XML описывающий одну страницу */
    private $xml;

    /**
     * @return mixed
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param mixed $xml
     * @return TurboItem
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
        return $this;
    }
}
