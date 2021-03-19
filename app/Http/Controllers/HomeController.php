<?php

namespace App\Http\Controllers;

use App\Inbox;
use App\Sent;
use App\Site;
use App\SiteStudy;
use App\Stratum;
use App\Study;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $sites = Site::count();
        $strata = Stratum::count();
        $studies = Study::count();
        $siteStudies = SiteStudy::count();

        $smses = Inbox::groupBy('date')
            ->select(DB::raw('Date(timestamp) as date'), DB::raw('count(*) as total'))
            ->whereNotNull('timestamp')
            ->orderBy('date','desc')
            ->limit(14)
            ->get();

        $final = [];

        foreach ($smses as $sms){
            array_push($final, ["date"=>$sms->date, "received"=>$sms->total, "processed"=>Sent::whereDate('timestamp',$sms->date)->count()]);
        }


        return view('dashboard')->with([
            'sites' => $sites,
            'strata' => $strata,
            'studies' => $studies,
            'siteStudies' => $siteStudies,
            'sms_chart' => array_reverse($final),
        ]);
    }
}
