<?php

use \Mockery as m;

require '../forms/RosterForm.php';

class RosterFormTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testFormDetectsBadInput()
    {
        // Create our fake _POST payload
        $_POST = array(
            'tig' => array(
                0 => array (
                    'id'        => 'id.0',
                    'tig_name'  => 'tig_name.0',
                    'item_type' => 'item_type.0',
                    'comments'  => 'comments.0',
                    'status'    => 'status.0',
                ),
                1 => array (
                    'id'        => 'id.1',
                    'tig_name'  => 'tig_name.1',
                    'item_type' => 'item_type.1',
                    'comments'  => 'comments.1',
                    'status'    => 'status.1',
                ),
                2 => array (
                    'id'        => 'id.2',
                    'tig_name'  => 'tig_name.2',
                    'item_type' => 'item_type.2',
                    'comments'  => 'comments.2',
                    'status'    => 'status.2',
                ),
            ),
        );
        // Create our form
        $builder = new Aura\Input\Builder(array(
            'tig_record' => function () {
                $tig_record = new Aura\Input\Fieldset(
                    new Aura\Input\Builder,
                    new Aura\Input\Filter
                );
                $tig_record->setField('id', 'hidden');
                $tig_record->setField('tig_name');
                $tig_record->setField('item_type');
                $tig_record->setField('comments');
                $tig_record->setField('status');
                return $tig_record;
            }
        ));
        $form = new RosterForm($builder, new Aura\Input\Filter());

        // Pass it the payload
        $form->setCollection('tig', 'tig_record');
        $form->fill($_POST);

        // Filter and validate
        $response = $form->filter();

        $this->assertFalse(
            $response,
            "RosterForm did not detect bad form input values"
        );
    }
}
