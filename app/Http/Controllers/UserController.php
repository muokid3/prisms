<?php

namespace App\Http\Controllers;

use App\Notifications\UserCreated;
use App\Study;
use App\User;
use App\UserGroup;
use App\UserPermission;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function users() {
        $users = User::all();
        $user_roles = UserGroup::all();

        return view('users.index')->with([
            'users' => $users,
            'user_roles' => $user_roles,
            'edit' => false,
        ]);
    }
    public function usersDT() {
        $users = User::all();

        return DataTables::of($users)
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->editColumn('role',function ($user) {
                return optional($user->role)->name;
            })
            ->editColumn('status',function ($user){
                return $user->status ? 'Active' : 'Blocked';
            })

            ->addColumn('phone',function ($user) {
                return $user->mobile_no;
            })
            ->editColumn('created_by', function($user) {
                if ($user->masterfile)
                    return optional(optional($user->masterfile)->creator)->email;
                else
                    return "";
            })
            ->addColumn('actions', function($user) {
                $actions = '<div class="pull-right">
                        <button source="' . route('edit-user' ,  $user->id) . '"
                    class="btn btn-warning btn-sm edit-user-btn" acs-id="'.$user->id .'">
                    <i class="fa fa-edit">edit</i> Edit</button>';
                $actions .= '<form action="'. route('delete-user',  $user->id) .'" style="display: inline; margin-left:10px" method="post" class="del_user_form">';
                $actions .= method_field('DELETE');
                $actions .= csrf_field() .'<button class="btn btn-danger btn-sm"><i class="fa fa-delete">edit</i>  Delete</button></form>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);

    }

    public function register_user(Request $request)
    {
        $data = request()->validate([
            'user_group' => 'required|max:10',
            'phone_no' => 'required|max:12|unique:users,phone_no',
            'email' => 'nullable|email|max:255|unique:users,email',
            'title'  => 'required',
            'site'  => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        $this->random_pass = $this->randomPassword();


        $user = new User();
        $user->user_group = $request->user_group;
        $user->email = $request->email;
        $user->phone_no = $request->phone_no;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->title = $request->title;
        $user->site_id = $request->site;
        $user->password = bcrypt($this->random_pass);


        $pass = $this->random_pass;

        DB::transaction(function() use ($user, $pass) {

            $user->saveOrFail();
            Session::flash("success", "User has been created");

            $user->notify(new UserCreated($pass));

            $message = 'Your account on PRISMS has been created as a '.$user->role->name.' for your organisation. Use your email as your username. Your password is '.$pass.' Link: https://prisms.kemri-wellcome.org/';

            send_sms("SEARCHTrial",$message,$user->phone_no,$user->id);


        });

        return redirect('/users');
    }


    public function edit_user($id)
    {
        $user = User::find($id);
        return $user;
    }

    public function update_user(Request $request)
    {

        $data = request()->validate([
            'user_group' => 'required|max:10',
            'phone_no' => 'required|max:12|unique:users,phone_no,'.$request->id,
            'email' => 'required|email|max:255|unique:users,email,'.$request->id,
            'title'  => 'required',
            'site'  => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'id' => 'required',
        ]);



        $user = User::find($request->id);
        $user->user_group = $request->user_group;
        $user->email = $request->email;
        $user->phone_no = $request->phone_no;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->title = $request->title;
        $user->site_id = $request->site;
        $user->update();

        request()->session()->flash('success', 'User has been updated.');
        return redirect()->back();
    }

    public function delete_user($id)
    {
        try {
            User::destroy($id);

            request()->session()->flash('success', 'User has been deleted.');
        } catch (QueryException $qe) {
            request()->session()->flash('warning', 'Could not delete user because it\'s being used in the system!');
        }

        return redirect()->back();
    }


    public function randomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public function editProfile() {
        $user = auth()->user();
        $photo = asset('assets/img/default-avatar.png');

        if ($user->photo) {
            $photo = Storage::disk('public')->url($user->photo);
        }



        return view('auth.edit-profile', [
            'user' => $user,
            'photo' => $photo
        ]);
    }

    public function updateProfile(Request $request) {
        $user = $request->user();
        request()->session()->flash('update_profile', true);

        $this->validate($request, [
            'first_name' => 'required',
            'surname' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'id_no' => 'required|unique:master_files,id_no,'.$user->masterfile->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile_no' => 'required|unique:users,mobile_no,' . $user->id
        ]);


        DB::transaction(function() use($request, $user) {


            if ($request->hasFile('photo')){

                $uploadedFile = $request->file('photo');
                $filename = time().$uploadedFile->getClientOriginalName();

                $request->file('photo')->storeAs("public/uploads", $filename);

                $user->photo = "uploads/".$filename;
            }

            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $user->update();



            $masterfile = $user->masterfile;
            $masterfile->first_name = $request->first_name;
            $masterfile->middle_name = $request->middle_name;
            $masterfile->surname = $request->surname;
            $masterfile->gender = $request->gender;
            $masterfile->dob = $request->dob;
            $masterfile->id_no = $request->id_no;
            $masterfile->mobile_no2 = $request->mobile_no2;
            $masterfile->update();


            $this->_passed = true;
        });

        if ($this->_passed)
            request()->session()->flash('success', 'Users Profile has been updated.');
        else
            request()->session()->flash('warning', 'Failed to update profile!');

        return redirect('edit-profile');
    }

    public function myProfile() {
        $user = auth()->user();
        $user_role = $user->role->role_code;
        $photo = asset('assets/img/default-avatar.png');
        $branches=BankBranch::all();

        $profile_data = [
            'user' => $user,
            'photo' => $photo,
            'mfUser' => $user->masterfile,
            'branches'=>$branches,
            'withdrawal_status' => [
                'processing' => 'Processing',
                'completed' => 'Completed'
            ]
        ];

        //if merchant admin then get accounts
        if (($user_role == Role::Merchant && $user->canEdit()) || $user_role == Role::SystemAdmin)
        {
            $profile_data['bank_accounts'] = $user->masterfile->bankAccounts;
            $profile_data['withdrawals'] = $user->masterfile->withdrawals;
            $profile_data['withdrawal_modes'] = WithdrawalMode::all();
            $profile_data['journal'] = AccountJournal::where('account_mf_id', $user->masterfile->id)->orderBy('transaction_date', 'asc')->get();
            $profile_data['debit'] = AccountJournal::debitTotal($user->masterfile->id);
            $profile_data['credit'] = AccountJournal::creditTotal($user->masterfile->id);
            $profile_data['current_balance'] = AccountJournal::accountBalance($user->masterfile->id);
        }

        return view('auth.my-profile', $profile_data);
    }

    public function updatePassword(Request $request) {
        $request->session()->flash('update_password', true);

        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $check = auth()->validate(['email' => $request->user()->email, 'password' => request('current_password')]);

        if($check) {
            User::where('id', $request->user()->id)->update(['password' => bcrypt(request('password'))]);
            $request->session()->flash('success', 'You have reset your password.');
        } else {
            $request->session()->flash('warning', 'The current password is incorrect, please try again.');
        }

        return redirect('edit-profile');
    }

    public function user_groups()
    {
        $user_groups = UserGroup::all();

        return view('users.user_groups')->with([
            'user_groups' => $user_groups,
        ]);
    }

    public function new_user_group(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:user_groups,name',
        ]);


        $user_group = new UserGroup();
        $user_group->name = $request->name;
        $user_group->saveOrFail();


        Session::flash("success", "Group has been created");


        return redirect()->back();
    }


    public function get_group_details($id)
    {

        return UserGroup::find($id);
    }

    public function update_group_details(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:user_groups,id',
            'name' => 'required|unique:user_groups,name,'.$request->id,
        ]);


        $user_group = UserGroup::find($request->id);
        $user_group->name = $request->name;
        $user_group->update();


        Session::flash("success", "Group has been updated");


        return redirect()->back();
    }


    public function delete_group(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:user_groups,id',
        ]);


        $user_group = UserGroup::find($request->id);
        $user_group->delete();


        Session::flash("success", "Group has been deleted");


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
    public function userGroupDetailsDT($_id) {

        $users = User::where('user_group',$_id)->get();

        return DataTables::of($users)
            ->editColumn('id', function ($user) {
                return $user->id;
//                return '<a href="'.url('users/details/'.$user->id) .'" title="View User" >#'. $user->id .' </a>';
            })

            ->addColumn('role',function ($user) {
                return optional($user->role)->name;
            })

            ->editColumn('email',function ($user) {
                return $user->email;
            })
            ->addColumn('actions', function($user) {
                $actions = '<div class="pull-left">';
//                $actions .= '<a title="Edit User" class="btn btn-link btn-sm btn-warning btn-just-icon"><i class="material-icons">edit</i> </a>';
//                $actions .= '<a title="View User" href="'.url('users/details/'.$user->id) .'" class="btn btn-info btn-sm pull-right"><i class="material-icons">list</i> View</a>';
//                $actions .= '<a title="Manage User" class="btn btn-link btn-sm btn-info btn-just-icon"><i class="material-icons">dvr</i> </a>';
                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['id','actions'])
            ->make(true);

    }
    public function add_group_permission(Request $request)
    {
        $this->validate($request, [
            'permission' => 'bail|required',
            'group_id' => 'bail|required',
        ]);

        foreach ($_POST['permission'] as $perm) {
            $userPermission = new userPermission();
            $userPermission->group_id = $request->group_id;
            $userPermission->permission_id = $perm;
            $userPermission->saveOrFail();
        }

        request()->session()->flash('success', 'Permissions added successfully');

        return redirect()->back();
    }
    public function delete_group_permission($group_id)
    {
        $userPermission = UserPermission::find($group_id);
        if ($data = $userPermission->delete()) {
            request()->session()->flash("success", "Permission deleted successfully.");
        }
        return redirect()->back();
    }
}
