<?php
namespace CodeClimate\Bundle\TestReporterBundle;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class ApiClient
{
    protected $apiHost;
    protected $certFile;

    public function __construct()
    {
        $this->apiHost  = "https://codeclimate.com";
        $this->certFile = dirname(__FILE__) . "/../../../cacert.pem";

        if (isset($_SERVER["CODECLIMATE_API_HOST"])) {
          $this->apiHost = $_SERVER["CODECLIMATE_API_HOST"];
        }
    }

    public function send($json)
    {
        $client = new Client($this->apiHost, array(
          "ssl.certificate_authority" => $this->certFile
        ));

        $request  = $client->createRequest('POST', "/test_reports");
        $response = false;

        $request->setHeader("User-Agent", "Code Climate (PHP Test Reporter v".Version::VERSION.")");
        $request->setHeader("Content-Type", "application/json");
        $request->setBody($json);

        try {
            $response = $client->send($request);
        } catch (ClientErrorResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}
