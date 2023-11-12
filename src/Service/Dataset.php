<?php

namespace App\Service;

use App\Entity\FieldConfiguration;

class Dataset
{

    const DATASET_ABALONE = 'DATASET_ABALONE';
    const DATASET_WEATHER = 'DATASET_WEATHER';
    const DATASET_IRIS = 'DATASET_IRIS';
    const DATASET_SEEDS = 'DATASET_SEEDS';
    const DATASET_WINE = 'DATASET_WINE';


    public static function isValidDataset(string $dataset): bool
    {
        return in_array($dataset, [self::DATASET_WINE, self::DATASET_IRIS, self::DATASET_SEEDS, self::DATASET_ABALONE, self::DATASET_WEATHER]);
    }

    public static function getLocalizationKey(string $dataset): string
    {
        switch ($dataset) {
            case self::DATASET_ABALONE:
                return "datasets.type.abalone";
            case self::DATASET_WEATHER:
                return "datasets.type.weather";
            case self::DATASET_IRIS:
                return "datasets.type.iris";
            case self::DATASET_SEEDS:
                return "datasets.type.seeds";
            case self::DATASET_WINE:
                return "datasets.type.wine";
        }
        return '';
    }

    public static function getFilename(string $dataset): string
    {
        switch ($dataset) {
            case self::DATASET_ABALONE:
                return "abalone_regression.csv";
            case self::DATASET_WEATHER:
                return "arkona_weather.csv";
            case self::DATASET_IRIS:
                return "iris_flowers_classification.csv";
            case self::DATASET_SEEDS:
                return "seeds_classification.csv";
            case self::DATASET_WINE:
                return "wine_quality_regression.csv";
        }
        throw new \Exception("Invalid Dataset");
    }

    /**
     * @param string $dataset
     * @return FieldConfiguration[]
     */
    public function getDefaultConfigurations(string $dataset): array
    {
        switch ($dataset) {
            case self::DATASET_ABALONE:
                return $this->getAbaloneConfiguration();
            case self::DATASET_WEATHER:
                return $this->getWeatherConfiguration();
            case self::DATASET_IRIS:
                return $this->getIrisConfiguration();
            case self::DATASET_SEEDS:
                return $this->getSeedsConfiguration();
            case self::DATASET_WINE:
                return $this->getWineConfiguration();
        }
        return [];
    }

    private function getAbaloneConfiguration(): array
    {
        $fields = [];

        $field = new FieldConfiguration();
        $field->setName('sex')
            ->setType('text')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('length')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('diameter')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('height')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('whole_weight')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('shucked_weight')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('viscera_weight')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('shell_weight')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('rings')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(true);
        $fields[] = $field;

        return $fields;
    }

    private function getWeatherConfiguration(): array
    {
        $fields = [];

        $field = new FieldConfiguration();
        $field->setName('date_time')
            ->setType('text')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('season')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('year')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('month')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('day')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('hour')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('hour_sin')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('hour_cos')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('prev_temp')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('prev_hum')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('temp')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('humidity')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('coverage_class')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('coverage')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('coverage_percent')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('range_visibility')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('wind_direction')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('wind_speed_bft')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('wind_speed_metric')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('precipitation')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(true);
        $fields[] = $field;

        return $fields;
    }

    private function getIrisConfiguration(): array
    {
        $fields = [];

        $field = new FieldConfiguration();
        $field->setName('sepal_length')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('sepal_width')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('petal_length')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('petal_width')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('class')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(true);
        $fields[] = $field;

        return $fields;
    }

    private function getSeedsConfiguration(): array
    {
        $fields = [];

        $field = new FieldConfiguration();
        $field->setName('area')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('perimeter')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('length_of_kernel')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('width_of_kernel')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('asymmetry_coeffcicient')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('length_of_kernel_grove')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('class')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(true);
        $fields[] = $field;

        return $fields;
    }

    private function getWineConfiguration(): array
    {
        $fields = [];

        $field = new FieldConfiguration();
        $field->setName('fixed_acidity')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('volatile_acidity')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('citric_acid')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('residual_sugar')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('chlorides')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('free_sulfur_dioxide')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('total_sulfur_dioxide')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('density')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('pH')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('sulphates')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('alcohol')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(false);
        $fields[] = $field;

        $field = new FieldConfiguration();
        $field->setName('quality')
            ->setType('number')
            ->setIsIgnored(false)
            ->setIsTarget(true);
        $fields[] = $field;

        return $fields;
    }

}