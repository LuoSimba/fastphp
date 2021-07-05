<?php
namespace http;

use Exception;


/**
 * request method: POST
 * request body: JSON format
 * response: JSON
 */
final class PostJSON extends HttpPost
{

    public function __construct()
    {
        parent::__construct();

        $this->header('Content-Type', 'application/json');
    }

    public function post(array $data): parent
    {
        $json = json_encode($data);
        curl_setopt($this->handle(), CURLOPT_POSTFIELDS, $json);

        return $this;
    }

    /**
     * @return JSON | null
     */
    public function getContent()
    {
        $content = parent::getContent();

        if ($content === false)
        {
            throw new Exception($this->getErrorString());
        }

        if ($content === null)
            return null;

        return json_decode($content);
    }
}

