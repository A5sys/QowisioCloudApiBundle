<?php

namespace QowisioCloudApiBundle\Service;

/**
 * Access description of your objects and sensors
 * ref: qowisio.cloud.api.devices.and.sensors
 */
class QowisioApiDevicesAndSensorsService
{
    const FUNCTION_DEVICES = 'devices';
    const FUNCTION_DEVICES_TYPES = 'devices/type';
    const FUNCTION_DEVICE_SENSORS = 'devices/{uid}/sensors';

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
     * Request the devices that belongs to the user, filtered by type or role (GET parameter i.e: /devices?type=<type>&role=<role>).
     * @param string $type optionnal filter by type (example QOWISIOSDKGPS)
     * @param string $role optionnal filter by role
     * @return array
     */
    public function getDevices($type = null, $role = null)
    {
        $params = array();
        if ($type !== null) {
            $params['type'] = $type;
        }
        if ($role !== null) {
            $params['role'] = $role;
        }

        return $this->apiCaller->callWs(static::FUNCTION_DEVICES, QowisioApiCaller::REST_GET, $params);
    }

    /**
     * List all the device type known by this user, eventually filtered by user role (GET parameter, &role=<role>).
     * @param string $role
     * @return array
     */
    public function getDevicesTypes($role = null)
    {
        $params = array();
        if ($role !== null) {
            $params = ['role' => $role];
        }

        return $this->apiCaller->callWs(static::FUNCTION_DEVICES_TYPES, QowisioApiCaller::REST_GET, $params);
    }

    /**
     * Return the sensors descriptions and last values that are linked to the object uid.
     * @param string $deviceUid
     * @return array
     */
    public function getDeviceSensors($deviceUid = null)
    {
        $urlFunction = str_replace('{uid}', $deviceUid, static::FUNCTION_DEVICE_SENSORS);

        return $this->apiCaller->callWs($urlFunction);
    }
}
