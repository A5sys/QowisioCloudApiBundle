## Qowisio Cloud API Bundle ##

Qowisio offers an UNB network to let connected object send their data, hardware to create connected objects themselves, a Web API to retreive the data stored into their cloud, and many more things.

This bundle focuses on the Qowisio Cloud API.

The connected object has already sent its data through the UNB network, the data has already been read by the Qowisio cloud preprocessing programs, and now, you can use this bundle to query the Qowisio Cloud API to get "cooked" data.

**Why cooked data ?**

A connected object using a UNB network is designed to be the less electric consuming as possible. For that reason, the message sent over UNB network must be as short as possible (12 bytes max). Data is binary encoded, and then sent over the UNB network. If their is no preprocessing, its up to you to extract all values of the message.

Example : GPS is latitude and longitude. 2 values are sent at the same time in the message, but on the API, you will access 2 data.

# Install #

composer

    composer require "a5sys/qowisio-cloud-api-bundle"

AppKernel.php

    $bundles = [
        ...
        new A5sys\QowisioCloudApiBundle\QowisioCloudApiBundle(),
        ...
    ];

# Configuration #

config.yml

	qowisio_cloud_api:
     	authentication:
    	    email: %qowisio_api_email%
    	    password: %qowisio_api_password%

parameters.yml

	qowisio_api_email: email_you_suscribed_with@tld.com
    qowisio_api_password: 'yourpassword'

Note : to get your Qowisio account : https://developer.qowisio.com/log

# Implemented functionnalities of the bundle VS API capabilities #

**Authentication API**

*Authentication*

- [ ] GET /confirm/{hash}
- [x] POST /login > *used for each query in the Data API*
- [ ] POST /resendconfirm
- [ ] POST /signup
- [ ] POST /password/reset
- [ ] POST /password/update
- [ ] POST /user/update

**Data API**

*Authentication*

- [x] GET /amiauthenticated

*Package*

- [ ] GET /packages
- [ ] GET /packages/{id}

*Devices / Sensors*

- [x] GET /devices
- [ ] PUT /devices
- [ ] POST /devices
- [ ] DELETE /devices
- [x] GET /devices/type
- [x] GET /devices/{uid}/sensors

*Measures*

- [x] GET /measures/{sensor_id}/{from}/{to}/{limit}

*Aggregations*

- [ ] GET /aggregations/{sensor_uid}/lastday
- [ ] GET /aggregations/{sensor_uid}/lastweek
- [ ] GET /aggregations/{sensor_uid}/lastmonth
- [ ] GET /aggregations/{sensor_uid}/lastquarter
- [ ] GET /aggregations/{sensor_uid}/{from}/{to}
- [ ] GET /aggregations/{sensor_uid}/{agg}/{from}/{to}/{limit}

# Usage #

All available **services** automatically authenticate before sending any query to the Cloud API:

**qowisio.cloud.api.authentication**

Get infos about authentication. 

**qowisio.cloud.api.devices.and.sensors**

Get infos about devices and their sensors

**qowisio.cloud.api.measures**

Get infos about sensor measures
        
**qowisio.tracker**

Get info about the specific tracker device provided during IoT connected days.

Returns a list of specific Coordinate object, in which you find latitude, longitude and a date. 

**qowisio.cloud.api.caller**

makes the calls to API. You should use it if you want to develop a not yet implemented call.

# Resources #
https://developer.qowisio.com/dev/documentation/cloudapi

