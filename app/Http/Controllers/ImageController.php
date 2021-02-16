<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Models\Image;
use Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        return view('gallery.index');
    }

    public function main()
    {
        return view('gallery.index');
    }

    public function list()
    {
        return response()->json(Image::where('user_id',Auth::user()->id)->get());
    }

    public function store(StoreImageRequest $request)
    {

        $file = $request->file('files');
        $file_type = $file->getMimeType();
        $file_size = $file->getSize();
        $filename = $file->getClientOriginalName();
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $file_ext = $file->getClientOriginalExtension();
        $new_name = $file_name . '_' . time() . '.' . $file_ext;

        Storage::putFileAs('images', $file, $new_name);

        $image = Image::create([
            'name' => $new_name,
            'type' => $file_type,
            'size' => $file_size,
            'user_id' => Auth::user()->id
        ]);

        return response()->json([
            'success' => 'Upload successfully',
            'files' => $image
        ], 201);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        Storage::disk('images')->delete($image->name);
        $image->delete();
        return response()->noContent();
    }
}
