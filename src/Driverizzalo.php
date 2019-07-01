<?php


namespace Jackal\Driverizzalo;


use Jackal\Driverizzalo\Model\Credentials;
use Jackal\Driverizzalo\Spreadsheets\Spreadsheet;

class Driverizzalo
{
    protected $credentials;

    public function __construct(Credentials $credetials)
    {
        $this->credentials = $credetials;
    }

    /**
     * @param $documentName
     * @return Spreadsheet
     */
    public function createSpreadsheet($documentName){

        $s = new Spreadsheet($this->credentials);
        return $s->create($documentName);
    }

    /**
     * @param $spreadsheetId
     * @return Spreadsheet
     */
    public function loadSpreadsheet($spreadsheetId){

        $s = new Spreadsheet($this->credentials);
        return $s->find($spreadsheetId);
    }
}