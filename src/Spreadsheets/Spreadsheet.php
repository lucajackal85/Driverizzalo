<?php


namespace Jackal\Driverizzalo\Spreadsheets;


use Google_Service_Sheets;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_CellData;
use Google_Service_Sheets_CellFormat;
use Google_Service_Sheets_ClearValuesRequest;
use Google_Service_Sheets_GridRange;
use Google_Service_Sheets_RepeatCellRequest;
use Google_Service_Sheets_Request;
use Google_Service_Sheets_Sheet;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_SpreadsheetProperties;
use Google_Service_Sheets_TextFormat;
use Google_Service_Sheets_ValueRange;
use Jackal\Driverizzalo\Model\Credentials;

class Spreadsheet extends BaseSpreadsheet
{
    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function create($documentName)
    {
        $requestBody = new Google_Service_Sheets_Spreadsheet();
        $props = new Google_Service_Sheets_SpreadsheetProperties();

        $props->title = $documentName;

        $requestBody->setProperties($props);

        $this->spreadsheet = $this->getService()->spreadsheets->create($requestBody);
        $this->currentSheet = $this->getSpreadsheet()->getSheets()[0]->getProperties()->getTitle();
        return $this;
    }

    public function find($spreadsheetId){

        $this->spreadsheet = $this->getService()->spreadsheets->get($spreadsheetId);
        $this->currentSheet = $this->getSpreadsheet()->getSheets()[0]->getProperties()->getTitle();
        return $this;
    }

    public function setSheet($name){
        $this->assertSheetExists($name);
        $this->currentSheet = $name;
    }

    public function setTitle($title){
        return $this->update([
            new Google_Service_Sheets_Request([
                'updateSpreadsheetProperties' => [
                    'properties' => [
                        'title' => $title
                    ],
                    'fields' => 'title'
                ]
            ]),
        ]);
    }

    public function addSheet($name){

        $response = $this->update([
            new Google_Service_Sheets_Request([
                'addSheet' => [
                    'properties' => [
                        'title' => $name
                    ],
                ]
            ])
        ]);

        $this->currentSheet = $name;

        return $this;
    }

    /**
     * @param $name
     * @return Spreadsheet
     */
    public function addSheetIfNotExists($name){
        try {
            if ($this->getSheetByName($name)) {
                $this->currentSheet = $name;
                return $this;
            }
        }catch (\Exception $e){
            $this->addSheet($name);
            return $this;
        }
    }


    public function removeSheet($name){
        $this->assertSheetExists($name);

        return $this->update([
            new Google_Service_Sheets_Request([
                'deleteSheet' => [
                    'sheetId' => $this->getSheetByName($name)->getSheetId(),
                ]
            ])
        ]);
    }

    public function clearSheet($name){
        $this->assertSheetExists($name);

        $requestBody = new Google_Service_Sheets_ClearValuesRequest();
        return $this->getService()->spreadsheets_values->clear($this->getSpreadsheet()->getSpreadsheetId(), $name, $requestBody);

        $this->currentSheet = $name;
    }

    public function resizeColumn($sheetName,$column,$size){
        return $this->update([
            new Google_Service_Sheets_Request([
                'updateDimensionProperties' => [
                    'range' => [
                        'sheetId' => $this->getSheetByName($sheetName)->getSheetId(),
                        'dimension' => "COLUMNS",
                        'startIndex' => 0,
                        'endIndex' => 1
                    ],
                    'properties' => [
                        'pixelSize' => $size
                    ],
                    'fields' => 'pixelSize'
                ]
            ]),
        ]);
    }

    /**
     * @param $sheetName
     * @param array $values
     * @param int $row
     * @param string $column
     * @return Spreadsheet
     * @throws \Exception
     */
    public function write(array $values,$row = 1,$column = 'A',$sheetName = null){

        $sheetName = $sheetName == null ? $this->currentSheet : $sheetName;

        $this->assertIsValidColumn($column);

        $this->assertSheetExists($sheetName);
        $range = $sheetName.'!'.$column.$row;

        $requestBody = new Google_Service_Sheets_ValueRange();
        $requestBody->setValues([
            'values' => $values,
        ]);

        $this->writeRequests[] = [
            'range' => $range,
            'request_body' => $requestBody
        ];

        return $this;

    }

    public function backgroundRow($red,$green,$blue,$row =1,$column = 'A',$sheetName){
        $sheetName = $sheetName == null ? $this->currentSheet : $sheetName;

        $this->assertIsValidColumn($column);
        $this->assertSheetExists($sheetName);

        $myRange = [
            'sheetId' => $this->getSheetByName($sheetName)->getSheetId(),
            'startRowIndex' => ($row -1),
            'endRowIndex' => $row,
            'startColumnIndex' => $this->getColumnIndex($column) - 1,
        ];

        $format = [
            "backgroundColor" => [
                "red" =>  $red / 255,
                "green" =>  $green / 255,
                "blue" =>  $blue / 255,
                "alpha" =>  1,
            ],
        ];

        $this->update([
            new \Google_Service_Sheets_Request([
                'repeatCell' => [
                    'fields' => 'userEnteredFormat.backgroundColor',
                    'range' => $myRange,
                    'cell' => [
                        'userEnteredFormat' => $format,
                    ],
                ],
            ])
        ]);
    }


}