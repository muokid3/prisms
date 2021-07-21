<?php

namespace App\Http\Controllers;

use App\AllocationList;
use App\Inbox;
use App\Sent;
use App\SiteStudy;
use App\Stratum;
use App\Study;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RandomizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function randomization() {

        if (auth()->user()->user_group == 1){

            //all studies
            $studies = Study::all();

            //overall randomization rate
            $rates = AllocationList::selectRaw('DATE(date_randomised) as date_randomised, count(*) as total')
                //->select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
                ->whereNotNull('date_randomised')
                ->whereNotNull('participant_id')
                ->orderBy('date_randomised', 'ASC')
                ->groupBy(DB::raw('Date(date_randomised)'))
                ->get();

            Log::info($rates);

        }else{

            //only studies that site is doing
            $studyIds = SiteStudy::where('site_id', auth()->user()->site_id)->pluck('study_id');
            $studies = Study::whereIn('id', $studyIds)->get();

            //randomization rate spefici to that site
            $rates = AllocationList::selectRaw('Date(date_randomised) as date_randomised, count(*) as total')
                //->select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
                ->whereNotNull('date_randomised')
                ->whereNotNull('participant_id')
                ->where('site_id', auth()->user()->site_id)
                ->orderBy('date_randomised', 'ASC')
                ->groupBy(DB::raw('Date(date_randomised)'))
                ->get();
        }




        //dd($allocations);

        $allocations = AllocationList::where('id',0)->get();

        return view('randomization.randomization')->with([
            'allocations' => $allocations,
            'study' => null,
            'stratum' => null,
            'studies' => $studies,
            'rates' => $rates,

        ]);
    }
    public function randomizationFiltered(Request $request) {

        $data = request()->validate([
            'study_id' => 'required',
            'stratum_id' => 'required',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
        ]);

        if (auth()->user()->user_group == 1){

            //all studies
            $studies = Study::all();

            //overall allocation rate
            if ($request->start_date !=null && $request->end_date !=null){
                $allocations = AllocationList::groupBy('allocation')
                    ->select('allocation', DB::raw('count(*) as total'))
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->whereNotNull('date_randomised')
                    ->whereDate('date_randomised', '>=', $request->start_date)
                    ->whereDate('date_randomised', '<=', $request->end_date)
                    ->whereNotNull('participant_id')
                    ->get();
            }else{
                $allocations = AllocationList::groupBy('allocation')
                    ->select('allocation', DB::raw('count(*) as total'))
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->whereNotNull('date_randomised')
                    ->whereNotNull('participant_id')
                    ->get();
            }

            //overall randomization rate
            if ($request->start_date !=null && $request->end_date !=null){
                $rates = AllocationList::select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
                    ->whereNotNull('date_randomised')
                    ->whereNotNull('participant_id')
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->whereDate('date_randomised', '>=', $request->start_date)
                    ->whereDate('date_randomised', '<=', $request->end_date)
                    ->groupBy('date_randomised')
                    ->orderBy('date_randomised', 'ASC')
                    ->get();
            }else{
                $rates = AllocationList::select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
                    ->whereNotNull('date_randomised')
                    ->whereNotNull('participant_id')
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->groupBy('date_randomised')
                    ->orderBy('date_randomised', 'ASC')
                    ->get();
            }


        }else{

            //only studies that site is doing
            $studyIds = SiteStudy::where('site_id', auth()->user()->site_id)->pluck('study_id');
            $studies = Study::whereIn('id', $studyIds)->get();

            //alocation rate as per site
            if ($request->start_date !=null && $request->end_date !=null){
                $allocations = AllocationList::whereIn('study_id', $studyIds)
                    ->groupBy('allocation')
                    ->select('allocation', DB::raw('count(*) as total'))
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->where('site_id', auth()->user()->site_id)
                    ->whereNotNull('date_randomised')
                    ->whereDate('date_randomised', '>=', $request->start_date)
                    ->whereDate('date_randomised', '<=', $request->end_date)
                    ->whereNotNull('participant_id')
                    ->get();
            }else{
                $allocations = AllocationList::whereIn('study_id', $studyIds)
                    ->groupBy('allocation')
                    ->select('allocation', DB::raw('count(*) as total'))
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->where('site_id', auth()->user()->site_id)
                    ->whereNotNull('date_randomised')
                    ->whereNotNull('participant_id')
                    ->get();
            }



            //randomization rate as per site
            if ($request->start_date !=null && $request->end_date !=null){
                $rates = AllocationList::select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
                    ->whereNotNull('date_randomised')
                    ->whereNotNull('participant_id')
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->where('site_id', auth()->user()->site_id)
                    ->whereDate('date_randomised', '>=', $request->start_date)
                    ->whereDate('date_randomised', '<=', $request->end_date)
                    ->groupBy('date_randomised')
                    ->orderBy('date_randomised', 'ASC')
                    ->get();
            }else{
                $rates = AllocationList::select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
                    ->whereNotNull('date_randomised')
                    ->whereNotNull('participant_id')
                    ->where('study_id',$request->study_id)
                    ->where('stratum_id',$request->stratum_id)
                    ->where('site_id', auth()->user()->site_id)
                    ->groupBy('date_randomised')
                    ->orderBy('date_randomised', 'ASC')
                    ->get();
            }


        }




        $study = Study::find($request->study_id);
        $stratum = Stratum::find($request->stratum_id);
//dd($allocations);
        return view('randomization.randomization')->with([
            'allocations' => $allocations,
            'studies' => $studies,
            'selectedStudy' => $study,
            'selectedStratum' => $stratum,
            'rates' => $rates,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,

        ]);
    }
    public function randomizationDT() {

        if (auth()->user()->user_group == 1){
            //show all
            $allocation= AllocationList::whereNotNull('date_randomised')
                ->whereNotNull('participant_id')
                ->get();
        }else{
            //show only mine
            //$studyIds = SiteStudy::where('study_coordinator', auth()->user()->id)->get('study_id');
            $studyIds = SiteStudy::where('site_id', auth()->user()->site_id)->get('study_id');

            $allocation= AllocationList::whereIn('study_id', $studyIds)
                ->whereNotNull('date_randomised')
                ->whereNotNull('participant_id')
                ->get();
        }


        return DataTables::of($allocation)
            ->addColumn('study', function ($allocation) {
                return optional($allocation->study)->study;
            })
            ->addColumn('staff',function ($allocation) {
                return optional($allocation->staff)->first_name.' '.optional($allocation->staff)->last_name;
            })
            ->addColumn('site',function ($allocation) {
                return optional($allocation->site)->site_name;
            })
            ->addColumn('stratum',function ($allocation) {
                return optional($allocation->stratum)->stratum;
            })
            ->editColumn('date_randomised', function($allocation) {
                return Carbon::parse($allocation->date_randomised)->isoFormat('MMMM Do YYYY');
            })
//
//            ->addColumn('actions', function($study) {
//                $actions = '<div class="pull-right">';
////                $actions = '<div class="pull-right">
////                        <button source="' . route('edit-user' ,  $user->id) . '"
////                    class="btn btn-warning btn-sm edit-user-btn" acs-id="'.$user->id .'">
////                    <i class="fa fa-edit">edit</i> Edit</button>';
//                $actions .= '<form action="'. route('delete-study',  $study->id) .'" style="display: inline; margin-left:10px" method="post" class="del_study_form">';
//                $actions .= method_field('DELETE');
//                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
//                $actions .= '</div>';
//                return $actions;
//            })
//            ->rawColumns(['actions'])
            ->make(true);

    }

    public function sms() {


        $smses = Inbox::groupBy('date')
            ->select(DB::raw('Date(timestamp) as date'), DB::raw('count(*) as total'))
            ->whereNotNull('timestamp')
            ->orderBy('date','desc')
            ->limit(14)
            ->get();

        $final = [];

        foreach ($smses as $sms){
            array_push($final, ["date"=>$sms->date, "inbox"=>$sms->total, "outbox"=>Sent::whereDate('delivery_time',$sms->date)->count()]);
        }

        return view('randomization.sms')->with([
            'final' => array_reverse($final),
        ]);
    }
    public function smsFiltered(Request $request) {

        $data = request()->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $smses = Inbox::groupBy('date')
            ->select(DB::raw('Date(timestamp) as date'), DB::raw('count(*) as total'))
            ->whereNotNull('timestamp')
            ->whereDate('timestamp', '>=', $request->start_date)
            ->whereDate('timestamp', '<=', $request->end_date)
            ->orderBy('date','desc')
            ->get();

        $final = [];

        foreach ($smses as $sms){
            array_push($final, ["date"=>$sms->date, "inbox"=>$sms->total, "outbox"=>Sent::whereDate('delivery_time',$sms->date)->count()]);
        }

        return view('randomization.sms')->with([
            'final' => array_reverse($final),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
    }
    public function smsDT() {
        $sms= Inbox::join('sent', 'inbox.id', '=', 'sent.message_id')
            ->whereNotNull('sent.delivery_time')
            ->select('inbox.id', 'inbox.text as incoming_text', 'sent.text as outgoing_text','inbox.timestamp as time_in','sent.delivery_time as time_out')
            ->get();

        return DataTables::of($sms)

            ->addColumn('actual_latency', function ($sms) {

                $from  = Carbon::createFromFormat('Y-m-d H:i:s', $sms->time_in);
                $to  = Carbon::createFromFormat('Y-m-d H:i:s', $sms->time_out);

                return  $to->diffInSeconds($from);
            })

            ->addColumn('latency', function ($sms) {

                $from  = Carbon::createFromFormat('Y-m-d H:i:s', $sms->time_in);
                $to  = Carbon::createFromFormat('Y-m-d H:i:s', $sms->time_out);

                $diff_in_minutes = $to->diffInSeconds($from);
                return $diff_in_minutes." Secs. (".$to->diffForHumans($from).")";
            })

            ->make(true);

    }
}
