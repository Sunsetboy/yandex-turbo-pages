<?php
declare(strict_types=1);

namespace TurboApi\Exceptions;

class UploadRssException extends \Exception
{
    private ?string $errorCode = null;
    private ?int $httpCode = null;

    /**
     * @return int|null
     */
    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    /**
     * @param int|null $httpCode
     * @return UploadRssException
     */
    public function setHttpCode(?int $httpCode): UploadRssException
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * @param string|null $errorCode
     * @return UploadRssException
     */
    public function setErrorCode(?string $errorCode): UploadRssException
    {
        $this->errorCode = $errorCode;
        return $this;
    }
}
