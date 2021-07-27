<?php

namespace App\Http\Controllers;

use App\AuditTrail;
use App\RedcapSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RedcapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function redcap_hospitals()
    {
        $hospitals = RedcapSite::all();

        return view('redcap.hospitals')->with([
            'hospitals' => $hospitals,
        ]);
    }
    public function new_redcap_hospital(Request $request)
    {
        $this->validate($request, [
            'redcap_hospital_id' => 'required|unique:redcap_sites,redcap_hospital_id',
            'redcap_hospital_name' => 'required',
        ]);


        $hospital = new RedcapSite();
        $hospital->redcap_hospital_id = $request->redcap_hospital_id;
        $hospital->redcap_hospital_name = $request->redcap_hospital_name;
        $hospital->saveOrFail();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Created new RedCap Site ('.$request->redcap_hospital_name.')',
        ]);


        Session::flash("success", "Hospital has been created");


        return redirect()->back();
    }
    public function get_redcap_hospital_details($id)
    {
        return RedcapSite::find($id);
    }
    public function update_redcap_hospital_details(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:redcap_sites,id',
            'redcap_hospital_id' => 'required|unique:redcap_sites,redcap_hospital_id,'.$request->id,
            'redcap_hospital_name' => 'required',
        ]);


        $hospital = RedcapSite::find($request->id);
        $hospital->redcap_hospital_id = $request->redcap_hospital_id;
        $hospital->redcap_hospital_name = $request->redcap_hospital_name;
        $hospital->update();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Updated redcap site #'.$request->id.' ('.$request->redcap_hospital_name.')',
        ]);

        Session::flash("success", "Site has been updated");


        return redirect()->back();
    }
    public function delete_redcap_hospital(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:redcap_sites,id',
        ]);

        try{
            $hospital = RedcapSite::find($request->id);

            DB::transaction(function() use ($hospital, $request) {
                AuditTrail::create([
                    'created_by' => auth()->user()->id,
                    'action' => 'Deleted RedCap site #'.$request->id.' ('.$hospital->redcap_hospital_name.')',
                ]);

                $hospital->delete();

                Session::flash("success", "Hospital has been deleted");

            });


        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete hospital because it has active users on the system');
        }


        return redirect()->back();
    }


    public function user_group_details($_id)
    {
        $ug = UserGroup::find($_id);

        if(is_null($ug))
            abort(404);

        $users = User::where('user_group',$_id)->get();

        $user_permissions = UserPermission::where('group_id',$_id)->get();



        return view('users.group_details')->with([
            'group' => $ug,
            'users' => $users,
            'user_permissions' => $user_permissions
        ]);

    }

}
