<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Services\ContainerService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class Controller extends BaseController
{
    public const CIRCULAR = 0;
    public const RECTANGULAR = 1;

    protected $containerService;

    /**
     * Init controller and ContainerService for calculation functions
     *
     */
    public function __construct(ContainerService $containerService)
    {
        $this->containerService = $containerService;
    }

    /**
     * Main function, transport declaration and items assignment
     * Calculate how many containers of each type we need for a transport
     *
     * @return View with the calculated data
     *
     */
    public function containerSurfaceCalculation(): View
    {
        $transports = [];

        $transport1 = new Transport();
        $transport1->name = "Transport 1";
        $transport1->addCircularContainer(50);

        $transport1->addCircularContainer(50);
        $transport1->addRectangularContainer(100, 100);
        $transports[] = $transport1;

        $transport2 = new Transport();
        $transport2->name = "Transport 2";
        $transport2->addRectangularContainer(400, 400);
        $transport2->addCircularContainer(100);
        $transports[] = $transport2;

        $transport3 = new Transport();
        $transport3->name = "Transport 3";
        $transport3->addRectangularContainer(100, 150);
        $transport3->addRectangularContainer(50, 50);
        $transport3->addCircularContainer(50);
        $transports[] = $transport3;

        foreach ($transports as $transport) {
            $transport->assignment = $this->containerService->calculateContainersNeeded($transport->containers);
        }

        return view('task')->with(compact('transports'));
    }
}
