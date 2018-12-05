<?php

namespace TurboApi;

/**
 * Класс для работы с API турбостраниц Яндекса
 * Class TurboApi
 */
class TurboApi
{

    const DEFAULT_API_VERSION = 'v3.2';
    const DEFAULT_API_BASE_URL = 'https://api.webmaster.yandex.net';
    const MODE_DEBUG = 'DEBUG';
    const MODE_PRODUCTION = 'PRODUCTION';

    private $hostAddress;
    private $apiVersion;
    private $apiBaseUrl;
    private $userId;
    private $hostId;
    private $isDebug;
    private $mode;
    private $token;
    private $authHeader;
    private $curlLink;
    private $uploadAddress;
    private $loadStatus;

    /**
     * @return mixed
     */
    public function getHostAddress()
    {
        return $this->hostAddress;
    }

    /**
     * @param mixed $hostAddress
     * @return TurboApi
     */
    public function setHostAddress($hostAddress)
    {
        $this->hostAddress = $hostAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param mixed $apiVersion
     * @return TurboApi
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param mixed $apiBaseUrl
     * @return TurboApi
     */
    public function setApiBaseUrl($apiBaseUrl)
    {
        $this->apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     * @return TurboApi
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLoadStatus()
    {
        return $this->loadStatus;
    }

    /**
     * @return mixed
     */
    public function getCurlLink()
    {
        return $this->curlLink;
    }

    /**
     * @param mixed $curlLink
     * @return TurboApi
     */
    public function setCurlLink($curlLink)
    {
        $this->curlLink = $curlLink;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUploadAddress()
    {
        return $this->uploadAddress;
    }

    /**
     * @param mixed $uploadAddress
     * @return TurboApi
     */
    public function setUploadAddress($uploadAddress)
    {
        $this->uploadAddress = $uploadAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return TurboApi
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHostId()
    {
        return $this->hostId;
    }

    /**
     * @param mixed $hostId
     * @return TurboApi
     */
    public function setHostId($hostId)
    {
        $this->hostId = $hostId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getisDebug()
    {
        return $this->isDebug;
    }

    /**
     * @param mixed $isDebug
     * @return TurboApi
     */
    public function setIsDebug($isDebug)
    {
        $this->isDebug = $isDebug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return TurboApi
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthHeader()
    {
        return $this->authHeader;
    }

    /**
     * @param string $authHeader
     * @return TurboApi
     */
    public function setAuthHeader($authHeader)
    {
        $this->authHeader = $authHeader;
        return $this;
    }

    /**
     * Возвращает адрес API
     * @return string
     */
    public function getApiURL()
    {
        return $this->getApiBaseUrl() . '/' . $this->getApiVersion();
    }

    /**
     * TurboApi constructor.
     * @param string $hostAddress
     * @param string $token
     * @param string $mode
     * @param string $apiBaseUrl
     * @param string $apiVersion
     */
    public function __construct($hostAddress, $token, $mode = self::MODE_DEBUG, $apiBaseUrl = self::DEFAULT_API_BASE_URL, $apiVersion = self::DEFAULT_API_VERSION)
    {
        $this->setToken($token)
            ->setMode($mode)
            ->setApiBaseUrl($apiBaseUrl)
            ->setApiVersion($apiVersion)
            ->setHostAddress($hostAddress)
            ->setAuthHeader('Authorization: OAuth ' . $this->getToken());
    }

    /**
     * Отправка запроса в API
     * @param string $method
     * @param string $route
     * @param mixed $data
     * @param array $headers
     * @return array
     */
    private function sendRequest($method, $route, $headers = [], $data = null)
    {
        $url = $this->getApiURL() . $route;
        if($this->getMode()) {
            $url .= '?mode=' . $this->getMode();
        }

        $ch = curl_init();
        $this->curlLink = $ch;
        curl_setopt($this->curlLink, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlLink, CURLOPT_HEADER, false);
        curl_setopt($this->curlLink, CURLOPT_CONNECTTIMEOUT, 2);
        $requestHeaders = array_merge([$this->authHeader], $headers);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($this->curlLink, CURLOPT_URL, $url);

        if ($method === 'POST') {
            curl_setopt($this->curlLink, CURLOPT_POST, 1);
            curl_setopt($this->curlLink, CURLOPT_POSTFIELDS, $data);
        }
        $jsonResponse = curl_exec($this->curlLink);
        $curlInfo = curl_getinfo($this->curlLink);
        curl_close($this->curlLink);

        return ['curlInfo' => $curlInfo, 'response' => $jsonResponse];
    }

    /**
     * Получение ID пользователя в вебмастере
     * @return mixed
     */
    public function requestUserId()
    {
        $responseRaw = $this->sendRequest('GET', '/user/');
        $apiResponse = $responseRaw['response'];
        $userId = json_decode($apiResponse, true)['user_id'];
        $this->setUserId($userId);

        return $userId;
    }

    /**
     * Получение id хоста в вебмастере
     * @return string|null
     */
    public function requestHost()
    {
        if (!isset($this->userId)) {
            return null;
        }

        /*
         * Запросом получаем список хостов, к которому пользователь имеет доступ в Яндекс.Вебмастере
         */
        $responseRaw = $this->sendRequest('GET', '/user/' . $this->getUserId() . '/hosts/');
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);

        // Выбираем нужный хост
        foreach ($apiResponseArray['hosts'] as $host) {
            if (strcmp($host['ascii_host_url'], $this->getHostAddress()) === 0) {
                $this->setHostId($host['host_id']);
                return $host['host_id'];
            }
        }
        return null;
    }

    /**
     * Получение адреса для загрузки RSS
     * @return string
     */
    public function requestUploadAddress()
    {
        if (!isset($this->userId) || !isset($this->hostId)) {
            return null;
        }

        $responseRaw = $this->sendRequest('GET', '/user/' . $this->getUserId() . '/hosts/' . $this->getHostId() . '/turbo/uploadAddress/');
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);
        $this->uploadAddress = $apiResponseArray['upload_address'];

        return $this->uploadAddress;
    }

    /**
     * Отправка RSS в турбо страницы
     * @param mixed $data
     * @return string ID задачи
     * @throws Exception
     */
    public function uploadRss($data)
    {
        if (!isset($this->uploadAddress)) {
            throw new Exception('Не задан адрес для отправки данных!');
        }

        $uploadRoute = explode($this->getApiVersion(), $this->getUploadAddress())[1];

        $responseRaw = $this->sendRequest('POST', $uploadRoute, ['Content-type: application/rss+xml'], $data);
        $apiResponse = $responseRaw['response'];
        $responseStatus = $responseRaw['curlInfo']['http_code'];

        print_r($responseStatus);
        print_r($apiResponse);

        if ((int)$responseStatus == 202) {
            return json_decode($apiResponse, true)['task_id'] . PHP_EOL;
        }
    }

    /**
     * Запрос информации об обработке задачи
     * @param $taskId
     * @return string Статус обработки
     */
    public function getTask($taskId)
    {
        if (!isset($this->userId) || !isset($this->hostId)) {
            return null;
        }

        echo '/user/' . $this->userId . '/hosts/' . $this->hostId . '/turbo/tasks/' . $taskId . PHP_EOL;
        $responseRaw = $this->sendRequest('GET', '/user/' . $this->userId . '/hosts/' . $this->hostId . '/turbo/tasks/' . $taskId);
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);
        $this->loadStatus = $apiResponseArray['load_status'];

        print_r($responseRaw);

        return $this->loadStatus;
    }
}
