<?php
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Custom adapter for paginating through IBL rotations
 */
class RotationAdapter implements AdapterInterface
{
    private $array;

    public function __construct($rotations, $franchises)
    {
        $this->array = $rotations;
        $this->franchises = $franchises;
    }

    public function getNbResults()
    {
        return count($this->array);
    }

    public function getSlice($offset, $length)
    {
        return array_slice($this->array, $offset, $length);
    }

    public function processByWeek()
    {
        $franchises_ids = [];
        foreach ($this->franchises as $id => $franchise) {
            $franchise_ids[] = $id;
        }

        $rotations_by_week = [];

        // Group all rotations by week
        foreach ($this->array as $row) {
            $data = $row;
            $data['nickname'] = $this->franchises[$row['franchise_id']];
            $rotations_by_week[$row['week']][] = $data;
        }

        // Then go through each week and add blanks for any missing franchises
        foreach ($rotations_by_week as $week => $rotation_for_week) {
            $franchise_rotation = [];
            $week = null;

            foreach ($rotation_for_week as $rotation) {
                $week = $rotation['week'];
                $franchise_rotation[] = $rotation['franchise_id'];
            }

            foreach ($franchise_ids as $franchise_id) {
                if (!in_array($franchise_id, $franchise_rotation)) {
                    $rotations_by_week[$week][] = [
                        'id' => 0,
                        'week' => $week,
                        'franchise_id' => $franchise_id,
                        'nickname' => $this->franchises[$franchise_id],
                        'rotation' => null
                    ];
                }
            }
        }

        // Rebuild our array
        $this->array = [];

        foreach ($rotations_by_week as $rotation_for_week) {
            foreach ($rotation_for_week as $key => $row) {
                $nickname[$key] = $row['nickname'];
            }

            $data = $rotation_for_week;
            array_multisort($nickname, SORT_ASC, SORT_STRING, $data);
            $this->array = array_merge($this->array, $data);
        }
    }
}
