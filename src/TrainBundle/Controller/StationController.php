<?php

namespace TrainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TrainBundle\Entity\Station;
use TrainBundle\Form\TripType;
use GuzzleHttp;

/**
 * @Route("/station")
 */
class StationController extends Controller
{

    /**
     * @Route("/search/station.json", name="search_station")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $client = new GuzzleHttp\Client();
            $query = $client->get('https://www.trainline.fr/api/v5_1/stations?context=search&q='.$request->get("q"), [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                    'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                ]
            ])->getBody();
            $res = json_decode($query->getContents(), true);

            $result = array();
            foreach ($res["stations"] as $k => $v) {
                foreach ($v as $i => $d) {
                    if ($i == "id") {
                        $result[$k][$i] = $d;
                    } elseif ($i == "name") {
                        $result[$k]["text"] = $d;
                    }
                }
            }
            return new JsonResponse($result);
        }
        return new JsonResponse('Request failed');

    }

}