<?php

namespace App\Http\Controllers;

use App\AuditTrail;
use App\BulkMail;
use App\Mail;
use App\User;
use App\UserGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MailController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function bulk_mails() {
        $userGroups = UserGroup::all();

        return view('mails.bulk_mails')->with([
            'userGroups' => $userGroups,

        ]);
    }
    public function bulkMailsDT() {
        $mails = BulkMail::all();
        return DataTables::of($mails)
            ->addColumn('target', function ($mails) {
                return optional($mails->group)->name;
            })
            ->editColumn('created_at', function ($mails) {
                return Carbon::parse($mails->created_at)->isoFormat('MMM Do YYYY H:m:s');
            })
            ->editColumn('created_by', function ($mails) {
                return optional($mails->creator)->first_name.' '.optional($mails->creator)->last_name;
            })
//            ->addColumn('actions', function($mails) {
//                $actions = '<div class="pull-right">';
//                $actions = '<div class="pull-right">
//                        <button source="' . route('edit-study' ,  $study->id) . '"
//                    class="btn btn-warning btn-sm edit-study-btn" acs-id="'.$study->id .'">
//                    <i class="fa fa-edit">edit</i> Edit</button>';
//                $actions .= '<form action="'. route('delete-study',  $study->id) .'" style="display: inline; margin-left:10px" method="post" class="del_study_form">';
//                $actions .= method_field('DELETE');
//                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
//                $actions .= '</div>';
//                return $actions;
//            })
//            ->rawColumns(['actions'])
            ->make(true);

    }

    public  function create_bulk_mail(Request  $request){
        $data = request()->validate([
            'user_group'  => 'required',
            'subject'  => 'required',
            'mail_content'  => 'required',
        ]);


        DB::transaction(function() use ($request) {

            $bulkMail = new BulkMail();
            $bulkMail->user_group = $request->user_group;
            $bulkMail->subject = $request->subject;
            $bulkMail->content = $request->mail_content;
            $bulkMail->created_by = auth()->user()->id;
            $bulkMail->saveOrFail();

            $targetGroup = User::where('user_group', $request->user_group)->get();


            foreach ($targetGroup as $target){
                if ($target->email != null){
                    /*
                     * TODO send email
                     */

                    \Illuminate\Support\Facades\Mail::to($target)->queue(new Mail\Bulk($bulkMail));

                    $mail = new Mail();
                    $mail->recipient = $target->email;
                    $mail->subject = $request->subject;
                    $mail->content = $request->mail_content;
                    $mail->saveOrFail();
                }

            }

            AuditTrail::create([
                'created_by' => auth()->user()->id,
                'action' => 'Sent bulk mail ('.$request->subject.') to '.optional(UserGroup::find($request->user_group))->name,
            ]);

            request()->session()->flash('success', 'E-mails to target group have been scheduled successfully');
        });
        return redirect()->back();
    }

}
