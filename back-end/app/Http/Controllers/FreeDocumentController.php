<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsersResource;
use App\Jobs\SendEmail;
use App\Models\User;
use App\Models\Cart;
use App\Models\History;
use App\Models\Teacher;
use App\Models\UserCourse;
use App\Models\FreeDocument;
use App\Models\FreeDocumentCategory;
use App\Models\CommentReply;
use App\Models\momoOrderDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportDocument;
use App\Exports\ExportDocumentCategory;
class FreeDocumentController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['exportDocumentCategory','exportDocumentCategoryLink','exportDocument','exportDocumentLink','getFreeDocumentAdmin','getFreeDocumentAlpha2','getFreeDocumentAlpha','onLogin','getFreeDocument','getTeacher', 'onRegister','getCode','getCodeForgotPassword','changePasswordForgot']]);
    }
    
    public function exportDocumentLink(){
        return response()->json(['url' => "http://localhost:8000/document/exportDocument"]);
    }
    public function exportDocument(){
        return Excel::download(new ExportDocument, 'document.xlsx');
    }
    public function exportDocumentCategoryLink(){
        return response()->json(['url' => "http://localhost:8000/document/exportDocumentCategory"]);
    }
    public function exportDocumentCategory(){
        return Excel::download(new ExportDocumentCategory, 'document_category.xlsx');
    }
    /**
     * @SWG\GET(
     *     path="api/freeDocument/getFreeDocument/",
     *     description="Return teacher's informaion.",
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="category_name", type="string"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="file", type="string"),
     *             @SWG\Property(property="path", type="string"),
     *             @SWG\Property(property="created_at", type="timestamp"),
     *             @SWG\Property(property="updated_at", type="timestamp"),
     *            )
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Missing Data"
     *     ),
     * )
     */

    public function getFreeDocumentAlpha()
    {
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $documentFind = DB::table('free_document')->get();
            return Response()->json(array("Successfully"=> 1,"data"=>$documentFind ));
        }
        else{
            $document=[];
            $category=DB::table('free_document_category')->where('status','Active')->get();
            for ($i=0;$i<count($category);$i++){
                $documentFind = DB::table('free_document')->where('category_id',$category[$i]->id)->where('status','Active')->get();
                for ($j=0;$j<count($documentFind);$j++){
                    if($documentFind[$j]->category_id!==3){
                    array_push($document,$documentFind[$j]);
                    }
                }
            }
        return Response()->json(array("Successfully"=> 1,"data"=>$document ));
        }
    }
    public function getFreeDocumentAlpha2()
    {
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $documentFind = DB::table('free_document')->get();
            return Response()->json(array("Successfully"=> 1,"data"=>$documentFind ));
        }
        else{
            $document=[];
            $category=DB::table('free_document_category')->where('status','Active')->get();
            for ($i=0;$i<count($category);$i++){
                $documentFind = DB::table('free_document')->where('category_id',$category[$i]->id)->where('status','Active')->get();
                for ($j=0;$j<count($documentFind);$j++){
                    if($documentFind[$j]->category_id===3){
                    array_push($document,$documentFind[$j]);
                    }
                }
            }
        return Response()->json(array("Successfully"=> 1,"data"=>$document ));
        }
    }



    public function getFreeDocument()
    {
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $documentFind = DB::table('free_document')->get();
            $dataRespon=[];
            $dataRespon[0]=DB::table('free_document_category')->get();
            $array=[];
            for ($i = 0; $i <count($documentFind); $i++) {
                $data = DB::table('free_document_category')->where('id', $documentFind[$i]->category_id)->first();
                $array[$i]=array(
                    'id' => $documentFind[$i]->id,
                    'category_name' => $data->name,
                    'name' => $documentFind[$i]->name,
                    'file' => $documentFind[$i]->file,
                    'path' => $documentFind[$i]->path,
                    'status' => $documentFind[$i]->status,
                    'created_at'=> $documentFind[$i]->created_at,
                    'updated_at'=> $documentFind[$i]->updated_at,
                );
            }
            $dataRespon[1]=$array;
            return Response()->json(array("Successfully"=> 1,"data"=>$dataRespon ));
        }
        else{
            $documentFind = DB::table('free_document')->where('status','Active')->get();
        $dataRespon=[];
        $dataRespon[0]=DB::table('free_document_category')->where('status','Active')->get();
        $array=[];
        for ($i = 0; $i <count($documentFind); $i++) {
            $data = DB::table('free_document_category')->where('id', $documentFind[$i]->category_id)->first();
            $array[$i]=array(
                'id' => $documentFind[$i]->id,
                'category_name' => $data->name,
                'name' => $documentFind[$i]->name,
                'file' => $documentFind[$i]->file,
                'path' => $documentFind[$i]->path,
                'status' => $documentFind[$i]->status,
                'created_at'=> $documentFind[$i]->created_at,
                'updated_at'=> $documentFind[$i]->updated_at,
            );
        }
        $dataRespon[1]=$array;
        return Response()->json(array("Successfully"=> 1,"data"=>$dataRespon ));
        } 
    }
    public function getFreeDocumentAdmin()
    {
            $documentFind = DB::table('free_document')->get();
            $dataRespon=[];
            $dataRespon[0]=DB::table('free_document_category')->get();
            $array=[];
            for ($i = 0; $i <count($documentFind); $i++) {
                $data = DB::table('free_document_category')->where('id', $documentFind[$i]->category_id)->first();
                $array[$i]=array(
                    'id' => $documentFind[$i]->id,
                    'category_name' => $data->name,
                    'name' => $documentFind[$i]->name,
                    'file' => $documentFind[$i]->file,
                    'path' => $documentFind[$i]->path,
                    'status' => $documentFind[$i]->status,
                    'created_at'=> $documentFind[$i]->created_at,
                    'updated_at'=> $documentFind[$i]->updated_at,
                );
            }
            $dataRespon[1]=$array;
            return Response()->json(array("Successfully"=> 1,"data"=>$dataRespon )); 
    }
    
    /**
     * @SWG\POST(
     *     path="api/freeDocument/createFreeDocument/",
     *     description="Return teacher's informaion.",
     * @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="Document name",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="file",
     *         in="query",
     *         type="file",
     *         description="Document file",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="category",
     *         in="query",
     *         type="integer",
     *         description="Category's id",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="name", type="integer"),
     *             @SWG\Property(property="file", type="string"),
     *             @SWG\Property(property="category_id", type="integer"),
     *             @SWG\Property(property="path", type="string"),
     *             @SWG\Property(property="status", type="string"),
     *             @SWG\Property(property="created_at", type="timestamp"),
     *             @SWG\Property(property="updated_at", type="timestamp"),
     *            )
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Missing Data"
     *     ),
     * security={
     *           {"api_key_security_example": {}}
     *       }
     * )
     */
    public function createFreeDocument(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'file'=>'required',
            'category_id' => 'required|numeric|digits_between:1,12',
        ],[
            'name.required' => 'Phần này không được để trống',
            'name.max' => 'Tên không được vượt quá 255 ký tự',
            'file.required'=>'Phần này không được để trống',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);     
        }
        if ($request->hasFile('file'))
        {
              $file      = $request->file('file');      
              $path      = 'upload\images\free_document';
              $filename  = $file->getClientOriginalName();
              $extension = $file->getClientOriginalExtension();
              $picture   = $filename;
              $file->move('upload\images\free_document', $picture);
              $postArray = [
                    'name'  => $request->name,
                    'file'  => $picture,
                    'category_id'  => $request->category_id,
                    'path'=>$path,
                    'status'=>$request->status,
                    'created_at'=> Carbon::now('Asia/Ho_Chi_Minh'),
                    'updated_at'=> Carbon::now('Asia/Ho_Chi_Minh')
                ];
                 $freeDocument = FreeDocument::create($postArray);
              return Response()->json(array("Successfully. Upload successfully!"=> 1,"data"=>$postArray ));
        } 
        else
        {
              return response()->json(["error" => "Upload Failed"]);
        }
    }
    public function getOneDocument(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:free_document,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);      
        }
            $book = FreeDocument::find($request->id);
        return response()->json([
            'data'=>$book
        ], 200);
       
    }

    /**
     * @SWG\POST(
     *     path="api/freeDocument/updateFreeDocument/{id}",
     *     description="Return free Document's informaion.",
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="Frê Document's Name",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="category",
     *         in="query",
     *         type="integer",
     *         description="Free Document's Type",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="file",
     *         in="query",
     *         type="file",
     *         description="Free Document's File",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *            @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="integer"),
     *             @SWG\Property(property="file", type="string"),
     *             @SWG\Property(property="category_id", type="integer"),
     *             @SWG\Property(property="path", type="string"),
     *             @SWG\Property(property="status", type="string"),
     *             @SWG\Property(property="created_at", type="timestamp"),
     *             @SWG\Property(property="updated_at", type="timestamp"),
     *            )
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Missing Data"
     *     ),
     * security={
     *           {"api_key_security_example": {}}
     *       }
     * )
     */
public function updateFreeDocument($id,Request $request){
    $validator = Validator::make($request->all(), [
        'name' => 'max:255',
        'file'=>'',
    ],[
        'name.max' => 'Tên không được vượt quá 255 ký tự',
    ]);
    if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()], 401);     
    }
    $freeDocument = FreeDocument::find($id);
    if($freeDocument){
    $file=$freeDocument->file;
    $created_at=$freeDocument->created_at;
    if ($request->status==null){
        $status=$freeDocument->status;
    }else{
        $status=$request->status;
    }
    $path=$freeDocument->path;
    if ($request->name==null){
        $name=$freeDocument->name;
    }else{
        $name=$request->name;
    }
    if ($request->category_id==null){
        $category_id=$freeDocument->category_id;
    }else{
        $category_id=$request->category_id;
    }
    if ($request->hasFile('file'))
    {
          $file      = $request->file('file');
          $filename  = $file->getClientOriginalName();
          $extension = $file->getClientOriginalExtension();
          $picture   = $filename;
          $path      = 'upload\images\free_document';
          $file->move('upload\images\free_document', $picture);
          $freeDocument->file=$picture;
          $freeDocument->name=$name;
          $freeDocument->status=$status;  
          $freeDocument->path=$path;          
          $freeDocument->category_id=$category_id;            
          $freeDocument->created_at=$created_at;               
          $freeDocument->updated_at=Carbon::now('Asia/Ho_Chi_Minh');      
          $freeDocument->save();
          return Response()->json(array("Successfully. Update successfully!"=> 1,"data"=>$freeDocument ));
        }
    else
    {  
        $freeDocument->name=$name;      
        $freeDocument->category_id=$category_id;      
        $freeDocument->file=$file;     
        $freeDocument->path=$path;     
        $freeDocument->status=$status;      
        $freeDocument->created_at=$created_at;      
        $freeDocument->updated_at=Carbon::now('Asia/Ho_Chi_Minh');      
        $freeDocument->save();
        return Response()->json(array("Successfully. Update successfully!"=> 1,"data"=>$freeDocument ));
    }
}else{
    return Response()->json(array("error!"=> 401,"message"=>"Id Not Found" ));
}
}

/**
     * @SWG\POST(
     *     path="api/freeDocument/destroyFreeDocument/{id}",
     *     description="Return freeDocument's informaion.",
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="integer"),
     *             @SWG\Property(property="file", type="string"),
     *             @SWG\Property(property="category_id", type="integer"),
     *             @SWG\Property(property="path", type="string"),
     *             @SWG\Property(property="status", type="string"),
     *             @SWG\Property(property="created_at", type="timestamp"),
     *             @SWG\Property(property="updated_at", type="timestamp"),
     *            )
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Missing Data"
     *     ),
     * security={
     *           {"api_key_security_example": {}}
     *       }
     * )
     */
    public function destroyFreeDocument($id){
        $freeDocumentFind= FreeDocument::find($id);
        if ($freeDocumentFind){
        $freeDocumentFind->delete();
        return response()->json([
        'data' => $freeDocumentFind
    ]);}
    else{
        return response()->json(["message" => "Delete failed"]);
    }
    }

    /**
     * @SWG\POST(
     *     path="api/freeDocument/blockActiveFreeDocument/{id}",
     *     description="Return freeDocument's informaion.",
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="integer"),
     *             @SWG\Property(property="file", type="string"),
     *             @SWG\Property(property="category_id", type="integer"),
     *             @SWG\Property(property="path", type="string"),
     *             @SWG\Property(property="status", type="string"),
     *             @SWG\Property(property="created_at", type="timestamp"),
     *             @SWG\Property(property="updated_at", type="timestamp"),
     *            )
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Missing Data"
     *     ),
     * security={
     *           {"api_key_security_example": {}}
     *       }
     * )
     */
    public function blockActiveFreeDocument($id){
            $freeDocumentFind = DB::table('free_document')->where('id', $id)->first();
            if ($freeDocumentFind){ 
                if ($freeDocumentFind->status==="Block"){
                    DB::table('free_document')->where('id', $freeDocumentFind->id)->update(['status'	=>	"Active"]);  
                    $freeDocumentRespon = DB::table('free_document')->where('id', $id)->first();
                    return response()->json([
                        'message' => 'successfully',
                        'user' => $freeDocumentRespon
                    ], 201);
                }
                else{
                    DB::table('free_document')->where('id', $freeDocumentFind->id)->update(['status'	=>	"Block"]);  
                    $freeDocumentRespon = DB::table('free_document')->where('id', $id)->first();
                    return response()->json([
                        'message' => 'successfully',
                        'user' => $freeDocumentRespon
                    ], 201);
                }
            }
            else{
                return response()->json([
                    'error' => 'id not found'
                ], 401); 
            }
    }

}