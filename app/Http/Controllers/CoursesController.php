<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use DB;
use Validator;
use Redirect;
use Illuminate\Http\Request;
use App\Models\Courses;
use Intervention\Image\ImageServiceProvider;
use Intervention\Image\Facades\Image;
class CoursesController extends Controller
{
    
  
            public function courses_view()
            {
                $getDynamic = [

                    'title'=>'Home | Admin Panel'
                ];
                return view('front-end.layouts.index' ,$getDynamic);
            }

            public function courses_view_list()
            
            {
                $courses  = Courses::all();
                $getDynamic = [
                    'title'=>'Courses List | Admin Panel',
                    'courseID' => 'Course ID',
                    'courseImage' =>'Course Image',
                    'courseName' =>'Course Name',
                    'courseDesc' =>'Course Description',
                    'coursesAction' =>'Actions',
                    'add_new' => 'Create Course',
                ];
                return view('front-end.courses-view' ,$getDynamic ,compact('courses'));
            }



           

            public function courseAdd()
    {
        $getDynamic = [
            'title' => 'Add Courses | Admin Panel',
            'courses_add' => 'Courses',
            'add' => 'Add',
            'courses_name' =>'Course Name',
            'course_desc' =>'Course Description',
            'course_media'=>'Course Media',
            'course_price'=>'Courses Pricing',
            'courses_form' =>'Courses Form',
            'course_details' =>'Courses Details',
            
    ];
        

        return view('front-end.courses-add' ,$getDynamic);
    }

    public function courseCreate(Request $request)
    {
        $getDynamic =['title' => 'Course Create | Admin Panel'];
       $rules = array(

            'course_name' => 'required',
            'course_price'=>'required',
            'course_desc' => 'required',
            'course_image'=> 'required',
       );
    
         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
             return Redirect::back()->withInput()->withErrors($validator);
         }

         $course = new Courses;
         $course->course_name = $request->course_name;
         $course->course_price = $request->course_price;  
         $course->course_desc = $request->course_desc;
         if($request->hasFile('course_image')){
            $image = $request->file('course_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(600, 600)->save( public_path('storage/uploads/'. $filename ));
            $course->course_image = $filename;
            $course->save();
          };
         $course->save();
        return redirect()->route('courses-view')
                        ->with('success','Course created successfully.' ,$getDynamic);
       
    }
            public function destroyCourse($id)
            {
                Courses::destroy($id);
                return response()->json([
                    'success'=>'Course Delete Succesfully'
                ]);

            }

            public function instructors_view()
            {
                $getDynamic =['title' => 'Instructord | Admin Panel'];
                return view('front-end.instructors-add' , $getDynamic);

            }
}
