<?php

use ChartMogul\Http\Client;
use ChartMogul\Subscription;
use ChartMogul\Exceptions\ChartMogulException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;

class SubscriptionTest extends PHPUnit_Framework_TestCase
{

  const ALL_SUBS_JSON = '{
    "customer_uuid": "cus_f466e33d-ff2b-4a11-8f85-417eb02157a7",
    "subscriptions": [
        {
            "uuid": "sub_dd169c42-e127-4637-8b8f-a239b248e3cd",
            "external_id": "abc",
            "cancellation_dates": [],
            "plan_uuid": "pl_d6fe6904-8319-11e7-82b4-ffedd86c182a",
            "data_source_uuid": "ds_637442a6-8319-11e7-a280-1f28ec01465c"
        }
    ],
    "current_page": 2,
    "total_pages": 3
}';
    public function testAllSubscriptions()
    {
        $stream = Psr7\stream_for(SubscriptionTest::ALL_SUBS_JSON);
        $response = new Response(200, ['Content-Type' => 'application/json'], $stream);
        $mockClient = new \Http\Mock\Client();
        $mockClient->addResponse($response);

        $cmClient = new Client(null, $mockClient);
        $query = ['customer_uuid' => 'cus_f466e33d-ff2b-4a11-8f85-417eb02157a7'];
        $result = Subscription::all($query, $cmClient);
        $request = $mockClient->getRequests()[0];

        $this->assertEquals("GET", $request->getMethod());
        $uri = $request->getUri();
        $this->assertEquals("", $uri->getQuery());
        $this->assertEquals("/v1/import/customers/cus_f466e33d-ff2b-4a11-8f85-417eb02157a7/subscriptions", $uri->getPath());

        $this->assertEquals(1, sizeof($result));
        $this->assertTrue($result[0] instanceof Subscription);
        $this->assertEquals("cus_f466e33d-ff2b-4a11-8f85-417eb02157a7", $result->customer_uuid);
        $this->assertEquals("sub_dd169c42-e127-4637-8b8f-a239b248e3cd", $result[0]->uuid);
        $this->assertEquals(2, $result->current_page);
        $this->assertEquals(3, $result->total_pages);
    }
}
