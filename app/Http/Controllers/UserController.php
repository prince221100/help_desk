<?php

namespace App\Http\Controllers;

use App\Mail\forgotmail;
use App\Mail\registermail;
use App\Models\Manager;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function login(Request $request){
        // dd($request->all());
        $request->validate([
            'role' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        // echo $request->role,$request->email,$request->password;
        $chk = User::where(['email'=>$request->email,'password'=>$request->password,'role'=>$request->role])->first();
        // dd($chk);
        if($chk == null){
            echo "Data is not match";
            return redirect('/')->withErrors('Data is not match');
        }else{
            echo "Login Successfully and go to dashboard";
            session(['email'=>$request->email,'role'=>$request->role]);
            return redirect('/dashboard');
        }

    }
    public function dashboard(){
        // $role = 3;
        if(session()->has('email')){
            $email = session()->get('email');
            $role  = session()->get('role');
            // dd($role);
            $val = User::where('email',$email)->first();
            $name = $val->firstname;
            // dd($val->firstname);
            if($role == 1){
                $uname = "Employee";
                return view('homepage',['uname'=>$uname,'name'=>$name]);
            }
            elseif($role == 2){
                $uname = "IT Admin";
                return view('homepage',['uname'=>$uname,'name'=>$name]);
            }
            else{
                $uname = "Manager";
                return view('homepage',['uname'=>$uname,'name'=>$name]);

            }
        }else{
            return redirect('/');
        }

    }

    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/');
    }

    public function forgotpassword(){
        return view('forgot_password');
    }

    public function forgot(Request $request){
        // dd($request->all());
        $request->validate([
            'email'=>'required',
        ]);
        $email = $request->email;
        // dd($email);
        $chk = User::where('email',$email)->first();
        // dd($chk);
        if($chk == null){
            echo "Data is not Found";
            return redirect('/forgot_password')->withErrors('Data is not Found');
        }else{
            echo "send to reset link in Email id";

            $val = DB::table('password_reset_tokens')->where('email',$email)->first();
            // dd($val);
            if($val == null){
                $token = Str::random(20);
                $store = DB::table('password_reset_tokens')->insert([
                    'email'=>$email,
                    'token'=>$token,
                    'created_at'=>Carbon::now(),
                ]);
                Mail::to($email)->send(new forgotmail($token));

                return redirect('/forgot_password')->withSuccess('Send to reset Link in Email Id');
            }
            else{
                return redirect('/forgot_password')->withErrors('Please Check Your email! link is already send');

            }
        }

    }
    public function reset_password($token){
        // $email = $email;
        $token = $token;
        // dd($token);
        $val = DB::table('password_reset_tokens')->where('token',$token)->first();
        if($val){
            // dd($val->email);
            return view('resetform',['token'=>$token,'email'=>$val->email]);
        }else{

            return redirect('/forgot_password')->withErrors('Your token is not set. please send request again!');

        }

    }
    public function reset(Request $request){
        // dd($request->all());
        $request->validate([
            'password' => 'required',
            'confirmpassword' => 'required|same:password',
        ]);

        $val = DB::table('password_reset_tokens')->where('token',$request->token)->first();
        // dd($val);
        if($val == null){
            return redirect("/")->withErrors(['msg'=> 'You are not memeber our webpage']);

        }else{
            // echo "Data updated";
            $pass= $request->password;
            $pass1= $request->confirmpassword;
            $email = $val->email;
            if($pass == $pass1){
                // echo "Password is match";
                $upd = User::where('email',$email)->update(['password'=> $pass]);

                DB::table('password_reset_tokens')->where('email',$email)->delete();

                return redirect('/')->withSuccess('Password is Updated Successfully..');

            }
            else{
                DB::table('password_reset_tokens')->where('email',$email)->delete();

                return redirect("/forgot_password")->withErrors(['msg'=> 'Try Again! Password is not match']);
            }
        }
    }

    public function add_employee(){
        if(session()->has('email')){
            $role = session()->get('role');
            if($role == 2){
                $uname = "IT Admin";
                $val = Manager::all();
                // dd($val);
                return view('addemployee',['uname'=>$uname,'manager'=>$val]);
            }
            else{
                return redirect('/');
            }

        }else{
            return redirect('/');
        }

    }

    public function add_employeedata(Request $request){
        // dd($request->all());
        $request->validate([
            'employeeid' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|unique:users,email',
            'password'=>'required',
            'department_name' => 'required',
            'managerid' => 'required'
        ]);
        $empid = $request->employeeid;
        $fname = $request->firstname;
        $lname = $request->lastname;
        $email = $request->email;
        $pass = $request->password;
        $department_name = $request->department_name;
        $managerid = $request->managerid;

        // dd($pass);

        $val = new User();
        $val->employee_id = $empid;
        $val->firstname = $fname;
        $val->lastname = $lname;
        $val->email = $email;
        $val->role = 1;
        $val->manager_id =$managerid;
        $val->users_department = $department_name;
        $val->password = $request->password;
        $val->save();

        Mail::to($email)->send(new registermail($request));
        return redirect('/add_employee')->withSuccess('Employee data stored successfully.');

    }

    public function show_employee(){

        if(session()->has('email')){
            $role = session()->get('role');
            if($role == 2){
                $uname = "IT Admin";
                $val = User::where('role',1)->paginate(10);
                return view('show_employee',['uname'=>$uname,'data'=>$val]);
            }
            else{
                return redirect('/');
            }

        }else{
            return redirect('/');
        }
    }


    public function profile(){
        if(session()->has('email')){
            $role = session()->get('role');
            $email = session()->get('email');
            $val = User::where('email',$email)->first();
            $name = $val->firstname;
            // dd($val->firstname);
            if($role == 1){
                $uname = "Employee";
                return view('profile',['uname'=>$uname,'name'=>$name,'val'=>$val]);
            }
            elseif($role == 2){
                $uname = "IT Admin";
                return view('profile',['uname'=>$uname,'name'=>$name,'val'=>$val]);
            }
            else{
                $uname = "Manager";
                return view('profile',['uname'=>$uname,'name'=>$name,'val'=>$val]);

            }


        }else{
            return redirect('/');
        }

    }

    public function profile_update(Request $request){
        // dd($request->all());
        $request->validate([
            'firstname'=>'required',
            'lastname' => 'required',
            'password'=>'required'

        ]);
        $val = User::where('email',$request->email)->first();
        // dd($val);


    }

}
