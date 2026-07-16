<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $productsData = collect([
            'Anti Phishing Software' => 'products.anti-phishing-software',
            'Anti Ransomware Software' => 'products.anti-ransomware-software',
            'Application Whitelisting' => 'products.application-whitelisting',
            'Backup Recovery' => 'products.backup-recovery',
            'Brand Protection' => 'products.brand-protection',
            'CASB' => 'products.casb',
            'Container and Kubernetes Security' => 'products.container-kubernetes-security',
            'Data Classification' => 'products.data-classification',
            'Data Loss Prevention' => 'products.data-loss-prevention',
            'Database Activity Monitoring' => 'products.database-activity-monitoring',
            'Distributed Denial-of-Service (DDoS) Attack' => 'products.distributed-denial-of-service-of-attack',
            'Email Security' => 'products.email-security',
            'Encryption' => 'products.encryption',
            'End-Point Detection and Response' => 'products.end-point-detection-response',
            'Extended Detection and Response' => 'products.extended-detection-protection-response',
            'Identity and Access Management' => 'products.identity-access-management',
            'IoT Security' => 'products.iot-security',
            'Multi Factor Authentication' => 'products.multi-factor-authentication',
            'Network Access Control' => 'products.network-access-control',
            'Next Generation Firewall' => 'products.next-generation-firewall',
            'Penetration Testing' => 'products.penetration-testing',
            'Privileged Access Management' => 'products.privilege-access-management',
            'SIEM Solution' => 'products.siem-solution',
            'Threat Intelligence' => 'products.threat-intelligence',
            'Unified Threat Management' => 'products.unified-threat-management',
            'User and Entity Behavior Analytics' => 'products.user-entity-behavior-analytics',
            'Web Application Firewall' => 'products.web-application-firewall',
            'WiFi Security' => 'products.wifi-security',
            'Zero Day Attack Protection' => 'products.zero-day-attack',
            'Zero Trust' => 'products.zero-trust',
        ])->map(function ($route, $title) {
            return (object) [
                'title' => $title,
                'route_name' => $route,
            ];
        })->values();

        return view('ciso/products/index', compact('productsData'));
    }
}
