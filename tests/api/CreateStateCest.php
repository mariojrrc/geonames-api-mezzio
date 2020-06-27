<?php

class CreateStateCest
{
    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader('X-Api-Key', 'e0f66c28-f348-4304-9609-3169f0cd07cf');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    // tests
    public function createStateViaAPI(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/v1/state', [
            'name' => 'São paulo',
            'shortName' => 'SP'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains('"id":');
    }
}
