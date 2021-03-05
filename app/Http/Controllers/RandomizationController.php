<?php

namespace App\Http\Controllers;

use App\AllocationList;
use App\Inbox;
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
        $allocation= AllocationList::all();

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
    public function delete_randomization($id)
    {
        try {
            Study::destroy($id);

            request()->session()->flash('success', 'Study has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete study because it\'s being used in the system!');
        }

        return redirect()->back();
    }

    public function sms() {
        $sms= Inbox::all();

        return view('randomization.sms')->with([
            'sms' => $sms,

        ]);
    }
    public function smsDT() {
        $sms= Inbox::all();

        return DataTables::of($sms)

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
}
