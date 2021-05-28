<?php

namespace App\Http\Controllers\API;

use App\AllocationList;
use App\AuditTrail;
use App\Http\Controllers\Controller;
use App\Http\Resources\GenericCollection;
use App\Inbox;
use App\Sent;
use App\Site;
use App\SiteStudy;
use App\Stratum;
use App\Study;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ApiController extends Controller
{
    //sites
    public function create_site(Request  $request)
    {
        $data = request()->validate([
            'site_name'  => 'required',
        ]);

        $exists = Site::where('site_name', $request->site_name)->first();

        if (is_null($exists)){
            $site = new Site();
            $site->site_name = $request->site_name;
            $site->saveOrFail();

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Via APP - Created new site #'.$site->id.' ('.$request->site_name.')',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site has been created successfully'
            ], 200);

        }else{

            return response()->json([
                'success' => false,
                'message' => 'A site with a similar name already exists'
            ], 200);
        }

    }
    public function get_sites()
    {
        return new GenericCollection(Site::orderBy('id', 'desc')->get());
    }
    public function update_site(Request $request)
    {

        $data = request()->validate([
            'site_name' => 'required|max:255|unique:sites,site_name,'.$request->id,
            'id' => 'required|exists:sites,id',
        ]);



        $site = Site::find($request->id);
        $site->site_name = $request->site_name;
        $site->update();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Via APP - Edited site #'.$site->id.' to "'.$request->site_name.'"',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Site has been updated successfully'
        ], 200);
    }
    public function delete_site($id)
    {
        try {

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Via APP - Deleted site #'.$id.' ('.Site::find($id)->site_name.')',
            ]);
            Site::destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Site has been deleted.'
            ], 200);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Could not delete site because it\'s being used in the system!'
            ], 200);
        }

    }
    //end of sites

    //studies
    public function create_study(Request  $request)
    {
        $data = request()->validate([
            'name'  => 'required',
            'detail'  => 'required',
        ]);

        $exists = Study::where('study', $request->name)->first();

        if (is_null($exists)){
            $study = new Study();
            $study->study = $request->name;
            $study->study_detail = $request->detail;
            $study->saveOrFail();

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Via APP - Created new study ('.$request->name.')',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Study has been created successfully'
            ], 200);

        }else{

            return response()->json([
                'success' => false,
                'message' => 'A study with a similar name already exists'
            ], 200);
        }
    }
    public function get_studies()
    {
        return new GenericCollection(Study::orderBy('id', 'desc')->get());
    }
    public function update_study(Request $request)
    {

        $data = request()->validate([
            'name' => 'required|max:255|unique:studies,study,'.$request->id,
            'detail'  => 'required',
            'id' => 'required|exists:studies,id',
        ]);



        $study = Study::find($request->id);
        $study->study = $request->name;
        $study->study_detail = $request->detail;
        $study->update();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Via APP - Edited study #'.$study->id.' to "'.$request->name.'"',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Study has been updated successfully'
        ], 200);
    }
    public function delete_study($id)
    {
        try {

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Via APP - Deleted study #'.$id.' ('.Study::find($id)->study.')',
            ]);

            Study::destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Study has been deleted.'
            ], 200);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Could not delete study because it\'s being used in the system!'
            ], 200);
        }

    }

    public function get_my_studies()
    {
        if (auth()->user()->user_group == 1){
            $siteStudies = SiteStudy::orderBy('id', 'desc')->get();

        }else {
            $siteStudies = SiteStudy::where('study_coordinator', auth()->user()->id)->orderBy('id', 'desc')->get();
        }

        return new GenericCollection($siteStudies);
    }

    //end of studies


    //strata
    public function create_stratum(Request  $request)
    {
        $data = request()->validate([
            'stratum'  => 'required',
        ]);

        $exists = Stratum::where('stratum', $request->stratum)->first();

        if (is_null($exists)){
            $stratum = new Stratum();
            $stratum->stratum = $request->stratum;
            $stratum->saveOrFail();

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Via APP - Created new stratum #'.$stratum->id.' ('.$request->stratum.')',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stratum has been created successfully'
            ], 200);

        }else{

            return response()->json([
                'success' => false,
                'message' => 'A stratum with a similar name already exists'
            ], 200);
        }

    }
    public function get_strata()
    {
        return new GenericCollection(Stratum::orderBy('id', 'desc')->get());
    }
    public function update_stratum(Request $request)
    {

        $data = request()->validate([
            'stratum' => 'required|max:255|unique:strata,stratum,'.$request->id,
            'id' => 'required|exists:strata,id',
        ]);



        $stratum = Stratum::find($request->id);
        $stratum->stratum = $request->stratum;
        $stratum->update();

        AuditTrail::create([
            'created_by' => auth()->user()->id,
            'action' => 'Via APP - Edited stratum #'.$stratum->id.' to "'.$request->stratum.'"',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stratum has been updated successfully'
        ], 200);
    }
    public function delete_stratum($id)
    {
        if (is_null(AllocationList::where('stratum_id',$id)->first())){
            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Via APP - Deleted stratum #'.$id.' ('.Stratum::find($id)->stratum.')',
            ]);

            Stratum::destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Stratum has been deleted.'
            ], 200);

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Could not delete stratum because it\'s being used in the system!'
            ], 200);
        }


    }
    //end of strata


    //allocation list
    public function upload_allocation_list(Request  $request)
    {
        $data = request()->validate([
            'site_id'  => 'required',
            'study_id'  => 'required',
            'stratum_id'  => 'required',
            'uploaded_file'  => 'required',
        ]);

        $success = true;

        $file = $request->file('uploaded_file');

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        Log::info("filename::".$filename);
        Log::info("extension::".$extension);
        Log::info("fileSize::".$fileSize);

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


                Log::info("Array size:::".sizeof($importData_arr));
//                dd($importData_arr);
                // Insert to MySQL database

                $duplicates = 0;

                foreach($importData_arr as $importData){

                    $sequence = $importData[0];
                    $allocation = $importData[1];

                    $exists = AllocationList::where('study_id',$request->study)
                        ->where('site_id',$request->site)
                        ->where('stratum_id',$request->stratum)
                        ->where('allocation',$allocation)
                        ->first();

                    if (is_null($exists)){
                        $allocList = new AllocationList();
                        $allocList->sequence = $sequence;
                        $allocList->study_id = $request->study_id;
                        $allocList->site_id = $request->site_id;
                        $allocList->stratum_id = $request->stratum_id;
                        $allocList->allocation = $allocation;
                        $allocList->saveOrFail();
                    }else{
                        $duplicates += 1;
                    }
                }

                if ($duplicates == 0)
                    $message = 'Allocation list has been uploaded successfully.';
                else
                    $message = 'Allocation list has been uploaded successfully. Duplicates have been skipped';

                AuditTrail::create([
                    'created_by' => auth()->user()->id,
                    'action' => 'Via App - Uploaded allocation list for study #'
                        .$request->study
                        .' ('.optional(Study::find($request->study_id))->study.')'
                        .' at site: '
                        .optional(Site::find($request->site_id))->site_name
                        .' for stratum '
                        .optional(Stratum::find($request->stratum_id))->stratum,
                ]);

            }else{
                $success = false;
                $message = "File too large. File must be less than 2MB.";
            }

        }else{
            $success = false;
            $message = "Invalid File Extension. Only upload .csv files";
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ], 200);
    }
    //end of allocation list


    //sms
    public function get_sms()
    {
        return new GenericCollection(Inbox::orderBy('id', 'desc')->paginate(15));
    }
    public function get_sms_graph()
    {
        $smses = Inbox::groupBy('date')
            ->select(DB::raw('Date(timestamp) as date'), DB::raw('count(*) as total'))
            ->whereNotNull('timestamp')
            ->orderBy('date','desc')
            ->limit(14)
            ->get();

        $final = [];

        foreach ($smses as $sms){
            array_push($final, ["date"=>Carbon::parse($sms->date)->isoFormat('MMM Do YY'), "inbox"=>$sms->total, "outbox"=>Sent::whereDate('delivery_time',$sms->date)->count()]);
        }

        return response()->json([
            'success' => true,
            'data' => array_reverse($final)
        ], 200);
    }

    //end of sms


    //study allocation
    public function get_study_allocation($studyId)
    {
        $allocations = AllocationList::where('study_id', $studyId)
            ->groupBy('allocation')
            ->select('allocation', DB::raw('count(*) as total'))
            ->whereNotNull('date_randomised')
            ->whereNotNull('participant_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $allocations
        ], 200);
    }
    public function randomization_rate()
    {
        $rates = AllocationList::select(DB::raw('Date(date_randomised) as date_randomised'), DB::raw('count(*) as total'))
            ->whereNotNull('date_randomised')
            ->whereNotNull('participant_id')
            ->groupBy('date_randomised')
            ->orderBy('date_randomised', 'ASC')
            ->limit(14)
            ->get();

        $final = [];
        foreach ($rates as $rate){
            array_push($final, ["date_randomised"=>Carbon::parse($rate->date_randomised)->isoFormat('MMM Do YY'), "total"=>$rate->total]);
        }

        return response()->json([
            'success' => true,
            'data' => $final
        ], 200);
    }
    //end of study allocation

}
