<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api',['except' => ['getAllContents','getOneContent','addNewContent','updateContent','deleteContent','changeStatusContent']]);
    }
    public function getAllContents(Request $request){
        $contents = Content::all();
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

        $content = Content::find($request->id);
        return response()->json([
            'content'=>$content
        ], 200);
    }
    public function addNewContent(Request $request){
        $login = auth()->user();
        if($login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:1|max:255|unique:content,name',
                'table_of_content_id'=>'required|exists:table_of_content,id',
                'status'=>'required|in:Active,Block'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
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
        if($login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:content,id',
                'name' => 'required|min:1|max:255',
                'table_of_content_id'=>'required|exists:table_of_content,id',
                'status'=>'required|in:Active,Block'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            
            $content = Content::find($request->id);
            $content->name = $request->name;
            $content->table_of_content_id = $request->table_of_content_id;
            $content->status = $request->status;
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
        if($login->is_admin == true){
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
        if($login->is_admin == true){
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
