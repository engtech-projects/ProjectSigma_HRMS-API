<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class EmployeeBulkUploadController extends Controller
{
    const START_ROW = 3;
    const HEADER_KEYS = [
        '','','','','','','','','','','','',
        '','','','','','','','','','','','',
        '','','','','','','','','','','','',
        '','','','','','','','','','','','',
        '','','','','','','','','','','','',
        '','','','','','','','','','','','',
        '','','','','','','','','','','','',
        '','','','','','',
    ];
    public function bulkUpload (Request $request) {
        // if file is not chosen, redirect back
        if(!request()->hasFile('employees-data')) {
            return response()->json([ 'message' => 'no excel file'], 422);
        }

        $file = $request->file('employees-data');
        $extension = strtolower($file->getClientOriginalExtension());
        // check extensions
        if (!in_array($extension, ['xls', 'xlsx']))
        {
            return response()->json([ 'message' => 'invalid file'], 422);
        }
        $extractedData = [];
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $totalData = $worksheet->getHighestDataRow() - Self::START_ROW;
        $headerColumnKey = 'A'.(Self::START_ROW - 1).':CM'.(Self::START_ROW - 1);
        $header = $worksheet->rangeToArray($headerColumnKey, null, true, true);
        for($x = 0; $x < $totalData; $x++) {
            $headCounter = 0;
            $tempData = [];
            $columnKey = 'A'.(Self::START_ROW + $x).':CM'.(Self::START_ROW + $x);
            $extractData = $worksheet->rangeToArray($columnKey, null, true, true);

            foreach($extractData as $data)
            {
                foreach(Self::HEADER_KEYS as $index)
                {
                    $tempData[Str::snake($index)] = $data[$headCounter];
                    $headCounter++;
                }
            }
            $extractedData[] = $tempData;
        }
        return $extractedData;
    }
    public function bulkSave(Request $request){
        //save
    }
}
