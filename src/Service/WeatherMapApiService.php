<?php
namespace App\Service;

use App\Entity\Weather;
use App\Repository\WeatherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherMapApiService
{
    private $weatherMapApiKey;
    private $httpClient;
    private $weatherRepository;
    private $entityManager;
    private $weatherMapApiUrl;

    public function __construct(HttpClientInterface $httpClient, string $weatherMapApiKey, string $weatherMapApiUrl, WeatherRepository $weatherRepository, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->weatherMapApiKey = $weatherMapApiKey;
        $this->weatherRepository = $weatherRepository;
        $this->entityManager=$entityManager;
        $this->weatherMapApiUrl= $weatherMapApiUrl;
    }


    public function getWeatherMapApiKey(): string
    {
        return $this->weatherMapApiKey;
    }
    public function fetchAndStoreWeatherByCity(string $city): Weather
    {
        $response = $this->httpClient->request('GET', $this->weatherMapApiUrl, [
            'query' => [
                'q' => $city,
                'appid' => $this->weatherMapApiKey,
                'units' => 'metric'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error fetching weather data.');
        }

        $data = $response->toArray();

        $weather= $this->prepareWeather($data);

        return $weather;
    }
    public function fetchAndStoreWeather(float $lat, float $lon): Weather
    {
        $response = $this->httpClient->request('GET', $this->weatherMapApiUrl, [
            'query' => [
                'lat' => $lat,
                'lon'=> $lon,
                'appid' => $this->weatherMapApiKey,
                'units' => 'metric'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error fetching weather data.');
        }

        $data = $response->toArray();

        $weather = $this->prepareWeather($data);
        return $weather;
    }

    // Prepare or set the Weather data for save
    public function prepareWeather($data){

        $weather = new Weather();
        $weather->setLat($data['coord']['lat']);
        $weather->setLon($data['coord']['lon']);
        $weather->setCity($data['name']);
        $weather->setTemperature($data['main']['temp']);
        $weather->setDescription($data['weather'][0]['description']);
        $weather->setFetchedAt(new \DateTime());

        $this->weatherRepository->save($weather);

        return $weather;
    }

}
