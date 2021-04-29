<?php

namespace App\Http\Controllers\API;

use App\AllocationList;
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

        return response()->json([
            'success' => true,
            'message' => 'Site has been updated successfully'
        ], 200);
    }
    public function delete_site($id)
    {
        try {
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

        return response()->json([
            'success' => true,
            'message' => 'Study has been updated successfully'
        ], 200);
    }
    public function delete_study($id)
    {
        try {
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

        return response()->json([
            'success' => true,
            'message' => 'Stratum has been updated successfully'
        ], 200);
    }
    public function delete_stratum($id)
    {
        if (is_null(AllocationList::where('stratum_id',$id)->first())){
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

}
