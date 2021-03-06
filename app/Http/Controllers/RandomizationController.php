<?php

namespace App\Http\Controllers;

use App\AllocationList;
use App\Inbox;
use App\SiteStudy;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RandomizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function randomization() {
        $allocation= AllocationList::all();

        return view('randomization.randomization')->with([
            'allocation' => $allocation,

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
            $studyIds = SiteStudy::where('study_coordinator', auth()->user()->id)->get('study_id');
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
        $sms= Inbox::all();

        return view('randomization.sms')->with([
            'sms' => $sms,

        ]);
    }
    public function smsDT() {
        $sms= Inbox::join('sent', 'inbox.id', '=', 'sent.message_id')
            ->select('inbox.id', 'inbox.text as incoming_text', 'sent.text as outgoing_text','inbox.timestamp as time_in','sent.timestamp as time_out')
            ->get();

        return DataTables::of($sms)

            ->addColumn('latency', function ($sms) {

                $from  = Carbon::createFromFormat('Y-m-d H:i:s', $sms->time_in);
                $to  = Carbon::createFromFormat('Y-m-d H:i:s', $sms->time_out);

                $diff_in_minutes = $to->diffInSeconds($from);
                return $diff_in_minutes." Secs. (".$to->diffForHumans($from).")";
            })

            ->make(true);

    }
}
