<?php

namespace App\Http\Controllers;

use App\Services\UploadFileService;
use Carbon\Carbon;

class UploadFileController extends Controller
{

    // services
    public  $uploadFileService =null;

    public function __construct(){
        // initialize benefiters list service
        $this->uploadFileService = new UploadFileService();
    }

    // returns upload csv view
    public function getUploadCSV(){
        $importedCSVFiles_basic_info = $this->uploadFileService->findImportedHistory();
        return view('uploadExcel')->with('importedCSVFiles_basic_info', $importedCSVFiles_basic_info);
    }

    // uploads the file to server
    public function excelUpload(\Illuminate\Http\Request $request){
        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:csv,txt']]);
        $uploadFileService = new UploadFileService();
        $file = $request->file('file');
        // The file goes to the private storage, with a random name, never to public/.
        $storedPath = $file->store('csv-imports', 'local');
        abort_if($storedPath === false, 500, 'The file could not be stored.');
        $fullFilePath = \Illuminate\Support\Facades\Storage::disk('local')->path($storedPath);
        // Insert upload main info (file name & date) to DB
        $uploadFileService->importedFilesTable($file->getClientOriginalName());
        // insert values to DB
        return $uploadFileService->fileImport($fullFilePath);
    }
}
