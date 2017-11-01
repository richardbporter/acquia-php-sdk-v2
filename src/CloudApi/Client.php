<?php

namespace AcquiaCloudApi\CloudApi;

use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\Key;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use AcquiaCloudApi\Response\CloudApiResponse;

/**
 * Class Client
 * @package AcquiaCloudApi\CloudApi
 */
class Client extends GuzzleClient
{
    /**
     * @var string BASE_URI
     */
    const BASE_URI = 'https://cloud.acquia.com/api';

    /**
     * @param array $config
     * @return static
     */
    public static function factory(Array $config = array())
    {

        $key = new Key($config['key'], $config['secret']);
        $middleware = new HmacAuthMiddleware($key);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        // @TODO set query string options here for sort, filter, limit, offset

        $client = new static([
            'handler' => $stack,
        ]);

        return $client;
    }

    /**
     * @param string $verb
     * @param string $path
     * @param array $options
     * @return CloudApiResponse
     */
    private function makeRequest(String $verb, String $path, Array $options = array())
    {

        // @TODO sort, filter, limit, offset
        // Sortable by: 'name', 'label', 'weight'.
        // Filterable by: 'name', 'label', 'weight'.

        // $options['query'] = [
        //    'sort' => 'name,-weight',
        //    'filter' => 'name=test',
        //    'limit' => 1,
        //    'offset' => 1,
        //];


        try {
            $response = $this->$verb(self::BASE_URI . $path, $options);
        } catch (ClientException $e) {
            print $e->getMessage();
            $response = $e->getResponse();
        }

        return new CloudApiResponse($response);
    }

    /**
     * @return CloudApiResponse
     */
    public function applications()
    {
        return $this->makeRequest('get', '/applications');
    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function application($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}");
    }

    /**
     * @param string $uuid
     * @param string $name
     * @return CloudApiResponse
     */
    public function applicationRename($uuid, $name)
    {

        $options = [
            'form_params' => [
                'name' => $name,
            ],
        ];

        return $this->makeRequest('put', "/applications/${uuid}", $options);
    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function code($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}/code");
    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function features($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}/features");
    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function databases($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}/databases");
    }

    /**
     * @param string $uuid
     * @param string $source
     * @return CloudApiResponse
     */
    public function databaseCopy($uuid, $source)
    {
        $options = [
            'form_params' => [
                'source' => $source,
            ],
        ];

        return $this->makeRequest('post', "/applications/${uuid}/databases", $options);
    }

    /**
     * @param string $uuid
     * @param string $name
     * @return CloudApiResponse
     */
    public function databaseCreate($uuid, $name)
    {
        $options = [
            'form_params' => [
                'name' => $name,
            ],
        ];

        return $this->makeRequest('post', "/applications/${uuid}/databases", $options);
    }

    /**
     * @param string $uuid
     * @param string $name
     * @return CloudApiResponse
     */
    public function databaseDelete($uuid, $name)
    {
        return $this->makeRequest('post', "/applications/${uuid}/databases/${name}");
    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function insight($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}/insight");
    }

    /**
     * @param string $id
     * @param string $dbName
     * @return CloudApiResponse
     */
    public function databaseBackup($id, $dbName)
    {
        return $this->makeRequest('post', "/environments/${id}/databases/${dbName}/backups");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function copyFiles($id)
    {
        return $this->makeRequest('post', "/environments/${id}/files");
    }

    /**
     * @param string $id
     * @param string $branch
     * @return CloudApiResponse
     */
    public function switchCode($id, $branch)
    {

        $options = [
            'form_params' => [
                'branch' => $branch,
            ],
        ];

        return $this->makeRequest('post', "/environments/${id}/code/actions/switch", $options);
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function domains($id)
    {
        return $this->makeRequest('get', "/environments/${id}/domains");
    }

    /**
     * @param string $id
     * @param string $hostname
     * @return CloudApiResponse
     */
    public function addDomain($id, $hostname)
    {

        $options = [
            'form_params' => [
                'hostname' => $hostname,
            ],
        ];

        return $this->makeRequest('post', "/environments/${id}/domains", $options);
    }

    /**
     * @param string $id
     * @param string $domain
     * @return CloudApiResponse
     */
    public function deleteDomain($id, $domain)
    {
        return $this->makeRequest('delete', "/environments/${id}/domains/${domain}");
    }

    /**
     * @param string $id
     * @param string $domains
     * @return CloudApiResponse
     */
    public function purgeVarnishCache($id, $domains)
    {

        $options = [
            'form_params' => [
                'domains' => $domains,
            ],
        ];

        return $this->makeRequest('post', "/environments/${id}/domains/actions/clear-varnish", $options);
    }

    /**
     *
     */
    public function databaseBackups()
    {

    }


    /**
     *
     */
    public function moveDomain()
    {

    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function tasks($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}/tasks");
    }

    /**
     * @param string $uuid
     * @return CloudApiResponse
     */
    public function environments($uuid)
    {
        return $this->makeRequest('get', "/applications/${uuid}/environments");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function environment($id)
    {
        return $this->makeRequest('get', "/environments/${id}");
    }

    /**
     * @param string $id
     * @param string $label
     * @return CloudApiResponse
     */
    public function environmentLabel($id, $label)
    {

        $options = [
            'form_params' => [
                'label' => $label,
            ],
        ];

        return $this->makeRequest('post', "/environments/${id}/actions/change-label", $options);
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function servers($id)
    {
        return $this->makeRequest('get', "/environments/${id}/servers");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function enableLiveDev($id)
    {
        return $this->makeRequest('post', "/environments/${id}/livedev/actions/enable");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function disableLiveDev($id)
    {
        return $this->makeRequest('post', "/environments/${id}/livedev/actions/disable");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function enableProductionMode($id)
    {
        return $this->makeRequest('post', "/environments/${id}/production-mode/actions/enable");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function disableProductionMode($id)
    {
        return $this->makeRequest('post', "/environments/${id}/production-mode/actions/disable");
    }

    /**
     * @param string $id
     * @return CloudApiResponse
     */
    public function crons($id)
    {
        return $this->makeRequest('get', "/environments/${id}/crons");
    }

    /**
     * @param string $id
     * @param string $command
     * @param string $frequency
     * @param string $label
     * @return CloudApiResponse
     */
    public function addCron($id, $command, $frequency, $label)
    {

        $options = [
            'form_params' => [
                'command' => $command,
                'frequency' => $frequency,
                'label' => $label,
            ],
        ];

        return $this->makeRequest('post', "/environments/${id}/crons", $options);
    }

    /**
     * @param string $id
     * @param string $cronId
     * @return CloudApiResponse
     */
    public function deleteCron($id, $cronId)
    {
        return $this->makeRequest('delete', "/environments/${id}/crons/${cronId}");
    }

    /**
     * @return CloudApiResponse
     */
    public function drushAliases()
    {
        return $this->makeRequest('get', '/account/drush-aliases/download');
    }

}
