<?php

namespace App\Controller;

use App\Form\WeatherSearchType;
use App\Repository\WeatherRepository;
use App\Service\WeatherMapApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WeatherMapApiController extends AbstractController
{

    private $weatherMapService;
    private $weatherRepository;
    private $serializer;

    public function __construct(WeatherMapApiService $weatherMapService, WeatherRepository $weatherRepository, SerializerInterface $serializer)
    {
        $this->weatherMapService = $weatherMapService;
        $this->weatherRepository= $weatherRepository;
        $this->serializer= $serializer;
    }

    /**
     * Description: Fetch By Latititue and Longitute or City
     * RequestBody={'lat':'','lon':''}
     */
    #[Route('/weather', name: 'app_weather_map_service')]
    public function index(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $city= array_key_exists('city',$data) ? $data['city'] :null;
            $lat= array_key_exists('lat',$data) ? $data['lat'] : null;
            $lon= array_key_exists('lon',$data) ? $data['lon'] : null;

            if(!empty($lat) && !empty($lon)){

                $weather = $this->weatherMapService->fetchAndStoreWeather($lat,$lon);

            }elseif(!empty($city)){

                $weather = $this->weatherMapService->fetchAndStoreWeatherByCity($city);

            }else{
                return new JsonResponse('Error:' .'Required fields Lat and Lon or city',500);
            }
        } catch (\Exception $e) {
            return new JsonResponse('Error: ' . $e->getMessage(), 500);
        }

        return $this->json([
            'city' => $weather->getCity(),
            'lat'=>$weather->getLat(),
            'lon'=>$weather->getLon(),
            'temperature' => $weather->getTemperature(),
            'description' => $weather->getDescription(),
            'fetchedAt' => $weather->getFetchedAt()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * @Route("/weather/search", name="weather_search")
     */

     #[Route('/weather/search', name: 'app_weather_search')]
     public function search(Request $request): JsonResponse
    {
        $form = $this->createForm(WeatherSearchType::class);
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            throw new BadRequestHttpException('Invalid JSON.');
        }

        // $form->handleRequest($request);
        $form->submit($data);

        $weatherData = [];

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $criteria=[
                'city'=>$data->getCity(),
                'fetchedAt'=>$data->getFetchedAt()
            ];
            
            $weatherData=$this->weatherRepository->fetchWeatherBy($criteria);
            $response=$this->serializer->serialize($weatherData,'json');
            return JsonResponse::fromJsonString($response);
            
        }

        // return $this->render('weather/search.html.twig', [
        //     'form' => $form->createView(),
        //     'weatherData' => $weatherData,
        // ]);

        return $this->json($weatherData);
    }

}
