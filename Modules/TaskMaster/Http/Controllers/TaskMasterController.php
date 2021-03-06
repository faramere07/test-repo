<?php

namespace Modules\TaskMaster\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\TaskMaster\Entities\Project;
use Modules\TaskMaster\Entities\Tasks;
use Modules\Admin\Entities\TaskType;
use App\User;
use Modules\Admin\Entities\UserDetail;
use Modules\Admin\Entities\UserType;
use Auth;
use PDF;
use \Carbon\Carbon;


use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;




class TaskMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $id =  Auth::id();
      $userDetails = UserDetail::where('user_id', $id)->first();
      $countProject = Project::where('user_id', $id)->where('archive_status', 'No')->count();
      $user = User::find($id);
      
      return view('taskmaster::index', compact('userDetails', 'countProject'));
      


    }

    public function project_dtb(){

        $projects = Project::where('user_id', Auth::id())
        ->where('archive_status', '!=', 'Archived')
        ->get();
                   
         return DataTables::of($projects)
            ->addColumn('actions', function($proj) {
                    return '<a href="'.route('viewTasks', $proj->id).'" class="btn btn-outline-info mx-2" role="button" aria-pressed="true">View Tasks</a>
                            <button class="btn btn-outline-primary edit" projId="'.$proj->id.'">Edit</button>
                            <button class="btn btn-outline-danger mx-2 destroy" projId="'.$proj->id.'" fname="'.$proj->firstName.'">Delete</button>
                            ';
                })
            ->rawColumns(['actions'])
            ->make(true);
    }


    public function addProj(Request $request)
    {
            Project::firstOrCreate([
            'project_name' => $request->projName,
            'project_desc' => $request->projDesc,
            'user_id' =>  Auth::id(),
            ]);
    }

     public function editProj(Request $request)
    {
        $proj = Project::where('id', $request->id)->first();

        echo
                ' 
             <p class="text-danger emptyUpdate"><em>*Please fill all information below.</em></p>
                <input type="hidden" name="id" id="peopleId" value="'.$proj->id.'">

                 <input type="text" name="projName" class="form-control mb-1 " placeholder="Project Name" required id="firstNameAdd" value="'.$proj->project_name.'">

                 <textarea class="form-control" name="projDesc" rows="3" placeholder="Project Description." required>'.$proj->project_desc.'</textarea>';

    }


    public function saveEditProj(Request $request)
    {
        
        $proj = Project::where('id', $request->id)->first();
        $proj->project_name =  $request->projName;
        $proj->project_desc =  $request->projDesc;
        
        
        $proj->save();

       
    }

    public function destroyProj(Request $request)
    {
        // $proj = Project::find($request->id);
        
        // $proj->delete();

        $proj = Project::where('id', $request->id)->first();
        $proj->archive_status =  'Archived';
        
        $proj->save();

    }

   
     public function task_dtb($id){
        $tasks = Tasks::with(['user', 'user.userDetail'])->where('project_id',$id)->get();

         return DataTables::of($tasks)
            ->addColumn('actions', function($tasks) {
                    return '<button class="btn btn-outline-danger col-md-5 float-right mx-2 destroy" taskId="'.$tasks->id.'" >Delete</button>
                            <button class="btn btn-outline-primary col-md-5 float-right edit" taskId="'.$tasks->id.'">Edit</button>
                            
                            ';
                })
            ->addColumn('assignee', function($tasks) {
                    return '<td>'.$tasks->user->userDetail->first_name.' '.$tasks->user->userDetail->last_name.'</td>
                            
                            ';
                })
            ->rawColumns(['actions', 'assignee'])
            ->make(true);
    }



    //************TASKS

     public function viewTasks($id){
        
        $project = Project::find($id);
        $types = TaskType::all();

        $users = User::join('user_types', 'user_types.id', 'users.type_id')
                    ->join('user_details', 'user_details.user_id', 'users.id')
                    ->where('type_name', 'User')
                    ->where('first_name', '!=', 'null')
                    ->select('*','users.id as u_id')
                    ->get();
                  

        return view('taskmaster::viewTasks', compact('project', 'types', 'users'));
    }


    public function addTask(Request $request)
    {
        Tasks::firstOrCreate([
            'project_id' => $request->projId,
            'task_title' => $request->taskTitle,
            'task_description' => $request->taskDesc,
            'task_type_id' =>$request->taskType,
            'user_id' => $request->userId,
            'date_time' => $request->dateTime,
            'due_date' => $request->dueDate,
            'status' => 'Ongoing',
            ]);
    }

    public function destroyTask(Request $request)
    {
        $taskdes = Tasks::find($request->id);
        
        $taskdes->delete();
    }

    public function editTask(Request $request)
    {
        $task = Tasks::where('id', $request->id)->first();
        $types = TaskType::all();

        $users = User::join('user_types', 'user_types.id', 'users.type_id')
                    ->join('user_details', 'user_details.user_id', 'users.id')
                    ->where('type_name', 'User')
                    ->where('first_name', '!=', 'null')
                    ->select('*','users.id as u_id')
                    ->get();

        echo '

            <p class="text-danger empty"><em>*Please fill all information below.</em></p>
    
              

              <div class="input-group input-group-lg mb-2">
                  <input required type="text" name="taskTitle" class="form-control" placeholder="Task title" value="'.$task->task_title.' ">   
              </div>

              <div class="mb-2">
                  <select class="form-control" name="taskType" required>
                    <option>--select task type--</option>';

                    foreach ($types as $type){
                        if($type->id == $task->task_type_id){
                            echo '<option selected value="'.$type->id.'">'.$type->type_name.'</option>'; 

                        }else{
                            echo '<option value="'.$type->id.'">'.$type->type_name.'</option>'; 
                        }
                    }
        echo '
                  </select>   
              </div>

              <div class="mb-2">

                  <select class="form-control" name="userId" required>
                    <option>--select user--</option>';

                    foreach ($users as $user){

                        if($user->u_id == $task->user_id){
                            echo '<option selected value="'.$user->u_id.'">'.$user->first_name.' '.$user->lastname.'</option>';

                        }else{
                            echo '<option value="'.$user->id.'">'.$user->first_name.' '.$user->lastname.'</option>';
                        }
                    }
        echo'
                  </select>   
              </div>
              <div class="row mb-2">
                <div class="col-md-6">
                  <label>Due Date:</label>
                  <input required type="date" id="dueDate" name="dueDate" class="form-control" value="'.$task->due_date.' ">
                </div>
                
                <div class="col-md-6">
                  <label>Due Time:</label>
                  <input required type="time" name="dateTime" class="form-control" value="'.$task->date_time.' ">
                </div>
              </div>
              

              <textarea class="form-control" name="taskDesc" rows="3" placeholder="Task Description." required>'.$task->task_description.'</textarea>

              <input type="hidden" name="id" value="'.$task->id.'">

              
              

        ';

    }

    public function saveEditTask(Request $request)
    {
        
        $task = Tasks::where('id', $request->id)->first();

        $task->task_title = $request->taskTitle;
        $task->task_description = $request->taskDesc;
        $task->task_type_id =$request->taskType;
        $task->user_id = $request->userId;
        $task->date_time = $request->dateTime;
        $task->due_date = $request->dueDate;
        
        
        $task->save();

       
    }

    //Update User Details

    public function updateUserDetails (Request $request){
        $user = User::find(Auth::id());
        $userDetails = UserDetail::where('user_id', Auth::id())->first();

        if($request->image != null){
          $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images'), $imageName);
        $userDetails->profile_picture = $imageName;
        }

        $userDetails->first_name= $request->firstName;
        $userDetails->mid_name = $request->midName;
        $userDetails->last_name =$request->lastName;
        $userDetails->save();


        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('taskmasterHome');

    }

    public function changePassword()
    {
        return view('taskmaster::changePassword');
        
    }

    public function savePassword(Request $request)
    {
       
        $hashedPassword = User::find(Auth::id());

        if (Hash::check($request->old_password, $hashedPassword->password)) {
            // Validations
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
            return redirect()->route('taskmasterHome')->with('success', 'Password Changed');
        }else{
           
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'old_password' => ['Password is incorrect.'],

             ]);
             throw $error;
        }
   
    
    }

    public function viewProfile(){

      $id =  Auth::id();
      $userDetails = UserDetail::where('user_id', $id)->first();

        return view('taskmaster::viewProfile', compact('userDetails'));
    }

public function viewEditProfile(Request $request)
    {
        $userDetails = UserDetail::where('user_id', Auth::id())->first();

        echo '
        <div class="form-row">
        <div class="col-md-4">
        <label class="small" for="fname">First Name</label>
            <input name="firstName" class="form-control" type="text" value="'.$userDetails->first_name.'">
        </div>

        <div class="col-md-4">
        <label class="small" for="mname">Middle Name</label>
            <input name="middleName" class="form-control" type="text" value="'.$userDetails->mid_name.'">
        </div>

        <div class="col-md-4">
        <label class="small" for="lname">Last Name</label>
            <input required name="lastName" class="form-control" type="text" value="'.$userDetails->last_name.'">
        </div>
        </div><br>
        <div class="form-row col-md-12">
        <label class="small" for="image">Image</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="image" name="image">
                <label class="custom-file-label" for="customFile">Choose file</label>
              </div>
        </div>

        </div>
        ';


    }
    public function editProfile (Request $request){
        $user = User::find(Auth::id());
        $userDetails = UserDetail::where('user_id', Auth::id())->first();

        if($request->image != null){
          $imageName = time().'.'.request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images'), $imageName);
        $userDetails->profile_picture = $imageName;
        }

        $userDetails->first_name= $request->firstName;
        $userDetails->mid_name = $request->middleName;
        $userDetails->last_name =$request->lastName;
        $userDetails->save();


        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('viewProfile')->with('success', 'Profile Updated');
        

    }

    public function projectReport(){

        $month = date('M Y', strtotime('first day of last month'));
        $projects = Project::whereMonth(
            'created_at', '=', Carbon::now()->subMonth()->month)->get();
      
      
        $pdf = PDF::loadView('taskmaster::pdf.projReport', compact('month', 'projects'));

        $pdf->save(storage_path().'_filename.pdf');

        return $pdf->stream('project_'.$month.'.pdf');
      
    }

    public function viewTaskReport(){

        $projects = Project::all();
        $types = TaskType::all();

        $users = User::join('user_types', 'user_types.id', 'users.type_id')
                    ->join('user_details', 'user_details.user_id', 'users.id')
                    ->where('type_name', 'User')
                    ->where('first_name', '!=', 'null')
                    ->select('*','users.id as u_id')
                    ->get();
                  

        return view('taskmaster::viewTaskReport', compact('projects', 'types', 'users'));
    }
    public function taskReport_dtb(){

        $tasks = Tasks::all();

         return DataTables::of($tasks)
            ->addColumn('actions', function($task) {
                    return '<button class="btn btn-outline-danger col-md-5 float-right mx-2 destroy" taskId="'.$task->id.'" >Delete</button>
                            <button class="btn btn-outline-primary col-md-5 float-right edit" taskId="'.$task->id.'">Edit</button>
                            
                            ';
                })
            ->addColumn('project_name', function($task) {
                    return $task->project->project_name;
                })
            ->rawColumns(['actions', 'project_name'])
            ->make(true);
    }

    public function taskReport(Request $request){
        $pTitle = $request->select1;
        $status = $request->select2;
        $min = $request->min;
        $max = $request->max;

        //get the details of the proj first to get the id

        //use id from previous query
        if($pTitle && $status && $min && $max){
            $project = Project::whereProject_name($pTitle)->first();
            $query = Tasks::whereProject_id($project->id)
                            ->whereStatus($status)
                            ->whereBetween('due_date', [$min, $max])
                            ->get();
        }else if($pTitle && !$status && $min && $max){
            $project = Project::whereProject_name($pTitle)->first();
            $query = Tasks::whereProject_id($project->id)
                            ->whereBetween('due_date', [$min, $max])
                            ->get();
        }else if(!$pTitle && $status && $min && $max){
            $query = Tasks::whereStatus($status)
                            ->whereBetween('due_date', [$min, $max])
                            ->get();
        }else{
            $query = Tasks::all()->whereBetween('due_date', [$min, $max]);
            
        }


        $month = date('M Y', strtotime('first day of last month'));
        $start = date('M d, Y', strtotime($min));
        $end = date('M d, Y', strtotime($max));
        $projects = Project::whereMonth(
            'created_at', '=', Carbon::now()->subMonth()->month)->get();
      
      
        $pdf = PDF::loadView('taskmaster::pdf.taskReport', compact('query', 'month', 'status', 'pTitle', 'start', 'end'));

        $pdf->save(storage_path().'_filename.pdf');

        return $pdf->stream('project_'.$month.'.pdf');
      
    }
}
