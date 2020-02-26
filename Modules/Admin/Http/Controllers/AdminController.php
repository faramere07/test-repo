<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use Modules\Admin\Entities\UserDetail;
use Modules\Admin\Entities\UserType;
use Modules\TaskMaster\Entities\Tasks;
use Modules\TaskMaster\Entities\Project;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;
use Auth;
use Session;
use Redirect;

class AdminController extends Controller
{

    public function index()
    {

        $types = UserType::where('type_name','!=','Admin')->get();

        return view('admin::index');
    }

    public function viewTask()
    {
        $tasks = Tasks::with(['project','user']);

        return Datatables::of($tasks)->make(true);
    }

    public function viewUsers()
    {
        $user_types = UserType::all();

        return view('admin::users', compact('user_types'));
    }

    public function usersShow()
    {
        $users = User::with('userDetail')->where('users.id','!=',Auth::id());

        return DataTables::of($users)
            ->addColumn('actions', function($user) {
                    return '
                    <button class="btn btn-outline-danger float-right col-md-5 mx-2 destroy" userId="'.$user->id.'">
                        <i class="fas fa-user-minus"></i>
                        <div class="buttonText">
                            Deactivate
                        </div>
                    </button>
                    <button class="btn btn-outline-info col-md-5 view" userId="'.$user->id.'">
                        <i class="fas fa-eye"></i>
                        <div class="buttonText2">
                            View
                        </div>
                    </button>
                            ';
                })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function addUser(Request $request)
    {
        $username = User::where('username',$request->get('username'))->first();

        if(empty($username)){
            User::create([
                'password' => Hash::make($request->get('password')),
                'username' => $request->get('username'),
                'type_id' => $request->get('type_id'),
                
            ]);

            Session::flash('message', "New User Added!");
        }else{
            Session::flash('message', "Failed to add User, Username already Exists!");
        }

        return Redirect::back();
    }

    public function editUser(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $userDetail = UserDetail::where('user_id', $request->id)->first();


        echo
                ' 
             <p class="text-danger emptyUpdate"><em>*Please fill all information below.</em></p>
                <input type="hidden" name="id" id="peopleId" value="'.$user->id.'">

                 <input type="text" name="username" class="form-control mb-1 " placeholder="First Name" required id="firstNameAdd" value="'.$user->username.'">

                  <input type="text" name="firstName" class="form-control mb-1 " placeholder="First Name" required id="firstNameAdd" value="'.$userDetail->first_name.'">

                   <input type="text" name="midName" class="form-control mb-1 firstNameEdit" placeholder="Middle Name" required id="firstNameAdd" value="'.$userDetail->mid_name.'">
              
                  <input type="text" name="lastName" class="form-control lastNameEdit" placeholder="Last Name" required id="lastNameAdd" value="'.$userDetail->last_name.'">';

    }


    public function saveEditUser(Request $request)
    {
        $user = User::find($request->id);
        $user->username =  $request->username;
        
        $user->save();

        $userDetail = UserDetail::where('user_id', $request->id)->first();
        $userDetail->first_name =  $request->firstName;
        $userDetail->last_name =  $request->lastName;
        $userDetail->mid_name =  $request->midName;

        
        $userDetail->save();

       
    }


    public function destroyUser(Request $request)
    {
        $userDetail = UserDetail::where('user_id', $request->id)->first();
        $user = User::find($request->id);

        $userDetail->delete();
        $user->delete();

        
        // $this->pickDate($request);
    }

    public function changePassword()
    {
        return view('admin::changePassword');
        
    }

    public function savePassword(Request $request)
    {
        $message = array(
            'password.min' => 'Please input atleast 6 characters',
            'password.confirmed' => 'Password does not match',
           
        );
        $request->validate( [
            'password' => 'sometimes|string|min:6|confirmed',

        ], $message);

        $id =  Auth::id();

        $user = User::find($id);
        $user->password =Hash::make($request->password);
        $user->save();

        // return view('admin::index')->with('success', 'User Updated');
        return redirect()->route('adminHome')->with('success','Password Changed');
        
    }

}
