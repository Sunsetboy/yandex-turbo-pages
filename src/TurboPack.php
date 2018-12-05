<?php

namespace TurboApi;

/**
 * Класс для работы с набором турбо-страниц в формате XML
 * Class TurboPack
 */
class TurboPack
{
    /**
     * @var string
     */
    private $siteTitle;

    /**
     * @var string
     */
    private $siteUrl;

    /**
     * @var string
     */
    private $siteLanguage;

    /**
     * @var string
     */
    private $siteDescription;


    /** @var TurboItem[] */
    private $turboItems;

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->siteTitle;
    }

    /**
     * @param string $siteTitle
     * @return TurboPack
     */
    public function setSiteTitle($siteTitle)
    {
        $this->siteTitle = $siteTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * @param string $siteUrl
     * @return TurboPack
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteLanguage()
    {
        return $this->siteLanguage;
    }

    /**
     * @param string $siteLanguage
     * @return TurboPack
     */
    public function setSiteLanguage($siteLanguage)
    {
        $this->siteLanguage = $siteLanguage;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteDescription()
    {
        return $this->siteDescription;
    }

    /**
     * @param string $siteDescription
     * @return TurboPack
     */
    public function setSiteDescription($siteDescription)
    {
        $this->siteDescription = $siteDescription;
        return $this;
    }

    /**
     * @return TurboItem[]
     */
    public function getTurboItems()
    {
        return $this->turboItems;
    }

    /**
     * @param TurboItem[] $turboItems
     */
    public function setTurboItems($turboItems)
    {
        $this->turboItems = $turboItems;
    }

    /**
     * Добавление страницы в набор
     * @param TurboItem $item
     */
    public function addItem(TurboItem $item)
    {
        $this->turboItems[] = $item;
    }

    /**
     * TurboPack constructor.
     * @param string $siteTitle
     * @param string $siteUrl
     * @param string $siteDescription
     * @param string $siteLanguage
     */
    public function __construct($siteTitle, $siteUrl, $siteDescription, $siteLanguage)
    {
        $this->setSiteTitle($siteTitle)
            ->setSiteUrl($siteUrl)
            ->setSiteDescription($siteDescription)
            ->setSiteLanguage($siteLanguage);
    }

    /**
     * Шапка RSS
     * @return string
     */
    private function getHeader()
    {
        return '<rss xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0"><channel> 
            <title>' . $this->getSiteTitle() . '</title>
            <link>' . $this->getSiteUrl() . '</link>
            <language>' . $this->getSiteLanguage() . '</language>
            <description>' . $this->getSiteDescription() . '</description>';
    }

    /**
     * Футер RSS
     * @return string
     */
    private function getFooter()
    {
        return '</channel></rss>';
    }

    /**
     * Возвращает массив XML документов
     * @param integer $taskSize Максимальное количество элементов в задаче
     * @return array
     */
    public function getTasks($taskSize = 5)
    {
        $tasks = [];
        $counter = 0;
        $currentTaskXML = '';

        foreach ($this->turboItems as $itemNumber => $item) {
            if ($counter % $taskSize == 0) {
                $currentTaskXML = $this->getHeader();
            }
            $currentTaskXML .= $item->getXml();

            if (($counter+1) % $taskSize == 0) {
                $currentTaskXML .= $this->getFooter();
                $tasks[] = $currentTaskXML;
            }
            $counter++;
        }

        if($counter % $taskSize != 0) {
            $currentTaskXML .= $this->getFooter();
            $tasks[] = $currentTaskXML;
        }

        return $tasks;
    }
}
