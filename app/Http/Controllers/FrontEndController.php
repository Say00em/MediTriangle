<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HealthCard;
use App\Models\DoctorModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HospitalModel;
use App\Models\DepartmentModel;
use App\Models\HealthCardApplicaton;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;

class FrontEndController extends Controller
{
    //Appoinmnet
    function appoinmentLink(){
        SEOMeta::setTitle('Appoinment'); //web title
        SEOTools::setDescription('this is description');
        SEOMeta::addKeyword('this is tags');
        OpenGraph::setTitle('this is seo title');
        SEOMeta::setCanonical('https://meditriangle.com' . request()->getPathInfo());
        return view('frontend.appoinment.index');
    }
    function loginLink(){
        return view('frontend.login');
    }
    function loginAccess(Request $request){
        $request->validate([
            'number'    => 'required',
            'password'  => 'required',
        ]);
        if (Auth::guard()->attempt(['number'=> $request->number,'password'=>$request->password],$request->remember)) {
            return redirect()->route('profile')->with('succ','Successfully logged in');
        }else {
            return back()->with('err','Password not matching');
        }


    }
    function registerLink(){
        return view('frontend.register');
    }
    function registerAccess(Request $request){
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'number'    => 'required',
            'password'  => 'required |min:8|max:16',
        ]);
        if (!User::where('email', $request->email)->exists()) {
            User::insert([
                'name'      => $request->name,
                'email'     => $request->email,
                'number'    => $request->number,
                'password'  => bcrypt($request->password),
            ]);
            Auth::guard()->attempt([
                'number'    => $request->number,
                'password'  =>$request->password
            ],$request->remember);
            return redirect()->route('profile')->with('succ','Account Created');
        }else{
            return back()->with('err','Email Already Exists');
        }
    }
    function reset(){
        return view('frontend.auth.reset');
    }
    function contact(){
        return view('frontend.contact');
    }
    function thankyou(){
        return view('frontend.thankyou');
    }
    function index(){
        SEOMeta::setTitle('Health Card'); //web title
        SEOTools::setDescription('this is description');
        SEOMeta::addKeyword('this is tags');
        OpenGraph::setTitle('this is seo title');
        SEOMeta::setCanonical('https://meditriangle.com' . request()->getPathInfo());
        $healths = HealthCard::where('status',1)->get()->first();
        return view('frontend.health-card.index',compact('healths'));
    }
    function healthCardStore(Request $request){

        $request->validate([
            'name' => 'required',
            'number' => 'required|numeric|digits:11',
            'address' => 'required',
        ],[
            'name'=>'Please Input Name!',
            'number'=>'Please Input Phone Numebr!',
            'number.numeric'=>'Please Input Numebr Type!',
            'number.digits'=>'Number Should Be 11 Digits!',
            'address'=>'Please Input Address!',
        ]);
        if($request->pass_nid_number){
            $request->validate([
                'pass_nid_number' =>'max:17',
            ],[
                'pass_nid_number.max'=>'Maximu 17 Digits!',

            ]);

            $application = new HealthCardApplicaton();
            $application->name = $request->name;
            $application->number = $request->number;
            $application->address = $request->address;
            $application->passport_nid = $request->pass_nid_number;
            $application->slug = 'MT-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 6);
            $application->status = "PROCESSING";
            $application->save();
            return redirect(route('thank.you'));

        }else{
            $application = new HealthCardApplicaton();
            $application->name = $request->name;
            $application->number = $request->number;
            $application->address = $request->address;
            $application->slug = 'MT-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 6);
            $application->status = "PROCESSING";
            $application->save();
            return redirect(route('thank.you'));
        }
    //    return back();
    }
    public function hospitalDetails($slug){
        $hospital = HospitalModel::where('slug',$slug)->first();
        return view('frontend.hospital.hospital_details',[
            'hospital' => $hospital
        ]);
    }
    public function findDoctor(){
        $departments = DepartmentModel::where('status',1)->get();
        $doctors = DoctorModel::where('status',1)->get();
        return view('frontend.doctor.doctor_filter',[
            'departments' => $departments,
            'doctors' => $doctors
        ]);
    }
    public function department_result($id) {
        $doctors = DoctorModel::with(['con_department', 'con_hospital'])
                              ->where('department_id', $id)
                              ->where('status', 1)
                              ->get();
        return response()->json($doctors);
    }
    public function search_result($search) {
        $doctors = DoctorModel::with(['con_department', 'con_hospital'])
        ->where('name', 'like' , "%$search%")
        ->where('status', 1)
        ->get();
        return response()->json($doctors);
    }


    function hctc(){
        SEOMeta::setTitle('Health Card Terms And Conditions'); //web title
        SEOTools::setDescription('this is description');
        SEOMeta::addKeyword('this is tags');
        OpenGraph::setTitle('this is seo title');
        SEOMeta::setCanonical('https://meditriangle.com' . request()->getPathInfo());
        return view('frontend.health-card-terms-and-conditions');
    }
    function privacypolicy(){
        SEOMeta::setTitle('Privacy Policy'); //web title
        SEOTools::setDescription('this is description');
        SEOMeta::addKeyword('this is tags');
        OpenGraph::setTitle('this is seo title');
        SEOMeta::setCanonical('https://meditriangle.com' . request()->getPathInfo());
        return view('frontend.privacyandpolicy');
    }
    function terms(){
        SEOMeta::setTitle('Terms And Conditions'); //web title
        SEOTools::setDescription('this is description');
        SEOMeta::addKeyword('this is tags');
        OpenGraph::setTitle('this is seo title');
        SEOMeta::setCanonical('https://meditriangle.com' . request()->getPathInfo());
        return view('frontend.terms-and-condition');
    }
    function aboutus(){
        SEOMeta::setTitle('About Us'); //web title
        SEOTools::setDescription('this is description');
        SEOMeta::addKeyword('this is tags');
        OpenGraph::setTitle('this is seo title');
        SEOMeta::setCanonical('https://meditriangle.com' . request()->getPathInfo());
        return view('frontend.aboutus');
    }
}
