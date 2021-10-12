<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Content;
use App\Models\TableOfContent;
use App\Models\Course;
use App\Models\CategoryCourse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api',['except' => ['getAllContents','getOneContent','addNewContent','updateContent','deleteContent','changeStatusContent']]);
    }
    public function getAllContents(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $contents = Content::all();
        }
        else{
            $contents = Content::where('status','Active')->get();
            foreach($contents as $i=>$content){
                $table_content = TableOfContent::find($content->table_of_content_id);
                if($table_content && $table_content->status == 'Active'){
                    $course = Course::find($table_content->course_id);
                    if($course && $course->status == 'Active'){
                        $category_course = CategoryCourse::find($course->category_course);
                        if($category_course && $category_course->status == 'Block'){
                            unset($contents[$i]);
                        }
                    }
                    else{
                        unset($contents[$i]);
                    }
                }
                else{
                    unset($contents[$i]);
                }
            }
        }
        return response()->json([
            'contents'=>$contents
        ], 200);
    }
    public function getOneContent(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:content,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);      
        }
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $content = Content::find($request->id);
        }
        else{
            $content = Content::where('status','Active')->where('id',$request->id)->get();
            foreach($content as $i=>$c){
                $table_content = TableOfContent::find($c->table_of_content_id);
                if($table_content && $table_content->status == 'Active'){
                    $course = Course::find($table_content->course_id);
                    if($course && $course->status == 'Active'){
                        $category_course = CategoryCourse::find($course->category_course);
                        if($category_course && $category_course->status == 'Block'){
                            unset($content[$i]);
                        }
                    }
                    else{
                        unset($content[$i]);
                    }
                }
                else{
                    unset($content[$i]);
                }
            }
        }
        return response()->json([
            'content'=>$content
        ], 200);
    }
    public function addNewContent(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:1|max:255',
                'table_of_content_id'=>'required|exists:table_of_content,id',
                'status'=>'required|in:Active,Block'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }

            $contents = Content::where('table_of_content_id',$request->table_of_content_id)->get();
            foreach($contents as $content){
                if(mb_strtolower($content->name,'UTF-8') === mb_strtolower($request->name,'UTF-8')){
                    return response()->json(['error'=>'The name must unique'], 400);   
                }
            }

            $content = new Content();
            $content->name = $request->name;
            $content->table_of_content_id = $request->table_of_content_id;
            $content->status = $request->status;
            $content->created_at =  Carbon::now('Asia/Ho_Chi_Minh');
            $content->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            $content->save();
            return response()->json([
                'success'=>1,
                'content'=>$content,
            ], 201);
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
    }
    public function updateContent(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:content,id',
                'name' => 'min:1|max:255',
                'table_of_content_id'=>'exists:table_of_content,id',
                'status'=>'in:Active,Block'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            
            $content = Content::find($request->id);
            if($content->name == $request->name || $request->name == null || $request->name == 'undefined'){
                $content->name = $content->name;
            }
            else{
                $content->name = $request->name;
            }
            $request->table_of_content_id == null  || $request->table_of_content_id == 'undefined'
            ? $content->table_of_content_id = $content->table_of_content_id 
            : $content->table_of_content_id = $request->table_of_content_id;
            
            $contents = Content::where('table_of_content_id',$content->table_of_content_id)->get();
            foreach($contents as $c){
                if($c->id != $content->id && mb_strtolower($content->name,'UTF-8') === mb_strtolower($c->name,'UTF-8')){
                    return response()->json(['error'=>'The name must unique'], 400);   
                }
            }
            $request->status == null || $request->status == 'undefined'
            ? $content->status = $content->status 
            : $content->status = $request->status;
            $content->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            $content->save();
            return response()->json([
                'success'=>1,
                'content'=>$content,
            ], 201);
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
    }
    public function deleteContent(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:content,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            $content = Content::find($request->id);
            $lesson = Lesson::where('content_id',$request->id)->delete();
            $content->delete();
                return response()->json([
                    'success'=>1,
                    'description'=>'deleted'
                ], 200);
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
    }
    public function changeStatusContent(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:content,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            $content = Content::find($request->id);
            if($content->status == 'Active'){
                $content->status = 'Block';
                $content->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
                $content->save();
                return response()->json([
                    'success'=>1,
                    'content'=>$content,
                ], 200);
            }
            else{
                $content->status = 'Active';
                $content->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
                $content->save();
                return response()->json([
                    'success'=>1,
                    'content'=>$content,
                ], 200);
            }
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
    }
}