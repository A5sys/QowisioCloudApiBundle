<?php

namespace QowisioCloudApiBundle\Service;

/**
 * Access description of your objects and sensors
 * ref: qowisio.cloud.api.measures
 */
class QowisioApiMeasuresService
{
    const FUNCTION_MEASURES = 'measures/{sensor_id}/{from}/{to}/{limit}';

    /**
     * WS API caller
     * @var QuowisioApiCaller
     */
    protected $apiCaller;

    /**
     * Constructor
     * @param \QowisioCloudApiBundle\Service\QowisioApiCaller $apiCaller
     */
    public function __construct(QowisioApiCaller $apiCaller)
    {
        $this->apiCaller = $apiCaller;
    }

    /**
     * Return a maximum of 100 measures from present to past.<br>
     * @param string  $sensorUid
     * @param integer $limit
     * @return array
     */
    public function getLastMeasures($sensorUid, $limit = 100)
    {
        $from = 0;
        $to = 0;

        $urlFunction = str_replace(['{sensor_id}', '{from}', '{to}', '{limit}'], [$sensorUid, $from, $to, $limit], static::FUNCTION_MEASURES);

        return $this->apiCaller->callWs($urlFunction);
    }

    /**
     * Return a maximum of 100 measures from present to past.<br>
     * You can add 'from_date' and 'to_date' filters to request data on a specific range and 'limit', to limit the number of results.<br>
     * @param string $sensorUid
     * @param \DateTime $from Date when search starts
     * @param \DateTime $to Date when search stop
     * @param integer   $limit
     * @return array
     */
    public function getMeasures($sensorUid, \DateTime $from, \DateTime $to, $limit = 100)
    {
        $urlFunction = str_replace(['{sensor_id}', '{from}', '{to}', '{limit}'], [$sensorUid, $from->getTimestamp(), $to->getTimestamp(), $limit], static::FUNCTION_MEASURES);

        return $this->apiCaller->callWs($urlFunction);
    }
}
