<?php

namespace QowisioCloudApiBundle\Service;

use QowisioCloudApiBundle\CookedMeasure\Coordinate;

/**
 * service to get data from a GPS device (named tracker)
 * ref: qowisio.tracker
 */
class QowisioTrackerService
{
    const LAT_SUFFIX = '.lat';
    const LNG_SUFFIX = '.lon';

    const SORT_DESC = 'DESC';
    const SORT_ASC = 'ASC';

    /**
     *
     * @var QowisioApiMeasuresService
     */
    protected $measuresService;

    /**
     * Constructor
     * @param \QowisioCloudApiBundle\Service\QowisioApiMeasuresService $measuresService
     */
    public function __construct(QowisioApiMeasuresService $measuresService)
    {
        $this->measuresService = $measuresService;
    }

    /**
     * Get the 100 last GPS coordinates from the device
     * @param string $deviceUid the UID of the device
     * @param string $sort optionnal default QowisioTrackerService::SORT_DESC. one of QowisioTrackerService::SORT_DESC or QowisioTrackerService::SORT_ASC
     */
    public function getLastCoordinates($deviceUid, $sort = self::SORT_DESC)
    {
        $latitudeList = $this->measuresService->getLastMeasures($deviceUid.static::LAT_SUFFIX);
        $longitudeList = $this->measuresService->getLastMeasures($deviceUid.static::LNG_SUFFIX);

        return $this->getGpsCoordinates($latitudeList, $longitudeList, $sort);
    }

    /**
     * Get the GPS coordinates from the device
     * @param string $deviceUid the UID of the device
     * @param \DateTime $from Date when search starts
     * @param \DateTime $to Date when search stop
     * @param integer   $limit optionnal default 100
     * @param string    $sort optionnal default QowisioTrackerService::SORT_DESC. one of QowisioTrackerService::SORT_DESC or QowisioTrackerService::SORT_ASC
     */
    public function getCoordinates($deviceUid, \DateTime $from, \DateTime $to, $limit = 100, $sort = self::SORT_DESC)
    {
        $latitudeList = $this->measuresService->getMeasures($deviceUid.static::LAT_SUFFIX, $from, $to, $limit);
        $longitudeList = $this->measuresService->getMeasures($deviceUid.static::LNG_SUFFIX, $from, $to, $limit);

        return $this->getGpsCoordinates($latitudeList, $longitudeList, $sort);
    }

    /**
     * Combine the two lists of latitudes and longitudes to get a list of coordinates.
     * The combination uses the timestamplog_collected property to ensure the pair of values
     * @param array  $latitudeList  list of sensor measure containing GPS latitudes
     * @param array  $longitudeList list of sensor measure containing GPS longitudes
     * @param string $sort One of QowisioTrackerService::SORT_DESC or QowisioTrackerService::SORT_ASC
     * @return array<Coordinates>
     */
    protected function getGpsCoordinates($latitudeList, $longitudeList, $sort)
    {
        $coordinates = [];
        foreach ($latitudeList as $latitudeMeasure) {
            foreach ($longitudeList as $longitudeMeasure) {
                if ($longitudeMeasure->timestamplog_collected === $latitudeMeasure->timestamplog_collected) {
                    $coordinates[] = new Coordinate($latitudeMeasure->measure_cooked, $longitudeMeasure->measure_cooked, $latitudeMeasure->timestamplog_collected);
                }
            }
        }

        if ($sort === static::SORT_ASC) {
            $ascending = function($coordA, $coordB) {
                if ($coordA->getTimestampCollected() === $coordB->getTimestampCollected()) {
                    return 0;
                }
                return $coordA->getTimestampCollected() < $coordB->getTimestampCollected() ? -1 : 1;
            };
            usort($coordinates, $ascending);
        }

        return $coordinates;
    }
}
