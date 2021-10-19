<?php

namespace Application\Model;

class Sedan extends Vehicle
{
    protected $sedanType;

    public function __construct($dbAdapter, $horsepower = null, $totalTires = null, $sedanType = null)
    {
        parent::__construct($dbAdapter, $horsepower, $totalTires);
        $this->sedanType = $sedanType;
    }

    public function validate()
    {
        parent::validate();

        if(empty($this->sedanType)) {
            $this->validationErrors[] = 'The sedan type must be specified';
        }

        return !$this->validationErrors;
    }

    public function setType($sedanType)
    {
        $this->sedanType = $sedanType;
    }

    public function getType()
    {
        return $this->sedanType;
    }

    public static function getTypes()
    {
        return [
            'Subcompact',
            'Compact',
            'Large family',
            'Full-size sedan',
            'Executive',
            'Full-size luxury'
        ];
    }

    public function getAll()
    {
        $query = '
            SELECT
                s.*,
                v.*
            FROM
                sedan s
                LEFT JOIN vehicle v ON s.vehicle_id = v.vehicle_id
        ';

        $statement = $this->dbAdapter->query( $query );

        $res = $statement->execute();

        $vehicles = [];
        while( $row = $res->next() ) {
            $vehicles[] = $row;
        }

        return $vehicles;
    }

    public function getById($sedanId)
    {
        $query = '
            SELECT
                s.*,
                v.*
            FROM
                sedan s
                LEFT JOIN vehicle v ON s.vehicle_id = v.vehicle_id
            WHERE
                sedan_id = :sedan_id
        ';

        $params = array(
            'sedan_id' => $sedanId
        );

        $statement = $this->dbAdapter->query( $query );

        $res = $statement->execute( $params );

        return $res->current();
    }

    public function insert()
    {
        $vehicleId = parent::insert();

        $insertParams = [
            'vehicle_id' => $vehicleId,
            'sedan_type' => $this->sedanType
        ];

        $query = '
            INSERT INTO sedan
            (
                vehicle_id,
                sedan_type
            )
            VALUES
            (
                :vehicle_id,
                :sedan_type
            )
            ';

        $this->dbAdapter->query( $query )->execute( $insertParams );

        $newSedanId = $this->dbAdapter->getDriver()->getLastGeneratedValue();

        return $this->getById($newSedanId);
    }
}