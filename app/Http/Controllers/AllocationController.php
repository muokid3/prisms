<?php

namespace App\Http\Controllers;

use App\AllocationList;
use App\AuditTrail;
use App\Site;
use App\Stratum;
use App\Study;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class AllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function strata() {
        $strata = Stratum::all();

        return view('allocations.strata')->with([
            'strata' => $strata,

        ]);
    }
    public function strataDT() {
        $strata = Stratum::all();

        return DataTables::of($strata)

            ->editColumn('created_at', function($strata) {
                return Carbon::parse($strata->created_at)->isoFormat('MMMM Do YYYY');
            })

            ->addColumn('actions', function($strata) {
                $actions = '<div class="pull-right">';
                $actions = '<div class="pull-right">
                        <button source="' . route('edit-strata' ,  $strata->id) . '"
                    class="btn btn-warning btn-sm edit-stratum-btn" acs-id="'.$strata->id .'">
                    <i class="fa fa-edit">edit</i> Edit</button>';
                $actions .= '<form action="'. route('delete-strata',  $strata->id) .'" style="display: inline; margin-left:10px" method="post" class="del_stratum_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);

    }
    public function edit_strata($id)
    {
        $stratum = Stratum::find($id);
        return $stratum;
    }
    public function update_strata(Request $request)
    {

        $data = request()->validate([
            'stratum' => 'required|max:255|unique:strata,stratum,'.$request->id,
            'id' => 'required',
        ]);



        $stratum = Stratum::find($request->id);
        $stratum->stratum = $request->stratum;
        $stratum->update();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Edited stratum #'.$stratum->id.' to "'.$request->stratum.'"',
        ]);

        request()->session()->flash('success', 'Stratum has been updated.');
        return redirect()->back();
    }
    public function delete_strata($id)
    {
        try {

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Deleted stratum #'.$id.' ('.Stratum::find($id)->stratum.')',
            ]);

            Stratum::destroy($id);

            request()->session()->flash('success', 'Stratum has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete stratum because it\'s being used in the system!');
        }

        return redirect()->back();
    }
    public  function create_strata(Request  $request){
        $data = request()->validate([
            'stratum' => 'required',
        ]);

        $exists = Stratum::where('stratum', $request->stratum)->first();

        if (is_null($exists)){
            $stratum = new Stratum();
            $stratum->stratum = $request->stratum;
            $stratum->saveOrFail();

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Created new stratum #'.$stratum->id.' ('.$request->stratum.')',
            ]);
            request()->session()->flash('success', 'Stratum has been created successfully');

        }else{
            request()->session()->flash('warning', 'A stratum with a similar name already exists');
        }

        return redirect()->back();
    }

    public function upload_list() {
        $strata = Stratum::all();
        $studies = Study::all();
        $sites = Site::all();

        return view('allocations.allocation_list')->with([
            'strata' => $strata,
            'studies' => $studies,
            'sites' => $sites,

        ]);
    }
    public function upload(Request  $request) {
        $data = request()->validate([
            'stratum' => 'nullable|max:10',
            'study' => 'required|max:10',
            'site' => 'required|max:10',
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

                info(json_encode($importData_arr));
                // Insert to MySQL database
                $duplicates = 0;

                foreach($importData_arr as $importData){
                    $sequence = $importData[0];
                    //$allocation = $importData[1];

                    $exists = AllocationList::where('study_id',$request->study)
                        ->where('site_id',$request->site)
                        ->where('stratum_id',$request->stratum)
                        ->where('sequence',$sequence)
                        ->first();

                    if (!is_null($exists)){
                        $duplicates += 1;
                    }
                }

                if ($duplicates == 0){
                    foreach($importData_arr as $importData){
                        $sequence = $importData[0];
                        $allocation = $importData[1];

                        $allocList = new AllocationList();
                        $allocList->sequence = $sequence;
                        $allocList->study_id = $request->study;
                        $allocList->site_id = $request->site;
                        $allocList->stratum_id = $request->stratum == null ? 1 : $request->stratum;
                        $allocList->allocation = $allocation;
                        $allocList->saveOrFail();
                    }

                    $message = 'Allocation list has been uploaded successfully.';
                    Session::flash('success',$message);

                    AuditTrail::create([
                        'created_by' => auth()->user()->id,
                        'action' => 'Uploaded allocation list for study #'
                            .$request->study
                            .' ('.optional(Study::find($request->study))->study.')'
                            .' at site: '
                            .optional(Site::find($request->site))->site_name
                            .' for stratum '
                            .optional(Stratum::find($request->stratum))->stratum,
                    ]);
                }else{
                    $message = 'Duplicate allocation entries have been detected and have been skipped';
                    Session::flash('warning',$message);
                }

            }else{
                Session::flash('warning','File too large. File must be less than 2MB.');
            }

        }else{
            Session::flash('warning','Invalid File Extension. Only upload .csv files');
        }


        return redirect()->back();

    }

}
