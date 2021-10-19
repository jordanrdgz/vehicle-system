<?php

namespace Application\Model;

class Motorcycle extends Vehicle
{
    protected $motorcycleType;

    public function __construct($dbAdapter, $horsepower = null, $totalTires = null, $motorcycleType = null)
    {
        parent::__construct($dbAdapter, $horsepower, $totalTires);
        $this->motorcycleType = $motorcycleType;
    }

    public function validate()
    {
        parent::validate();

        if(empty($this->motorcycleType)) {
            $this->validationErrors[] = 'The motorcycle type must be specified';
        }

        return !$this->validationErrors;
    }

    public function setType($motorcycleType)
    {
        $this->motorcycleType = $motorcycleType;
    }

    public function getType()
    {
        return $this->motorcycleType;
    }

    public static function getTypes()
    {
        return [
            'Standard',
            'Cruiser',
            'Sport Bike',
            'Touring',
            'Sport Touring',
            'Dual Sport',
            'Scooter',
            'Moped',
            'Off-road'
        ];
    }

    public function getAll()
    {
        $query = '
            SELECT
                m.*,
                v.*
            FROM
                motorcycle m
                LEFT JOIN vehicle v ON m.vehicle_id = v.vehicle_id
        ';

        $statement = $this->dbAdapter->query( $query );

        $res = $statement->execute();

        $vehicles = [];
        while( $row = $res->next() ) {
            $vehicles[] = $row;
        }

        return $vehicles;
    }

    public function getById($motorcycleId)
    {
        $query = '
            SELECT
                m.*,
                v.*
            FROM
                motorcycle m
                LEFT JOIN vehicle v ON m.vehicle_id = v.vehicle_id
            WHERE
                motorcycle_id = :motorcycle_id
        ';

        $params = array(
            'motorcycle_id' => $motorcycleId
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
            'motorcycle_type' => $this->motorcycleType
        ];

        $query = '
            INSERT INTO motorcycle
            (
                vehicle_id,
                motorcycle_type
            )
            VALUES
            (
                :vehicle_id,
                :motorcycle_type
            )
            ';

        $this->dbAdapter->query( $query )->execute( $insertParams );

        $newMotorcycleId = $this->dbAdapter->getDriver()->getLastGeneratedValue();

        return $this->getById($newMotorcycleId);
    }
}