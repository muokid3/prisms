<?php

namespace App\Http\Controllers;

use App\AuditTrail;
use App\BulkMail;
use App\BulkSms;
use App\Jobs\SendSms;
use App\User;
use App\UserGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function bulk_sms() {
        $userGroups = UserGroup::all();

        return view('sms.bulk_sms')->with([
            'userGroups' => $userGroups,

        ]);
    }

    public function bulkSmsDT() {
        $sms = BulkSms::all();
        return DataTables::of($sms)

            ->editColumn('created_at', function ($sms) {
                return Carbon::parse($sms->created_at)->isoFormat('MMM Do YYYY H:m:s');
            })
            ->editColumn('created_by', function ($sms) {
                return optional($sms->creator)->first_name.' '.optional($sms->creator)->last_name;
            })

            ->make(true);

    }

    public  function create_group_bulk_sms(Request  $request){
        $data = request()->validate([
            'user_group'  => 'required',
            'message'  => 'required',
        ]);


        DB::transaction(function() use ($request) {

            $bulkSms = new BulkSms();
            $bulkSms->recipients = UserGroup::find($request->user_group)->name;
            $bulkSms->message = $request->message;
            $bulkSms->created_by = auth()->user()->id;
            $bulkSms->saveOrFail();

            $targetGroup = User::where('user_group', $request->user_group)->get();

            foreach ($targetGroup as $target){
                SendSms::dispatch($target->phone_no, $request->message);
            }

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Sent bulk SMS to '.optional(UserGroup::find($request->user_group))->name,
            ]);

            request()->session()->flash('success', 'Bulk SMS to target group has been scheduled successfully');
        });
        return redirect()->back();
    }

    public  function create_specified_bulk_sms(Request  $request){
        $data = request()->validate([
            'recipients'  => 'required',
            'message'  => 'required',
        ]);


        DB::transaction(function() use ($request) {

            $bulkSms = new BulkSms();
            $bulkSms->recipients = $request->recipients;
            $bulkSms->message = $request->message;
            $bulkSms->created_by = auth()->user()->id;
            $bulkSms->saveOrFail();

            $targetGroup = explode (",", $request->recipients);


            foreach ($targetGroup as $target){
                SendSms::dispatch($target, $request->message);
            }

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Sent bulk SMS to: '.$request->recipients,
            ]);

            request()->session()->flash('success', 'Bulk SMS to specified recipients has been scheduled successfully');
        });
        return redirect()->back();
    }

}
