<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CisoEducationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = [
            [
                'title' => 'Applying CISSP Knowledge in Practice',
                'route' => 'cissp',
                'image_url' => 'CISSPLogo.png'
            ],
            [
                'title' => 'Applying CISM Knowledge in Practice',
                'route' => 'cism',
                'image_url' => 'CISMLogo.png'
            ],
            [
                'title' => 'Applying CGEIT Knowledge in Practice',
                'route' => 'cgeit',
                'image_url' => 'CGEITLogo.png'
            ],
            [
                'title' => 'Applying PMP Knowledge in Practice',
                'route' => 'pmp',
                'image_url' => 'PMPLogo.png'
            ],
            [
                'title' => 'Applying Agile Approach to Your Department',
                'route' => 'agile',
                'image_url' => 'AgileLogo.png'
            ]
        ];

        return view('ciso/ciso-education/index', compact('data'));
    }
}
