<?php

use \Mockery as m;

class RosterModelTest extends PHPUnit_Framework_TestCase
{
    private $prophecy;

    public function setup()
    {
        $this->prophecy = new \Prophecy\Prophet;
    }

    public function tearDown()
    {
        $this->prophecy->checkPredictions();
        m::close();
    }

    public function testGetByNicknameReturnsExpectedList()
    {
        // Choose a nickname
        $nickname = 'MAD';

        // Roster lists should contain the following indexes
        // id, tig_name, ibl_team, item_type, status, comments
        $expected_roster = [
            [1, 'FOO Fighter', 'MAD', 1, 1, 'Test entry 1'],
            [2, 'TOR Bautista', 'MAD', 1, 1, 'Joey Bats'],
            [3, 'TOR Dickey', 'MAD', 2, 0, 'Trade 7/13'],
            [4, 'MAD#1', 'MAD', 3, 0, null]
        ];

        // Create our mock DB connection with the methods we need
        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('fetchAll')->andReturn($expected_roster);

        // Pass in an array as the second parameter to not run the constructor
        $db = m::mock('PDODouble', []);
        $db->shouldReceive('prepare')->andReturn($stmt);

        $roster = new Roster($db);
        $test_roster = $roster->getByNickname($nickname);

        $this->assertEquals(
            $expected_roster,
            $test_roster,
            'getByNickname did not return expected roster'
        );
    }

    public function testBadNicknameReturnsEmptyRoster()
    {
        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('fetchAll')->andReturn(null);

        // Pass in an array as the second parameter to not run the constructor
        $db = m::mock('PDODouble', []);
        $db->shouldReceive('prepare')->andReturn($stmt);

        $roster = new Roster($db);
        $test_roster = $roster->getByNickname('BAD NICKNAME');

        $this->assertEquals(
            [],
            $test_roster,
            'Bad nickname did not return empty roster'
        );
    }

    public function testUpdatePlayerTeam()
    {
        $expected_values = [
            'ibl_team' => 'MAD',
            'id' => 1
        ];
        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')->andReturn(true);

        $db = m::mock('PDODouble', []);
        $db->shouldReceive('prepare')->andReturn($stmt);

        $roster = new Roster($db);
        $expected_response = true;
        $response = $roster->updatePlayerTeam('MAD', 1);

        $this->assertEquals(
            $expected_response,
            $response,
            'updatePlayerTeam() did not return expected TRUE response'
        );
    }

    public function testDeletePlayerByIdWorksAsExpected()
    {
        /*
        $new_delete = $this->createMockNewDelete();

        $player_values = ['player_id' => 1];
        $db = $this->getMockBuilder('stdClass')
            ->setMethods(['newDelete', 'query'])
            ->getMock();
        $db->expects($this->once())
            ->method('newDelete')
            ->will($this->returnValue($new_delete));
        $db->expects($this->once())
            ->method('query')
            ->with($new_delete, $player_values)
            ->will($this->returnValue(true));

        $roster = new Roster($db);
        $response = $roster->deletePlayerById(1);

        $this->assertEquals(
            true,
            $response,
            'deletePlayerById() did not return expected true response'
        );
         */
    }

    public function testReleasePlayerByListWorksAsExpected()
    {
        /*
        // Create a list of player ID's
        $release_list = [1, 2, 3, 4, 5];

        // Mock the object returned by $db->newUpdate()
        $update = m::mock('stdClass');
        $update->shouldReceive('table', 'cols', 'set', 'where')->andReturn($update);

        // Mock the object returned by $db->newDelete()
        $delete = m::mock('stdClass');
        $delete->shouldReceive('from', 'where')->andReturn($delete);

        // Mock the object returned by $db->newSelect()
        $select = m::mock('stdClass');
        $select->shouldReceive('cols', 'from', 'where')->andReturn($select);

        // Mock our database object
        $db = m::mock('stdClass');
        $db->shouldReceive('newDelete')->andReturn($delete);
        $db->shouldReceive('newUpdate')->andReturn($update);
        $db->shouldReceive('newSelect')->andReturn($select);
        $db->shouldReceive('fetchOne')->andReturn(
            ['status' => 1],
            ['status' => 1],
            ['status' => 1],
            ['status' => 1],
            ['status' => 3]
        );
        $db->shouldReceive('query')->with($update, ['id' => 1])->once();
        $db->shouldReceive('query')->with($update, ['id' => 2])->once();
        $db->shouldReceive('query')->with($update, ['id' => 3])->once();
        $db->shouldReceive('query')->with($update, ['id' => 4])->once();
        $db->shouldReceive('query')->with($delete, ['id' => 5])->once();

        $roster = new Roster($db);
        $roster->releasePlayerByList($release_list);
         */
    }


    public function testAddPlayerAddsPlayerCorrectly()
    {
        /*
        // Create an array of player data
        $player_data = [
            'tig_name' => 'Testy McTesterton',
            'ibl_team' => 'MAD',
            'item_type' => 1,
            'comments' => 'Test item'
        ];

        // Create a mock insert object
        $insert = $this->getMockBuilder('stdClass')
            ->setMethods(['into', 'cols'])
            ->getMock();
        $insert->expects($this->once())
            ->method('cols')
            ->will($this->returnValue($insert));
        $insert->expects($this->once())
            ->method('into')
            ->will($this->returnValue($insert));

        // Create a mock DB connection
        $db = $this->getMockBuilder('stdClass')
            ->setMethods(['newInsert', 'query'])
            ->getMock();
        $db->expects($this->once())
            ->method('newInsert')
            ->will($this->returnValue($insert));
        $db->expects($this->once())
            ->method('query')
            ->will($this->returnValue(true));

        // Create a new roster object
        $roster = new Roster($db);

        // Assert that true was returned when adding a player
        $this->assertTrue(
            $roster->addPlayer($player_data),
            'addPlayer() did not correctly add a new player'
        );
         */
    }
}
