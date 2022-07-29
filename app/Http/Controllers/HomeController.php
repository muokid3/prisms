<?php

namespace App\Http\Controllers;

use App\AllocationList;
use App\Inbox;
use App\Sent;
use App\Site;
use App\SiteStudy;
use App\Stratum;
use App\Study;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use IU\PHPCap\RedCapProject;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function dashboard()
    {
        $userGroup = auth()->user()->user_group;

        switch ($userGroup) {
            case 1: //super admin
                $sites = Site::count();
                $invalid = Inbox::where('status',44)->count();
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
                    'invalid' => $invalid,
                    'studies' => $studies,
                    'siteStudies' => $siteStudies,
                    'sms_chart' => array_reverse($final),
                ]);
                break;
            case 2: //admin
                $sites = Site::count();
                $invalid = Inbox::where('status',44)->count();
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
                    'invalid' => $invalid,
                    'studies' => $studies,
                    'siteStudies' => $siteStudies,
                    'sms_chart' => array_reverse($final),
                ]);

                break;
            case 3: //site admin

                $site = Site::find(auth()->user()->site_id);
                $studies = SiteStudy::where('site_id',$site->id)->get();
                $allocations = AllocationList::where('site_id',$site->id)->get();

                $sourceIds = User::where('site_id',$site->id)->select('phone_no')->get();

                $finalSourceIds = [];

                foreach ($sourceIds as $sourceId){
                    array_push($finalSourceIds, ["phone_no"=>"+".$sourceId->phone_no]);
                }

//                Log::info(json_encode($sourceIds));
//                Log::info(json_encode($finalSourceIds));

                $smses = Inbox::groupBy('date')
                    ->select(DB::raw('Date(timestamp) as date'), DB::raw('count(*) as total'))
                    ->whereNotNull('timestamp')
                    ->whereIn('source', $finalSourceIds)
                    ->orderBy('date','desc')
                    ->limit(14)
                    ->get();

                Log::info(json_encode($smses));

                $final = [];

                foreach ($smses as $sms){
                    array_push($final, ["date"=>$sms->date, "inbox"=>$sms->total, "outbox"=>Sent::whereDate('delivery_time',$sms->date)->count()]);
                }


                return view('site_admin_dashboard')->with([
                    'site' => $site,
                    'allocations' => $allocations,
                    'studies' => $studies,
                    'final' => array_reverse($final),
                ]);
                break;
            default:
                abort(403,"You do not have permissions to access this resource. Please contact system admin");
        }


    }

    public function strata ($stduy_id){
        $strata = AllocationList::where('study_id',$stduy_id)->distinct()->get(['stratum_id']);

        return json_encode(Stratum::whereIn('id', $strata)->get());

    }

    public function search()
    {

        $redcapProject = new RedCapProject("https://searchtrial.kemri-wellcome.org/api/", "8E77FB323E730636E6204C516ECC74B3");

        $data = array(
            'type' => 'flat',
            'format' => 'csv'
        );

        $records = $redcapProject->exportRecordsAp($data);

        $fileName = 'search_participants.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        info(json_encode($records));

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="search_participants.csv"',
        ];
        return \Response::make($records, 200, $headers);

//        return $records;
//        return response()->stream($records, 200, $headers);


    }


}
