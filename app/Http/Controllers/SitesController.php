<?php

namespace App\Http\Controllers;

use App\Site;
use App\SiteStudy;
use App\Study;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SitesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function studies() {
        $sites = Site::all();

        return view('sites.studies')->with([
            'sites' => $sites,

        ]);
    }
    public function studiesDT() {
        $studies = Study::all();

        return DataTables::of($studies)
            ->addColumn('site', function ($study) {
                return optional($study->site)->site_name;
            })
            ->addColumn('coordinator',function ($study) {
                return optional($study->coordinator)->first_name.' '.optional($study->coordinator)->last_name;
            })
            ->editColumn('created_at', function($study) {
                return Carbon::parse($study->created_at)->isoFormat('MMMM Do YYYY');
            })

            ->addColumn('actions', function($study) {
                $actions = '<div class="pull-right">';
//                $actions = '<div class="pull-right">
//                        <button source="' . route('edit-user' ,  $user->id) . '"
//                    class="btn btn-warning btn-sm edit-user-btn" acs-id="'.$user->id .'">
//                    <i class="fa fa-edit">edit</i> Edit</button>';
                $actions .= '<form action="'. route('delete-study',  $study->id) .'" style="display: inline; margin-left:10px" method="post" class="del_study_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);

    }
    public function delete_study($id)
    {
        try {
            Study::destroy($id);

            request()->session()->flash('success', 'Study has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete study because it\'s being used in the system!');
        }

        return redirect()->back();
    }


    public function site_studies() {
        $sites = SiteStudy::all();

        return view('sites.site_studies')->with([
            'sites' => $sites,

        ]);
    }
    public function site_studiesDT() {
        $studies = SiteStudy::all();

        return DataTables::of($studies)
            ->addColumn('site', function ($study) {
                return optional($study->site)->site_name;
            })

            ->addColumn('study', function ($study) {
                return optional($study->study)->study;
            })
            ->addColumn('study_detail', function ($study) {
                return optional($study->study)->study_detail;
            })
            ->addColumn('coordinator',function ($study) {
                return optional($study->coordinator)->first_name.' '.optional($study->coordinator)->last_name;
            })
            ->editColumn('date_initiated', function($study) {
                return Carbon::parse($study->date_initiated)->isoFormat('MMMM Do YYYY');
            })

            ->addColumn('actions', function($study) {
                $actions = '<div class="pull-right">';
//                $actions = '<div class="pull-right">
//                        <button source="' . route('edit-user' ,  $user->id) . '"
//                    class="btn btn-warning btn-sm edit-user-btn" acs-id="'.$user->id .'">
//                    <i class="fa fa-edit">edit</i> Edit</button>';
                $actions .= '<form action="'. route('delete-site-study',  $study->id) .'" style="display: inline; margin-left:10px" method="post" class="del_study_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);

    }
    public function delete_site_study($id)
    {
        try {
            SiteStudy::destroy($id);

            request()->session()->flash('success', 'Site study has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete site study because it\'s being used in the system!');
        }

        return redirect()->back();
    }


    public function sites() {
        $sites = Site::all();

        return view('sites.sites')->with([
            'sites' => $sites,

        ]);
    }
    public function sitesDT() {
        $sites = Site::all();

        return DataTables::of($sites)

            ->addColumn('studies',function ($site) {
                return $site->studies->count();
            })
            ->editColumn('created_at', function($site) {
                return Carbon::parse($site->created_at)->isoFormat('MMMM Do YYYY');
            })

            ->addColumn('actions', function($study) {
                $actions = '<div class="pull-right">';
//                $actions = '<div class="pull-right">
//                        <button source="' . route('edit-user' ,  $user->id) . '"
//                    class="btn btn-warning btn-sm edit-user-btn" acs-id="'.$user->id .'">
//                    <i class="fa fa-edit">edit</i> Edit</button>';
                $actions .= '<form action="'. route('delete-site',  $study->id) .'" style="display: inline; margin-left:10px" method="post" class="del_site_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);

    }
    public function delete_site($id)
    {
        try {
            Site::destroy($id);

            request()->session()->flash('success', 'Site has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete site because it\'s being used in the system!');
        }

        return redirect()->back();
    }

}
