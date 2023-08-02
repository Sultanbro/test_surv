<?php

namespace App\DTO\Coordinate;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CoordinateDTO
{
    public $country;
    public $city;
    public $geo_lat;
    public $geo_lon;

    public function __construct(array $data)
    {
        $this->validate($data);
        $this->country = $data['country'];
        $this->city = $data['city'];
        $this->geo_lat = $data['geo_lat'];
        $this->geo_lon = $data['geo_lon'];
    }

    private function validate(array $data)
    {
        $validator = Validator::make($data, [
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'geo_lat' => 'required|numeric|between:-90,90',
            'geo_lon' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}