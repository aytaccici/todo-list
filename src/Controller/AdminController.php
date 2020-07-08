<?php

namespace App\Controller;

use App\Service\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{


    /**
     * @Route("/admin", name="admin")
     */
    public function index(Calendar $calendar)
    {


        $calendar=$calendar->prepare();


        dump($calendar);
        foreach ($calendar as $c) {

            //dd($c);
        }

        return $this->render('admin/index.html.twig', [
            'calendar' => $calendar
        ]);

    }


}
