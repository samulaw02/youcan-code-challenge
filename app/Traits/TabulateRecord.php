<?php

namespace App\Traits;

Trait TabulateRecord {
    //tabulate cli records
    public function tabulate(array $headers, $data)
    {
        $tableData = [];
        foreach ($data as $record) {
            $rowData = [];
            foreach ($headers as $header) {
                $rowData[] = isset($record->$header) ? $record->$header : null;
            }
            $tableData[] = $rowData;
        }
        $this->table($headers, $tableData);
    }

}
