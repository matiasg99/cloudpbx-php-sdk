<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');
use PHPUnit\Framework\TestCase;
use Cloudpbx\Protocol;
use Cloudpbx\Util;

class IvrMenuTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer([]);
    }

    public function testCreateIvrMenu(): void
    {
        $customer = $this->customer;

        $ivr = $this->client->ivrMenus->create($customer->id, [
            'name' => 'test-ivr-mune',
            'description' => 'test ivr menu'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\IvrMenu::class, $ivr);
        $this->assertTrue($ivr->id > 0);
        $this->assertEquals('test-ivr-mune', $ivr->name);
        $this->assertEquals('test ivr menu', $ivr->description);
    }

    public function testQueryAllIvrMenu(): void
    {
        $this->createDefaultIvrMenu($this->customer->id);

        $ivrmenus = $this->client->ivrMenus->all($this->customer->id);

        $this->assertIsArray($ivrmenus);
        $this->assertGreaterThan(0, count($ivrmenus));

        $ivrmenu = $ivrmenus[0];
        $this->assertTrue($ivrmenu->hasAttribute('id'));
        $this->assertTrue($ivrmenu->hasAttribute('name'));
        $this->assertEquals($this->customer->id, $ivrmenu->customer->id);
    }

    public function testDeleteIvrMenu(): void
    {
        $customer = $this->customer;
        $ivr = $this->createDefaultIvrMenu($customer->id);

        $this->client->ivrMenus->delete($customer->id, $ivr->id);

        try {
            $this->client->ivrMenus->show($customer->id, $ivr->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testQueryAllIvrMenuEntries(): void
    {
        $this->markTestIncomplete(
            'This test not have staging environment'
        );
        return;

        $customer = $this->customer;
        $ivrmenu = /** not implemented */

        $entries = $this->client->ivrMenuEntries->all(self::$customer_id, $ivrmenu->id);

        $this->assertIsArray($entries);
        $this->assertGreaterThan(0, count($entries));

        $entry = $entries[0];
        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('digits'));
        $this->assertTrue($entry->hasAttribute('param'));
        $this->assertTrue($entry->hasAttribute('action'));
    }

    public function testUpdateIvrMenu(): void
    {
        $customer = $this->customer;
        $ivr = $this->createDefaultIvrMenu($customer->id);
        $sound_greet_long = $this->createIvrSound($customer->id, 'ivr_greet_long');
        $sound_greet_short = $this->createIvrSound($customer->id, 'ivr_greet_short');
        $sound_invalid = $this->createIvrSound($customer->id, 'ivr_invalid');
        $sound_exit = $this->createIvrSound($customer->id, 'ivr_exit');

        $ivr_updated = $this->client->ivrMenus->update($customer->id, $ivr->id, [
            'timeout' => 33,
            'inter_digit_timeout' => 5,
            'max_failures' => 3,
            'digit_len' => '1',
            'greet_long_sound_id' => $sound_greet_long->id,
            'greet_short_sound_id' => $sound_greet_short->id,
            'invalid_sound_id' => $sound_invalid->id,
            'exit_sound_id' => $sound_exit->id
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\IvrMenu::class, $ivr_updated);
        $this->assertEquals($ivr->id, $ivr_updated->id);
        $this->assertEquals(33, $ivr_updated->timeout);
        $this->assertEquals(5, $ivr_updated->inter_digit_timeout);
        $this->assertEquals(3, $ivr_updated->max_failures);
        $this->assertEquals('1', $ivr_updated->digit_len);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Sound::class, $this->client->preload($ivr_updated->greet_long_sound));
        $this->assertTrue($ivr_updated->hasAttribute('greet_short_sound_id'));
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Sound::class, $this->client->preload($ivr_updated->greet_short_sound));
        $this->assertTrue($ivr_updated->hasAttribute('invalid_sound_id'));
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Sound::class, $this->client->preload($ivr_updated->invalid_sound));
        $this->assertTrue($ivr_updated->hasAttribute('exit_sound_id'));
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Sound::class, $this->client->preload($ivr_updated->exit_sound));
    }

    private function createIvrSound($customer_id, $section) {
        return $this->client->sounds->create($customer_id, $this->generateRandomName(), 'default', $section, 'tests/integration/example.ogg');
    }
}
