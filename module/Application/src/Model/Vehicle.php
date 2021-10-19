<?php

namespace Application\Model;

abstract class Vehicle
{
    protected $dbAdapter;
    protected $horsepower;
    protected $totalTires;
    protected $model;
    protected $color;
    protected $validationErrors = [];

    public function __construct($dbAdapter, $horsepower = null, $totalTires = null, $model = null, $color = null)
    {
        $this->dbAdapter = $dbAdapter;
        $this->horsepower = $horsepower;
        $this->totalTires = $totalTires;
        $this->model = $model;
        $this->color = $color;
    }

    protected function validate(){
        if(empty($this->horsepower)) {
            $this->validationErrors[] = 'The horesepower must be specified';
        }

        if(empty($this->totalTires)) {
            $this->validationErrors[] = 'The total tires must be specified';
        }

        if(empty($this->model)) {
            $this->validationErrors[] = 'The car model must be specified';
        }

        if(empty($this->color)) {
            $this->validationErrors[] = 'The car color must be specified';
        }
    }

    public function getHorsepower()
    {
        return $this->horsepower;
    }

    public function getTotalTires()
    {
        return $this->totalTires;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public function setHorsepower($horsepower)
    {
        $this->horsepower = $horsepower;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function setTotalTires($totalTires)
    {
        $this->totalTires = $totalTires;
    }

    public function insert()
    {
        $insertParams = [
            'horsepower' => $this->horsepower,
            'total_tires' => $this->totalTires,
            'model' => $this->model,
            'color' => $this->color
        ];

        $query = '
            INSERT INTO vehicle
            (
                horsepower,
                total_tires,
                model,
                color
            )
            VALUES
            (
                :horsepower,
                :total_tires,
                :model,
                :color
            )
            ';

        $this->dbAdapter->query( $query )->execute( $insertParams );

        return $this->dbAdapter->getDriver()->getLastGeneratedValue();
    }
}