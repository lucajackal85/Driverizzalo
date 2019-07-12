<?php


namespace Jackal\Driverizzalo\Spreadsheets;


use Google_Service_Sheets;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Sheet;
use Google_Service_Sheets_Spreadsheet;

abstract class BaseSpreadsheet
{
    protected $credentials;
    protected $client;
    protected $spreadsheet;
    protected $currentSheet;

    protected $requests = [];
    protected $writeRequests = [];
    protected $columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

    protected function getClient(){
        if(!$this->client){
            $this->client = (new Client($this->credentials))->getClient();
        }

        return $this->client;
    }

    protected function getService(){
        return new Google_Service_Sheets($this->getClient());
    }

    protected function update($request){
        $update = $this->getService()->spreadsheets->batchUpdate($this->getSpreadsheet()->getSpreadsheetId(), $request);
        $this->spreadsheet = $this->getService()->spreadsheets->get($this->spreadsheet->getSpreadsheetId());
    }


    /**
     * @return Google_Service_Sheets_Spreadsheet
     */
    public function getSpreadsheet(): Google_Service_Sheets_Spreadsheet
    {
        return $this->spreadsheet;
    }

    protected function getSheetByName($name)
    {
        /** @var Google_Service_Sheets_Sheet $sheet */
        foreach ($this->getSpreadsheet()->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() == $name) {
                return $sheet->getProperties();
            }
        }

        return null;
    }

    protected function assertSheetExists($name){
        if($this->getSheetByName($name) == null){
            throw new \Exception(sprintf('Sheet "%s" does not exists',$name));
        }
    }

    protected function assertIsValidColumn($column){
        foreach (str_split($column) as $columnName){
            if(!in_array($columnName,$this->columns)){
                throw new \Exception(sprintf('Column name "%s" not valid',$column));
            }
        }
    }

    protected function getColumnIndex($column){
        $offsetColumns = (strlen($column) - 1) * count($this->columns);
        $lastValue = substr($column,-1);
        $index = (array_flip($this->columns)[$lastValue]) + 1;
        return $index + $offsetColumns;
        //exit;
    }
}