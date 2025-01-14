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
use App\Models\Lesson;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\momoOrderDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportUser;
use App\Exports\ExportTeacher;
class TeacherController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['exportTeacherLink','exportTeacher','onLogin','getTeacher', 'onRegister','getCode','getCodeForgotPassword','changePasswordForgot']]);
    }
    
    /**
     * @SWG\GET(
     *     path="api/teacher/getTeacher/",
     *     description="Return teacher's informaion.",
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="image", type="string"),
     *             @SWG\Property(property="position", type="string"),
     *             @SWG\Property(property="description", type="string"),
     *             @SWG\Property(property="phone", type="string"),
     *             @SWG\Property(property="facebook", type="string"),
     *             @SWG\Property(property="skype", type="string"),
     *             @SWG\Property(property="youtube", type="string"),
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
    public function getTeacher()
    {
        $teacherFind = DB::table('teacher')->get();
        return Response()->json(array("Successfully"=> 1,"data"=>$teacherFind ));
    }
    public function exportTeacherLink(){
        return response()->json(['url' => "http://localhost:8000/teacher/exportTeacher"]);
    }
    public function exportTeacher(){
        return Excel::download(new ExportTeacher, 'teacher.xlsx');
    }
/**
     * @SWG\POST(
     *     path="api/teacher/createTeacher/",
     *     description="Return teacher's informaion.",
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="Your name",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="image",
     *         in="query",
     *         type="file",
     *         description="Your image",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="position",
     *         in="query",
     *         type="string",
     *         description="Your position",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="Your phone",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="description",
     *         in="query",
     *         type="string",
     *         description="Your description",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="facebook",
     *         in="query",
     *         type="string",
     *         description="Your facebook",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="skype",
     *         in="query",
     *         type="string",
     *         description="Your skype",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="youtube",
     *         in="query",
     *         type="string",
     *         description="Your youtube",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="image", type="string"),
     *             @SWG\Property(property="position", type="string"),
     *             @SWG\Property(property="description", type="string"),
     *             @SWG\Property(property="phone", type="string"),
     *             @SWG\Property(property="facebook", type="string"),
     *             @SWG\Property(property="skype", type="string"),
     *             @SWG\Property(property="youtube", type="string"),
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
    public function createTeacher(Request $request){
        $adminFind = auth()->user();
        if (($adminFind->email==="web.vatly365@gmail.com")){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'position' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'description' => 'required',
            'phone' => 'required|numeric|starts_with:0|digits_between:10,12',
            'facebook' => 'required',
            'skype' => 'required',
            'youtube' => 'required'
        ],[
            'name.required' => 'Tên giáo viên không để trống',
            'name.max' => 'Tên giáo viên không quá 255 kí tự',
            'image.image' => 'Hãy chọn hình ảnh',
            'image.mimes' => 'Hãy chọn hình ảnh có đuôi là PNG, JPG, JPEG',
            'position.required' => 'Không được bỏ trống',
            'description.required' => 'Không được bỏ trống',
            'facebook.required' => 'Không được bỏ trống',
            'skype.required' => 'Không được bỏ trống',
            'youtube.required' => 'Không được bỏ trống',
            'phone.required' => 'Không được bỏ trống',
            'phone.numeric' => 'Số điện thoại chỉ chứa chữ số',
            'phone.starts_with' => 'Số điện thoại phải bắt đầu từ số 0',
            'phone.digits_between' => 'Số điện thoại phải từ 10 tới 12 số',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);     
        }
        if ($request->hasFile('image'))
        {
              $file      = $request->file('image');        
              $filename  = $file->getClientOriginalName();
              $extension = $file->getClientOriginalExtension();
              $picture   = $filename;
              $file->move('upload\images\teacher', $picture);
              $postArray = [
                    'name'  => $request->name,
                    'position'  => $request->position,
                    'image'  => $picture,
                    'phone'  => $request->phone,
                    'description'  => $request->description,
                    'facebook'  => $request->facebook,
                    'skype'  => $request->skype,
                    'youtube'     => $request->youtube,
                    'created_at'=> Carbon::now('Asia/Ho_Chi_Minh'),
                    'updated_at'=> Carbon::now('Asia/Ho_Chi_Minh')
                ];
                 $teacher = Teacher::create($postArray);
              return Response()->json(array("Successfully. Upload successfully!"=> 1,"data"=>$postArray ));
        } 
        else
        {
              return response()->json(["message" => "Upload Failed"]);
        }
    }
    else{
        return response()->json([
            'error' => 'admin not found'
        ], 401); 
    }
    }

    public function getOneTeacher(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:teacher,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);      
        }
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $book = Teacher::find($request->id);
        }
        else{
            $book = Teacher::where('status','Active')->where('id',$request->id)->first();
        }
        return response()->json([
            'data'=>$book
        ], 200);
       
    }
/**
     * @SWG\POST(
     *     path="api/teacher/updateTeacher/{id}",
     *     description="Return teacher's informaion.",
     *  @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         type="string",
     *         description="Your name",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="image",
     *         in="query",
     *         type="file",
     *         description="Your image",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="position",
     *         in="query",
     *         type="string",
     *         description="Your position",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         type="string",
     *         description="Your phone",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="description",
     *         in="query",
     *         type="string",
     *         description="Your description",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="facebook",
     *         in="query",
     *         type="string",
     *         description="Your facebook",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="skype",
     *         in="query",
     *         type="string",
     *         description="Your skype",
     *         required=true,
     *     ),
     * @SWG\Parameter(
     *         name="youtube",
     *         in="query",
     *         type="string",
     *         description="Your youtube",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="image", type="string"),
     *             @SWG\Property(property="position", type="string"),
     *             @SWG\Property(property="description", type="string"),
     *             @SWG\Property(property="phone", type="string"),
     *             @SWG\Property(property="facebook", type="string"),
     *             @SWG\Property(property="skype", type="string"),
     *             @SWG\Property(property="youtube", type="string"),
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
public function updateTeacher($id,Request $request){
    $adminFind = auth()->user();
    if (($adminFind->email==="web.vatly365@gmail.com")){
    $validator = Validator::make($request->all(), [
        'name' => 'max:255',
        'position' => '',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
        'description' => '',
        'phone' => 'numeric|starts_with:0|digits_between:10,12',
        'facebook' => '',
        'skype' => '',
        'youtube' => ''
    ],[
        'name.max' => 'Tên giáo viên không quá 255 kí tự',
        'image.mimes' => 'Hãy chọn hình ảnh có đuôi là PNG, JPG, JPEG',
        'phone.numeric' => 'Số điện thoại chỉ chứa chữ số',
        'phone.starts_with' => 'Số điện thoại phải bắt đầu từ số 0',
        'phone.digits_between' => 'Số điện thoại phải từ 10 tới 12 số',
    ]);
    if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()], 401);     
    }
    $teacherFind = Teacher::find($id);
    if($teacherFind){
    $image=$teacherFind->image;
    $created_at=$teacherFind->created_at;
    if ($request->name==null){
        $name=$teacherFind->name;
    }else{
        $name=$request->name;
    }

    if ($request->position==null){
        $position=$teacherFind->position;
    }else{
        $position=$request->position;
    }
    if ($request->phone==null){
        $phone=$teacherFind->phone;
    }else{
        $phone=$request->phone;
    }
    if ($request->description==null){
        $description=$teacherFind->description;
    }else{
        $description=$request->description;
    }
    if ($request->facebook==null){
        $facebook=$teacherFind->facebook;
    }else{
        $facebook=$request->facebook;
    }
    if ($request->skype==null){
        $skype=$teacherFind->skype;
    }else{
        $skype=$request->skype;
    }
    if ($request->youtube==null){
        $youtube=$teacherFind->youtube;
    }else{
        $youtube=$request->youtube;
    }
    if ($request->hasFile('image'))
    {
          $file      = $request->file('image');
          $filename  = $file->getClientOriginalName();
          $extension = $file->getClientOriginalExtension();
          $picture   = $filename;
          $file->move('upload\images\teacher', $picture);
          $teacherFind->image=$picture;
          $teacherFind->name=$name;
          $teacherFind->description=$description;  
          $teacherFind->position=$position;    
          $teacherFind->phone=$phone;      
          $teacherFind->youtube=$youtube;      
          $teacherFind->skype=$skype;      
          $teacherFind->facebook=$facebook;      
          $teacherFind->created_at=$created_at;      
          $teacherFind->updated_at=Carbon::now('Asia/Ho_Chi_Minh');      
          $teacherFind->save();
          return Response()->json(array("Successfully. Update successfully!"=> 1,"data"=>$teacherFind ));
        }
    else
    {
        $teacherFind->image=$image;
        $teacherFind->name=$name;
        $teacherFind->description=$description;  
        $teacherFind->position=$position;    
        $teacherFind->phone=$phone;      
        $teacherFind->youtube=$youtube;      
        $teacherFind->skype=$skype;      
        $teacherFind->facebook=$facebook;      
        $teacherFind->created_at=$created_at;      
        $teacherFind->updated_at=Carbon::now('Asia/Ho_Chi_Minh');      
        $teacherFind->save();
        return Response()->json(array("Successfully. Update successfully!"=> 1,"data"=>$teacherFind ));
    }
}else{
    return Response()->json(array("error!"=> 401,"message"=>"Id Not Found" ));
}
}
else{
    return response()->json([
        'error' => 'admin not found'
    ], 401); 
}
}

/**
     * @SWG\POST(
     *     path="api/teacher/destroyTeacher/{id}",
     *     description="Return teacher's informaion.",
     *     @SWG\Response(
     *         response=200,
     *         description="Successfully",
     *         @SWG\Schema(
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="image", type="string"),
     *             @SWG\Property(property="position", type="string"),
     *             @SWG\Property(property="description", type="string"),
     *             @SWG\Property(property="phone", type="string"),
     *             @SWG\Property(property="facebook", type="string"),
     *             @SWG\Property(property="skype", type="string"),
     *             @SWG\Property(property="youtube", type="string"),
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
    public function destroyTeacher($id){
        $adminFind = auth()->user();
        if (($adminFind->email==="web.vatly365@gmail.com")){
        $teacherFind= Teacher::find($id);
        if ($teacherFind){
        $teacherFind->delete();
        return response()->json([
        'data' => $teacherFind
    ]);}
    else{
        return response()->json(["message" => "Delete failed"]);
    }
}
else{
    return response()->json([
        'error' => 'admin not found'
    ], 401); 
}
    }
}