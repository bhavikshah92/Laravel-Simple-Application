<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use App\Models\User;
use Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(){

        if(Auth::check()){
            $emp = DB::table('employee')
            ->where('deleted_at',NULL)
            ->get();
            //return view('games.index',compact('games'));
            return view('employee.index', ['employees' => $emp]);
        }
        else{
            return redirect('login');
        }
        
    }

    public function showAddScreen(){
        return view("employee.create");
    }

    public function storeEmployee(Request $request){
        try {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|string|email',
                'address' => 'required|string|max:255',
                'phone' => 'required|numeric|min:10'
            ]);

            $emailid=$request->email;
            $phone=$request->phone;

                // $emp = new Employee;
                // $emp->name = $request->name;
                // $emp->emailid = $request->email;
                // $emp->address = $request->address;
                // $emp->phone = $request->phone;
                // $emp->save();
                // return redirect()->route('employee')
                // ->with('success','Employee has been created successfully.');

            if (DB::table('employee')->where(function ($query) use ($emailid,$phone) {
                $query->where('emailid','=',$emailid)
                    ->orWhere('phone','=',$phone);
            })->whereNull('deleted_at')->exists()) {
                return redirect("employee")->with('success', 'Employee details already exist');
            }           
            else{
                DB::table('employee')->insert([
                    'emailid' => $request->email,
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('users')->insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make("12345678"),
                    'usertype'=>'User',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                //Send Email
                $this->sendEmail($request->email, $request->name);
            }
            
            // $employee = new Employee;
            // $employee->name = $request->name;
            // $employee->emailid = $request->email;
            // $employee->address = $request->address;
            // $employee->phone = $request->phone;
            // //echo "<pre>";print_r($employee); die;
            // $employee->save();
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->intended('employee')->with('success', $e->getMessage());
        }
        return redirect("employee")->with('success', 'Employee details saved successfully');
    }

    public function edit($id){
        $employee = DB::table('employee')->find($id);
        return view('employee.edit', compact('employee'));
    }

    public function updateprofile(Request $request){
        try {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|string|email',
                'address' => 'required|string|max:255',
                'phone' => 'required|numeric|min:10'
            ]);
            
            $emailid=$request->email;
            $phone=$request->phone;
            $id=$request->employeeid;

            if (DB::table('employee')->where(function ($query) use ($emailid,$phone,$id) {
                $query->where('emailid','=',$emailid)
                    ->orWhere('phone','=',$phone);
            })->whereNull('deleted_at')->where('id','!=',$id)->exists()) {
                return redirect("userhome")->with('success', 'Profile details already exist');
            }           
            else{
                DB::table('employee')
                ->where('id', $request->employeeid)
                ->update([
                    'emailid' => $request->email,
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'updated_at' => now(),
                ]);
            }
            // $employee = new Employee;
            // $employee->name = $request->name;
            // $employee->emailid = $request->email;
            // $employee->address = $request->address;
            // $employee->phone = $request->phone;
            // //echo "<pre>";print_r($employee); die;
            // $employee->save();
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->intended('userhome')->with('success', $e->getMessage());
        }
        return redirect("userhome")->with('success', 'Profile details updated successfully');
    }

    public function updateEmployee(Request $request){
        try {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|string|email',
                'address' => 'required|string|max:255',
                'phone' => 'required|numeric|min:10'
            ]);
            
            $emailid=$request->email;
            $phone=$request->phone;
            $id=$request->employeeid;

            if (DB::table('employee')->where(function ($query) use ($emailid,$phone,$id) {
                $query->where('emailid','=',$emailid)
                    ->orWhere('phone','=',$phone);
            })->whereNull('deleted_at')->where('id','!=',$id)->exists()) {
                return redirect("employee")->with('success', 'Employee details already exist');
            }           
            else{
                DB::table('employee')
                ->where('id', $request->employeeid)
                ->update([
                    'emailid' => $request->email,
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'updated_at' => now(),
                ]);
            }
            // $employee = new Employee;
            // $employee->name = $request->name;
            // $employee->emailid = $request->email;
            // $employee->address = $request->address;
            // $employee->phone = $request->phone;
            // //echo "<pre>";print_r($employee); die;
            // $employee->save();
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->intended('employee')->with('success', $e->getMessage());
        }
        return redirect("employee")->with('success', 'Employee details updated successfully');
    }

    public function destroy($id){
        //$deleted = DB::table('employee')->where('id', '=', $id)->delete();
        DB::table('employee')
        ->where('id', $id)
        ->update([
            'updated_at' => now(),
            'deleted_at' => now(),
        ]);
        return redirect("employee")->with('success', 'Employee details deleted successfully');
    }

    public function uploadContent(Request $request){
        $file = $request->file('uploaded_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes
            //Check for file extension and size
            $this->checkUploadedFileProperties($extension, $fileSize);
            //Where uploaded file will be stored on the server 
            $location = 'uploads'; //Created an "uploads" folder for that
            // Upload file
            $file->move($location, $filename);
            // In case the uploaded file path is to be stored in the database 
            $filepath = public_path($location . "/" . $filename);
            // Reading file
            $file = fopen($filepath, "r");
            $importData_arr = array(); // Read through the file and store the contents as an array
            $i = 0;
            //Read the contents of the uploaded file 
            while (($filedata = fgetcsv($file, 4096, ",")) !== FALSE) {
                $num = count($filedata);
                // Skip first row (Remove below comment if you want to skip the first row)
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file); //Close after reading
            //dd($importData_arr);
            $j = 0;
            foreach ($importData_arr as $importData) {
                $name = $importData[0]; //Get user names
                $email = $importData[1]; //Get the user emails
                $address = $importData[2]; //Get the user address
                $phone = $importData[3]; //Get the user phone no
                
                try {

                    DB::beginTransaction();

                    if (DB::table('employee')->where(function ($query) use ($email,$phone) {
                        $query->where('emailid','=',$email)
                            ->orWhere('phone','=',$phone);
                    })->whereNull('deleted_at')->exists()) {
                        //return redirect("employee")->with('success', 'Employee details already exist');
                    }           
                    else{
                        $j++;
                        DB::table('employee')->insert([
                            'emailid' => $email,
                            'name' => $name,
                            'address' => $address,
                            'phone' => $phone,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        DB::table('users')->insert([
                            'name' => $name,
                            'email' => $email,
                            'password' => Hash::make("12345678"),
                            'usertype'=>'User',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        //Send Email
                        $this->sendEmail($email, $name);
                    }
                    DB::commit();
                }
                catch (\Exception $e) {
                    //throw $th;
                    DB::rollBack();
                }
            }
            return redirect("employee")->with('success', "$j records successfully uploaded");
        }
        else {
            //no file was uploaded
            //throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
            return redirect("employee")->with('error', 'No file was uploaded', Response::HTTP_BAD_REQUEST);
        }
    }

    public function checkUploadedFileProperties($extension, $fileSize){
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            }
            else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE); //413 error
            }
        }
        else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }
    
    public function sendEmail($email, $name){
        $data = array(
            'email' => $email,
            'name' => $name,
            'subject' => 'Welcome Message',
        );
        Mail::send('welcomeEmail', $data, function ($message) use ($data) {
            $message->from('mmyioenouzldemqxwt@kvhrs.com');
            $message->to($data['email']);
            $message->subject($data['subject']);
        });
    }

    public function fetchAllEmployeeAPI(){
        $emp = DB::table('employee')
        ->where('deleted_at',NULL)
        ->get();
        return response()->json(['employees' => $emp], 200);
    }

    public function findEmployeeByIdAPI($id){
        $employee = DB::table('employee')->find($id);
        return response()->json(['employees' => $employee], 200);
    }

    public function deleteEmployeeByIdAPI($id){
        $response=DB::table('employee')
        ->where('id', $id)
        ->update([
            'updated_at' => now(),
            'deleted_at' => now(),
        ]);
        if($response==1){
            return response()->json(['data' => ['success'=>1,'message'=>'Employee details deleted successfully']], 200);
        }
        else{
            return response()->json(['data' => ['success'=>0,'message'=>'Something went wrong']], 500);
        }
        
    }

    public function createEmployeeAPI(Request $request){
        try {

            $header = $request->bearerToken();
            
            if (DB::table('users')->where('api_token',$header)->exists()) {
                $request->validate([
                    'name' => 'required|max:255',
                    'email' => 'required|string|email',
                    'address' => 'required|string|max:255',
                    'phone' => 'required|numeric|min:10'
                ]);
    
                $emailid=$request->email;
                $phone=$request->phone;
    
                if (DB::table('employee')->where(function ($query) use ($emailid,$phone) {
                    $query->where('emailid','=',$emailid)
                        ->orWhere('phone','=',$phone);
                })->whereNull('deleted_at')->exists()) {
                    return response()->json(['data' => ['success'=>0,'message'=>'Employee details already exist']], 500);
                }
                else{
                    DB::table('employee')->insert([
                        'emailid' => $request->email,
                        'name' => $request->name,
                        'address' => $request->address,
                        'phone' => $request->phone,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
    
                    DB::table('users')->insert([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make("12345678"),
                        'usertype'=>'User',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
    
                    //Send Email
                    $this->sendEmail($request->email, $request->name);
                }
            }
            else{
                return response()->json(['data' => ['success'=>0,'message'=>'Unauthorized Access']], 401);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => ['success'=>0,'message'=>$e->getMessage()]], 500);
        }
        return response()->json(['data' => ['success'=>1,'message'=>'Employee details saved successfully']], 200);
    }
    
    public function updateEmployeeProfileAPI(Request $request,$id){
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|string|email',
                'address' => 'required|string|max:255',
                'phone' => 'required|numeric|min:10'
            ]);
            
            $emailid=$request->email;
            $phone=$request->phone;
            
            if (DB::table('employee')->where(function ($query) use ($emailid,$phone,$id) {
                $query->where('emailid','=',$emailid)
                    ->orWhere('phone','=',$phone);
            })->whereNull('deleted_at')->where('id','!=',$id)->exists()) {
                return response()->json(['data' => ['success'=>0,'message'=>'Profile details already exist']], 500);
            }           
            else{
                DB::table('employee')
                ->where('id', $id)
                ->update([
                    'emailid' => $request->email,
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'updated_at' => now(),
                ]);
            }            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => ['success'=>0,'message'=>$e->getMessage()]], 500);
        }
        DB::commit();
        return response()->json(['data' => ['success'=>1,'message'=>'Profile details updated successfully']], 200);
    }
}