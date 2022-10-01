<?php

namespace TurboApi;

use TurboApi\Exceptions\TurboApiConnectionException;
use TurboApi\Exceptions\TurboApiException;
use TurboApi\Exceptions\UploadRssException;

/**
 * Класс для работы с API турбостраниц Яндекса
 * Class TurboApi
 */
class TurboApi
{

    const DEFAULT_API_VERSION = 'v4';
    const DEFAULT_API_BASE_URL = 'https://api.webmaster.yandex.net';
    const MODE_DEBUG = 'DEBUG';
    const MODE_PRODUCTION = 'PRODUCTION';

    const TASK_TYPE_FILTER_DEBUG = 'DEBUG';
    const TASK_TYPE_FILTER_PRODUCTION = 'PRODUCTION';
    const TASK_TYPE_FILTER_ALL = 'ALL';

    const LOAD_STATUS_FILTER_PROCESSING = 'PROCESSING';
    const LOAD_STATUS_FILTER_OK = 'OK';
    const LOAD_STATUS_FILTER_WARNING = 'WARNING';
    const LOAD_STATUS_FILTER_ERROR = 'ERROR';

    private string $hostAddress;
    private string $apiVersion;
    private string $apiBaseUrl;
    private int $userId;
    private string $hostId;
    private bool $isDebug;
    private string $mode;
    private string $token;
    private string $authHeader;
    private $curlLink;
    private string $uploadAddress;
    private string $loadStatus;

    /**
     * @return string
     */
    public function getHostAddress(): string
    {
        return $this->hostAddress;
    }

    /**
     * @param string $hostAddress
     * @return TurboApi
     */
    public function setHostAddress(string $hostAddress): self
    {
        $this->hostAddress = $hostAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    /**
     * @param string $apiVersion
     * @return TurboApi
     */
    public function setApiVersion(string $apiVersion): self
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param string $apiBaseUrl
     * @return TurboApi
     */
    public function setApiBaseUrl(string $apiBaseUrl): self
    {
        $this->apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return TurboApi
     */
    public function setMode(string $mode): self
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
    public function setCurlLink($curlLink): self
    {
        $this->curlLink = $curlLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadAddress(): string
    {
        return $this->uploadAddress;
    }

    /**
     * @param string $uploadAddress
     * @return TurboApi
     */
    public function setUploadAddress(string $uploadAddress): self
    {
        $this->uploadAddress = $uploadAddress;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return TurboApi
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostId(): string
    {
        return $this->hostId;
    }

    /**
     * @param string $hostId
     * @return TurboApi
     */
    public function setHostId(string $hostId): self
    {
        $this->hostId = $hostId;
        return $this;
    }

    /**
     * @return bool
     */
    public function getisDebug(): bool
    {
        return $this->isDebug;
    }

    /**
     * @param bool $isDebug
     * @return TurboApi
     */
    public function setIsDebug(bool $isDebug): self
    {
        $this->isDebug = $isDebug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return TurboApi
     */
    public function setToken($token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthHeader(): string
    {
        return $this->authHeader;
    }

    /**
     * @param string $authHeader
     * @return TurboApi
     */
    public function setAuthHeader($authHeader): self
    {
        $this->authHeader = $authHeader;
        return $this;
    }

    /**
     * Возвращает адрес API
     * @return string
     */
    public function getApiURL(): string
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
    public function __construct(
        string $hostAddress,
        string $token,
        string $mode = self::MODE_DEBUG,
        string $apiBaseUrl = self::DEFAULT_API_BASE_URL,
        string $apiVersion = self::DEFAULT_API_VERSION
    ) {
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
     * @param array $getParams
     * @return array
     */
    private function sendRequest($method, $route, $headers = [], $data = null, $getParams = [])
    {
        $url = $this->getApiURL() . $route;
        if ($this->getMode()) {
            $getParams['mode'] = $this->getMode();
        }
        if (!empty($getParams)) {
            $url .= '?' . http_build_query($getParams);
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
        if (!isset(json_decode($apiResponse, true)['user_id'])) {
            throw new TurboApiConnectionException('Не удалось получить ID пользователя');
        }
        $userId = json_decode($apiResponse, true)['user_id'];
        $this->setUserId($userId);

        return $userId;
    }

    /**
     * Получение id хоста в вебмастере
     * @return string|null
     * @throws TurboApiException|TurboApiConnectionException
     */
    public function requestHost(): ?string
    {
        if (!isset($this->userId)) {
            throw new TurboApiException('Не задан ID пользователя');
        }

        /*
         * Запросом получаем список хостов, к которому пользователь имеет доступ в Яндекс.Вебмастере
         */
        $responseRaw = $this->sendRequest('GET', '/user/' . $this->getUserId() . '/hosts/');
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);

        if (!isset($apiResponseArray['hosts'])) {
            throw new TurboApiConnectionException('Не удалось получить список хостов пользователя');
        }

        // Выбираем нужный хост
        foreach ($apiResponseArray['hosts'] as $host) {
            if (strcmp($host['ascii_host_url'], $this->getHostAddress()) === 0) {
                $this->setHostId($host['host_id']);
                return $host['host_id'];
            }
        }

        throw new TurboApiException('Хост не найден в хостах пользователя');
    }

    /**
     * Получение адреса для загрузки RSS
     * @return string
     * @throws TurboApiConnectionException
     * @throws TurboApiException
     */
    public function requestUploadAddress(): string
    {
        if (!isset($this->userId) || !isset($this->hostId)) {
            throw new TurboApiException('Хост или id пользователя не задан');
        }

        $responseRaw = $this->sendRequest('GET', '/user/' . $this->getUserId() . '/hosts/' . $this->getHostId() . '/turbo/uploadAddress/');
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);
        if (!isset($apiResponseArray['upload_address'])) {
            throw new TurboApiConnectionException('Не удалось получить адрес загрузки RSS');
        }
        $this->uploadAddress = $apiResponseArray['upload_address'];

        return $this->uploadAddress;
    }

    /**
     * Отправка RSS в турбо страницы
     * @param mixed $data
     * @return string ID задачи
     * @throws TurboApiException
     * @throws UploadRssException
     */
    public function uploadRss($data): string
    {
        if (!isset($this->uploadAddress)) {
            throw new TurboApiException('Не задан адрес для отправки данных!');
        }

        $uploadRoute = explode($this->getApiVersion(), $this->getUploadAddress())[1];

        $responseRaw = $this->sendRequest('POST', $uploadRoute, ['Content-type: application/rss+xml'], $data);
        $apiResponse = $responseRaw['response'];
        $responseStatus = $responseRaw['curlInfo']['http_code'];

        if ((int)$responseStatus == 202) {
            return json_decode($apiResponse, true)['task_id'] . PHP_EOL;
        } else {
            $uploadRssException = new UploadRssException(
                json_decode($apiResponse, true)['error_message'] ??
                'Unknown exception'
            );
            $uploadRssException->setHttpCode((int)$responseStatus);
            $uploadRssException->setErrorCode(
                json_decode($apiResponse, true)['error_code'] ??
                'Unknown error code'
            );
            throw $uploadRssException;
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
            throw new TurboApiException('Хост или id пользователя не задан');
        }

        $responseRaw = $this->sendRequest('GET', '/user/' . $this->userId . '/hosts/' . $this->hostId . '/turbo/tasks/' . $taskId);
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);
        $this->loadStatus = $apiResponseArray['load_status'];

        return $this->loadStatus;
    }

    /**
     * Запрос списка задач
     * @param int $offset Смещение в списке. Минимальное значение — 0
     * @param int $limit Ограничение на количество элементов в списке. Минимальное значение — 1; максимальное значение — 100.
     * @param string $taskTypeFilter Фильтрация по режиму загрузки RSS-канала. Возможные значения: DEBUG, PRODUCTION, ALL.
     * @param string $loadStatusFilter Фильтрация по статусу загрузки RSS-канала. Возможные значения: PROCESSING, OK, WARNING, ERROR.
     * @return array Ответ от api
     * @link https://yandex.ru/dev/turbo/doc/api/ref/list-tasks-docpage/
     */
    public function getTasks($offset = 0, $limit = 10, $taskTypeFilter = null, $loadStatusFilter = null)
    {
        if (!isset($this->userId) || !isset($this->hostId)) {
            throw new TurboApiException('Хост или id пользователя не задан');
        }

        $getParams = [
            'offset' => $offset,
            'limit' => $limit
        ];
        if (isset($taskTypeFilter)) {
            $getParams['task_type_filter'] = $taskTypeFilter;
        }
        if (isset($loadStatusFilter)) {
            $getParams['load_status_filter'] = $loadStatusFilter;
        }

        $responseRaw = $this->sendRequest('GET', '/user/' . $this->userId . '/hosts/' . $this->hostId . '/turbo/tasks/', [], null, $getParams);
        $apiResponse = $responseRaw['response'];
        $apiResponseArray = json_decode($apiResponse, true);

        return $apiResponseArray;
    }
}
