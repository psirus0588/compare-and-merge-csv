#!/usr/bin/php

<?php

$newCompare = new Compare;
$newCompare->fromTerminal();


class Compare
{
    public $file_price1;
    public $file_price2;
    public $file_price1_col_num;
    public $file_price2_col_num;
    public $filename_output;
    public $params;

    public function __construct()
    {
    }

    public function fromTerminal()
    {

        $this->file_price1 = $this->_printInTerm("Enter csv file name 1:");
        $this->file_price2 = $this->_printInTerm("Enter csv file name 2:");
        $this->file_price1_col_num = $this->_printInTerm("Enter column number 1 for comparsion:");
        $this->file_price2_col_num = $this->_printInTerm("Enter column number 2 for comparsion:");

        $this->params['with_titles'] = $this->_printInTerm("Csv files with titles? (Y/N):");

        $this->filename_output = $this->_printInTerm("Enter outpup csv file name:");

        $this->doWork($this->file_price1,$this->file_price1_col_num,$this->file_price2,$this->file_price2_col_num,$this->filename_output, $this->params);

        echo "\n Files compared, view output file: ".$this->params['with_titles']."\n\n";

    }

    private function _printInTerm($outputString){
        echo "\n".$outputString."\n";
        $handle = fopen ("php://stdin","r");
        $outputData = fgets($handle);
        fclose($handle);
        return $outputData;
    }

    private function doWork($file_price1, $file_price1_col_num, $file_price2, $file_price2_col_num, $filename_output, array $params = [])
    {
        $params['with_titles'] = strtoupper($params['with_titles']);

        if ($params['with_titles'] === 'Y') {
            $title1 = array_shift($price1);
            $title2 = array_shift($price2);
            $title = array_merge($title1,$title2);
            $this->_arrayToCsv($title, $filename_output);
        }

        if (($handle = fopen($file_price1, "r")) !== FALSE) {
            $price1 = array();
            while (($data = fgetcsv($handle, 10000000, ";")) !== FALSE) {
                array_push($price1, $data);
            }
        }
        if (($handle = fopen($file_price2, "r")) !== FALSE) {
            $price2 = array();
            while (($data = fgetcsv($handle, 10000000, ";")) !== FALSE) {
                array_push($price2, $data);
            }
        }
        
        foreach ($price1 as $key1 => $value1) {
            if ($params['with_titles'] === 'Y' && $key1 === 0) continue;

            foreach ($price2 as $key2 => $value2) {
                if ($params['with_titles'] === 'Y' && $key2 === 0) continue;

                if ($value1[$file_price1_col_num] === $value2[$file_price1_col_num]) {
                    $this->_arrayToCsv(array_merge($value1,$value2), $filename_output);
                }
            }
        }

    }

    private function _arrayToCsv(array &$fields, $file_name, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ( $fields as $field ) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            }
            else {
                $output[] = $field;
            }
        }
            file_put_contents($file_name, implode( $delimiter, $output )."\r\n", FILE_APPEND);

        return true;
    }
}


