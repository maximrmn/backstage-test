<?php

namespace App\Services;

use App\Models\Container;
use Exception;
use Illuminate\Support\Facades\Log;

class ContainerService
{
    /**
     * Init small container
     *
     */
    public function initSmallContainer(): Container
    {
        return new Container(100, 100);
    }

    /**
     * Init big container
     *
     */
    public function initBigContainer(): Container
    {
        return new Container(300, 200);
    }

    /**
     * Check if the item fits inside a container
     * Implementation of a matrix structure to define the item and container space
     * If the container has another items, check if there is free space for the current item
     *
     */
    public function checkItemFits($item, $container): bool
    {
        try {
            for ($i = 0; $i + $item->width <= $container->width; $i++) {
                for ($j = 0; $j + $item->length <= $container->length; $j++) {
                    $fit = true;
                    for ($row = $i; $row < $i + $item->width; $row++) {
                        for ($col = $j; $col < $j + $item->length; $col++) {
                            if ($container->grid[$row][$col] != 0) {
                                $fit = false;
                                break 2;
                            }
                        }
                    }
                    if ($fit) {
                        for ($row = $i; $row < $i + $item->width; $row++) {
                            for ($col = $j; $col < $j + $item->length; $col++) {
                                $container->grid[$row][$col] = 1;
                            }
                        }
                        return true;
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return false;
    }

    /**
     * Check if the item dimensions are extra large for the big container
     *
     */
    public function itemExtraLarge($item, $container): bool
    {
        return $item->width > $container->width && $item->length > $container->length;
    }

    /**
     * The function to check the items for each transport and get the amount of containers needed
     *
     */
    function calculateContainersNeeded($items): array
    {
        // Containers initialisation
        $bigContainer = $this->initBigContainer();
        $smallContainer = $this->initSmallContainer();

        $containersUsed = [
            'bigContainers' => [],
            'smallContainers' => [],
            'unfitItems' => [],
        ];

        $containerIndex = 0;

        foreach ($items as $item) {
            // Check if the item fits in s small container
            $fitsSmall = $this->checkItemFits($item, $smallContainer);
            if ($fitsSmall) {
                $containersUsed['smallContainers'][] = $item;
            } else {
                // The container can have another items, init another empty small container and check again
                $smallContainer = $this->initSmallContainer();

                $fitsSmall = $this->checkItemFits($item, $smallContainer);
                if ($fitsSmall) {
                    $containersUsed['smallContainers'][] = $item;
                } else {
//                    Check if the item dimensions are too big for the big container
                    if ($this->itemExtraLarge($item, $bigContainer)) {
                        $containersUsed['unfitItems'][] = $item;
                    }

                    // If the item does not fi in a small container, check if it fits in a big container
                    $fitsBig = $this->checkItemFits($item, $bigContainer);
                    if ($fitsBig) {
                        $containersUsed['bigContainers'][$containerIndex][] = $item;
                    } else {
                        // The container can have another items, init another empty big container and check again
                        $bigContainer = $this->initBigContainer();
                        $containersUsed['bigContainers'][] = [];
                        $containerIndex++;

                        $fitsBig = $this->checkItemFits($item, $bigContainer);
                        if ($fitsBig) {
                            $containersUsed['bigContainers'][$containerIndex][] = $item;
                        }
                    }
                }
            }
        }

        // Optimize the items fit into the containers
        if (count($containersUsed['bigContainers']) > 0) {
            $containersUsed = $this->optimizeArrangement($containersUsed);
        }

//        echo json_encode($containersUsed);
//        return [];

        return $containersUsed;
    }

    /**
     * Optimize items arrangement in the containers
     * If there is a big container with free space in it, try to attach the items from the smaller containers
     *
     */
    public function optimizeArrangement($containersUsed): array
    {
        $optimizedContainersUsed = [
            'bigContainers' => [],
            'smallContainers' => [],
            'unfitItems' => $containersUsed['unfitItems'],
        ];

        foreach ($containersUsed['bigContainers'] as $bigIndex => $container) {
            $bigContainer = $this->initBigContainer();

            foreach ($container as $item) {
                // No need to check for fit, as the item is already in the container,
                // This step is made to set the occupied space in the big container
                $this->checkItemFits($item, $bigContainer);
                $optimizedContainersUsed['bigContainers'][$bigIndex][] = $item;
            }

            // Check if any small items fit in the big containers
            foreach ($containersUsed['smallContainers'] as $smallIndex => $item) {
                $fitsBig = $this->checkItemFits($item, $bigContainer);

                if ($fitsBig) {
                    // If the small item fits in the big container, remove it from the small container
                    $optimizedContainersUsed['bigContainers'][$bigIndex][] = $item;
                    unset($containersUsed['smallContainers'][$smallIndex]);
                } else {
                    break;
                }
            }
        }

        if (count($containersUsed['smallContainers']) > 0) {
            $optimizedContainersUsed['smallContainers'] = array_values($containersUsed['smallContainers']);
        }

        return $optimizedContainersUsed;
    }

}
