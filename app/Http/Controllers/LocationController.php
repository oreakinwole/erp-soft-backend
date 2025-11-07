<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all Nigerian states
     */
    public function getStates()
    {
        $states = [
            'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
            'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT - Abuja', 'Gombe',
            'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara',
            'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau',
            'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
        ];

        return response()->json([
            'success' => true,
            'data' => array_map(function($state) {
                return [
                    'value' => strtolower(preg_replace('/[\s\-]+/', '_', $state)),
                    'label' => $state
                ];
            }, $states)
        ]);
    }

    /**
     * Get LGAs for a specific state
     */
    public function getLGAs(Request $request)
    {
        $state = $request->query('state');
        
        // Sample LGAs for Lagos (you can expand this for other states)
        $lgas = [
            'lagos' => [
                'Agege', 'Ajeromi-Ifelodun', 'Alimosho', 'Amuwo-Odofin', 'Apapa', 'Badagry',
                'Epe', 'Eti-Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye', 'Ikeja', 'Ikorodu',
                'Kosofe', 'Lagos Island', 'Lagos Mainland', 'Mushin', 'Ojo', 'Oshodi-Isolo',
                'Shomolu', 'Surulere'
            ],
            'fct_abuja' => [
                'Abaji', 'Bwari', 'Gwagwalada', 'Kuje', 'Kwali', 'Municipal Area Council'
            ],
            // Add more states and their LGAs as needed
        ];

        $stateLGAs = $lgas[$state] ?? [];

        return response()->json([
            'success' => true,
            'data' => array_map(function($lga) {
                return [
                    'value' => strtolower(preg_replace('/[\s\-]+/', '_', $lga)),
                    'label' => $lga
                ];
            }, $stateLGAs)
        ]);
    }
}