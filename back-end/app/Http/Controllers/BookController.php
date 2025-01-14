<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportBook;
use App\Exports\ExportBookType;


class BookController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api',['except' => ['exportBookLink','exportBook','exportBookTypeLink','exportBookType','getBookTypeSearch','getAllBooks','getOneBook','addBook','updateBook','deleteBook','changeStatus']]);
    }

    public function exportBookLink(){
        return response()->json(['url' => "http://localhost:8000/book/exportBook"]);
    }
    public function exportBook(){
        return Excel::download(new ExportBook, 'book.xlsx');
    }
    public function exportBookTypeLink(){
        return response()->json(['url' => "http://localhost:8000/book/exportBookType"]);
    }
    public function exportBookType(){
        return Excel::download(new ExportBookType, 'book_type.xlsx');
    }
    public function getAllBooks(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $books = Book::all();
        }
        else{
            $books = Book::where('status','Active')->get();
        }
        return response()->json([
            'books'=>$books
        ], 200);  
    }
    
    public function getOneBook(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:book,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);      
        }
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $book = Book::find($request->id);
        }
        else{
            $book = Book::where('status','Active')->where('id',$request->id)->first();
        }
        return response()->json([
            'book'=>$book
        ], 200);
       
    }
    public function getBookTypeSearch(Request $request){
        if($request->id!=="allBook"){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:book_type,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);      
        }
       
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $book = Book::where('type',$request->id)->get();
        }
        else{
            $book = Book::where('status','Active')->where('type',$request->id)->get();
        }
        return response()->json([
            'bookTypeSearch'=>$book
        ], 200);
        }else{
            $login = auth()->user();
            if($login && $login->is_admin == true){
                $books = Book::all();
            }
            else{
                $books = Book::where('status','Active')->get();
            }
            return response()->json([
                'bookTypeSearch'=>$books
            ], 200);  
        }
    }
    public function addBook(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:1|max:255|unique:book,name',
                'Initial_price'=>'required|numeric|min:0',
                'promotion'=>'required|numeric|between:0,100',
                'image'=>'required|image|mimes:png,jpeg,jpg',
                'type'=>'required|exists:book_type,id',
                'page_number'=>'required|numeric|min:1',
                'author'=>'required|min:1|max:255',
                'status'=>'required|in:Active,Block',
                'quantity'=>'required|numeric|min:1',
                'description'=>'required'
            ],[
                'name.required' => 'Tên sách không để trống',
                'name.max' => 'Tên sách không quá 255 kí tự',
                'name.unique' => 'Tên này đã tồn tại',
                'Initial_price.required' => 'Giá tiền không để trống',
                'image.image' => 'Hãy chọn hình ảnh',
                'image.mimes' => 'Hãy chọn hình ảnh có đuôi là PNG, JPG, JPEG',
                'type.required' => 'Hãy chọn loại sách',
                'page_number.required' => 'Số trang sách không để trống',
                'author.required' => 'Tên tác giả không để trống',
                'quantity.required' => 'Số lượng sách không để trống',
                'description.required' => 'Mô tả sách không để trống',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            if($request->hasfile('image')) {
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'book';
                if (!file_exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0775, true);
                }       
                $file = $request->file('image');
                $date = now('Asia/Ho_Chi_Minh');
                $date = $date->format('d-m-Y-H-i-s');
                $extension = $file->extension();
                $newImageName = Str::slug('book_img', '_').'_'.$date.'.'.$extension;
                $file->move(public_path().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'book', $newImageName);
                $linkFile = $request->getSchemeAndHttpHost().'/'.'upload'.'/'.'images'.'/'.'book'.'/'.$newImageName;
            }
            $book = new Book();
            $book->name = $request->name;
            $book->Initial_price = $request->Initial_price;
            $book->promotion = $request->promotion;
            $request->Initial_price < 1 ?
            $book->promotion_price = round($request->Initial_price - $request->promotion/100*$request->Initial_price, 1) :
            $book->promotion_price = round($request->Initial_price - $request->promotion/100*$request->Initial_price);
            $book->image = $newImageName;
            $book->type = $request->type;
            $book->page_number = $request->page_number;
            $book->author = $request->author;
            $book->status = $request->status;
            $book->quantity = $request->quantity;
            $book->description = $request->description;
            $book->created_at =  Carbon::now('Asia/Ho_Chi_Minh');
            $book->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            $book->save();
            return response()->json([
                'success'=>1,
                'book'=>$book,
            ], 201);
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
       
    }
    public function updateBook(Request $request){
        $login = auth()->user();
        if($login && $login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:book,id',
                'name' => 'max:255',
                'Initial_price'=>'numeric|min:0',
                'promotion'=>'numeric|between:0,100',
                'image'=>'image|mimes:png,jpeg,jpg',
                'type'=>'exists:book_type,id',
                'page_number'=>'numeric|min:1',
                'status'=>'in:Active,Block',
                'author'=>'max:255',
                'quantity'=>'numeric|min:1',
                'description'=>''

            ],[
                'name.max' => 'Tên sách không quá 255 kí tự',
                'name.unique' => 'Tên này đã tồn tại',
                'image.image' => 'Hãy chọn hình ảnh',
                'image.mimes' => 'Hãy chọn hình ảnh có đuôi là PNG, JPG, JPEG',
                'page_number.min' => 'Số trang sách ít nhất là 1 trang',
                'author.required' => 'Tên tác giả không để trống',
                'quantity.min' => 'Số lượng sách ít nhất 1 trang',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
           
            $book = Book::find($request->id);
            if($book->name == $request->name || $request->name == null){
                $book->name = $book->name;
            }
            else{
                $validator = Validator::make($request->all(), [
                    'name' => 'unique:book,name',
                ]);
                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 400);      
                }
                $book->name = $request->name;
            }
            if($request->hasfile('image')) {
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'book';
                if (!file_exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0775, true);
                }       
                $file = $request->file('image');
                $date = now('Asia/Ho_Chi_Minh');
                $date = $date->format('d-m-Y-H-i-s');
                $extension = $file->extension();
                $newImageName = Str::slug('book_img', '_').'_'.$date.'.'.$extension;
                $file->move(public_path().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'book', $newImageName);
                $linkFile = $request->getSchemeAndHttpHost().'/'.'upload'.'/'.'images'.'/'.'book'.'/'.$newImageName;
            }
            $request->Initial_price == null ? $book->Initial_price = $book->Initial_price : $book->Initial_price = $request->Initial_price;
            $request->promotion == null ? $book->promotion = $book->promotion : $book->promotion = $request->promotion;
            $book->Initial_price < 1 ?
            $book->promotion_price = round($book->Initial_price - $book->promotion/100*$book->Initial_price, 1) :
            $book->promotion_price = round($book->Initial_price - $book->promotion/100*$book->Initial_price);
            if($request->hasfile('image')){
                File::delete($destinationPath.'/'.$book->image);
                $book->image = $newImageName;
            }
            else{
                $book->image = $book->image;
            }
            $request->type == null ? $book->type = $book->type : $book->type = $request->type;
            $request->page_number == null ? $book->page_number = $book->page_number : $book->page_number = $request->page_number;
            $request->author == null ? $book->author = $book->author : $book->author = $request->author;
            $request->status == null ? $book->status = $book->status : $book->status = $request->status;
            $request->description == null ? $book->description = $book->description : $book->description = $request->description;
            $book->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            $book->save();
            return response()->json([
                'success'=>1,
                'book'=>$book,
            ], 200);
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
    }
    public function deleteBook(Request $request){
        $login = auth()->user();
        if($login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:book,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            $book = Book::find($request->id);
            $destinationPath = public_path().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'book_images';
            File::delete($destinationPath.'/'.$book->image);
            $book->delete();
            $cart = Cart::where('product_id',$request->id)->where('type','book')->delete();
                return response()->json([
                    'success'=>1,
                    'description'=>'xóa thành công'
                ], 200);
        }
        else{
            return response()->json([
                'error'=>1,
                'description'=>'account login is not admin',
            ], 401);
        }
    }
    public function changeStatus(Request $request){
        $login = auth()->user();
        if($login->is_admin == true){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:book,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);      
            }
            $book = Book::find($request->id);
            if($book->status == 'Active'){
                $book->status = 'Block';
                $book->save();
                return response()->json([
                    'success'=>1,
                    'book'=>$book,
                ], 200);
            }
            else{
                $book->status = 'Active';
                $book->save();
                return response()->json([
                    'success'=>1,
                    'book'=>$book,
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