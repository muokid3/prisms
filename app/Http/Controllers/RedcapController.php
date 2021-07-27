<?php

namespace App\Http\Controllers;

use App\AuditTrail;
use App\RedcapSite;
use App\SiteContact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

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

    public function redcap_hospital_details($_id)
    {
        $rs = RedcapSite::find($_id);

        if(is_null($rs))
            abort(404);

        //$siteContacts = SiteContact::where('redcap_site_id',$_id)->get();


        return view('redcap.hospital_details')->with([
            'rs' => $rs,
        ]);

    }
    public function hospitalContactsDT($_id) {

        $contacts = SiteContact::where('redcap_site_id',$_id)->get();

        return DataTables::of($contacts)
            ->addColumn('role',function ($contacts) {
                return optional($contacts->role)->name;
            })
            ->addColumn('actions', function($contacts) {
                $actions = '<div class="pull-right">';
                $actions .= '<form action="'. route('delete-hosp-contact',  $contacts->id) .'" style="display: inline; margin-left:10px" method="post" class="del_contact_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);

    }

    public function upload_hospital_contacts(Request  $request) {
        $data = request()->validate([
            'hospital_id' => 'required',
            'user_role' => 'required',
            'file' => 'required|file',
        ]);



        $file = $request->file('file');

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if(in_array(strtolower($extension),$valid_extension)){

            // Check file size
            if($fileSize <= $maxFileSize){

                // File upload location
                $location = 'public/uploads';

                // Upload file
                $file->move($location,$filename);

                // Import CSV to Database
                $filepath = public_path($location."/".$filename);

                // Reading file
                $file = fopen($filepath,"r");

                $importData_arr = array();
                $i = 0;

                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata );

                    if($i == 0){
                        $i++;
                        continue;
                    }
                    for ($c=0; $c < $num; $c++) {

//                        if($c == 0){
//                            $c++;
//                            continue;
//                        }
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);

//                dd($importData_arr);
                // Insert to MySQL database


                foreach($importData_arr as $importData){
                    $fullname = $importData[0];
                    $firstName = $importData[1];
                    $phoneNo = $importData[2];

                    if (substr($phoneNo, 0, 1) === '0') {
                        $phoneNo = "+254".ltrim($phoneNo, "0");
                    }

                    if (substr($phoneNo, 0, 1) === '7') {
                        $phoneNo = "+254".$phoneNo;
                    }

                    if (substr($phoneNo, 0, 1) === '2') {
                        $phoneNo = "+".$phoneNo;
                    }

                    $siteContact = new SiteContact();
                    $siteContact->redcap_site_id = $request->hospital_id;
                    $siteContact->user_group = $request->user_role;
                    $siteContact->contact_first_name = $firstName;
                    $siteContact->contact_full_name = $fullname;
                    $siteContact->contact_phone_no = $phoneNo;
                    $siteContact->saveOrFail();
                }

                $message = 'Contact list has been uploaded successfully.';
                Session::flash('success',$message);

                AuditTrail::create([
                    'created_by' => auth()->user()->id,
                    'action' => 'Uploaded contact list for Hospital: '
                        .' ('.optional(RedcapSite::find($request->hospital_id))->redcap_hospital_name.')',
                ]);

            }else{
                Session::flash('warning','File too large. File must be less than 2MB.');
            }

        }else{
            Session::flash('warning','Invalid File Extension. Only upload .csv files');
        }


        return redirect()->back();

    }

    public function delete_hosp_contact($id)
    {
        try {

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Deleted hospital contact #'.
                    $id.
                    '  ('.optional(SiteContact::find($id))->contact_full_name.
                    ' for '.
                    optional(optional(SiteContact::find($id))->hospital)->redcap_hospital_name.')',
            ]);

            SiteContact::destroy($id);

            request()->session()->flash('success', 'Hospital contact has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete contact because it\'s being used in the system!');
        }

        return redirect()->back();
    }



}
