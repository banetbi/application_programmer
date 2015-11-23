<?php

$fileData = fopen("./ingest/files/data.csv", "r");
if (!mkdir("./ingest/files/SimpleArchiveFormat")) {
    die("Error: Could not create archive directory.");
}
$i = 0;
$keyArray = array();

//Itereate over the CSV file and grab one line at a time until you hit the end of file.
while (!feof($fileData)) {
    $itemRow = fgetcsv($fileData);
    if ($itemRow[0] == "") {
        //If there is no data in the title, we are done with the file, regardless of whether the EOF has been hit.
        break;
    }
    if ($itemRow[0] == 'Title') {
        //Build map of header keys
        foreach ($itemRow as $strHeaderName) {
            $keyArray[] = $strHeaderName;
        }
    } else {
        if (!mkdir("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i))) {
            die("Error: Could not create archive subdirectory for item_" . sprintf('%03d', $i));
        }
        //Key the row with Header Keys
        $arrKeyedArray = array_combine($keyArray, $itemRow);

        //Get array of file names
        $arrFileNames = explode(";", $arrKeyedArray['Files']);

        $fileContents = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/contents", "w");
        foreach ($arrFileNames as $strFileName) {
            fputs($fileContents, trim($strFileName) . "\n");
            copy("./ingest/files/" . trim($strFileName), "./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/" . trim($strFileName));
        }
        fclose($fileContents);
        $fileDublinCore = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/dublin_core.xml", "w");
        fputs($fileDublinCore, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fputs($fileDublinCore, "<dublin_core>\n");
        foreach ($arrKeyedArray as $key => $value) {
            //Check the key and write either into the dublin_core file or the metadata_etd file
            switch ($key) {
                case "Date Issued":
                    if ($value != "") {
                        $strExport = "\t" . '<dcvalue element="date" qualifier="issued">' . $value . '</dcvalue>' . "\n";
                        fputs($fileDublinCore, $strExport);
                    }
                    break;
                case "CollectionID":
                    if ($value != "") {
                        $strExport = "\t" . '<dcvalue element="identifier" qualifier="collectionId">' . $value . '</dcvalue>' . "\n";
                        fputs($fileDublinCore, $strExport);
                    }
                    break;
                case "Abstract Description":
                    if ($value != "") {
                        $strExport = "\t" . '<dcvalue element="description" qualifier="abstract">' . $value . '</dcvalue>' . "\n";
                        fputs($fileDublinCore, $strExport);
                    }
                    break;
                case "Advisor":
                    if ($value != "") {
                        $strExport = "\t" . '<dcvalue element="contributor" qualifier="advisor">' . $value . '</dcvalue>' . "\n";
                        fputs($fileDublinCore, $strExport);
                    }
                    break;
                case "Committee Members":
                    if ($value != "") {
                        $arrCommitteeMembers = explode(";", $value);
                        foreach ($arrCommitteeMembers as $strCommitteeMember) {
                            $strExport = "\t" . '<dcvalue element="contributor" qualifier="committeeMember">' . trim($strCommitteeMember) . '</dcvalue>' . "\n";
                            fputs($fileDublinCore, $strExport);
                        }
                    }
                    break;
                case "Degree Discipline":
                    if ($value != "") {
                        if (!file_exists("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml")) {
                            $fileEtdMetadata = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml", "w");
                            fputs($fileEtdMetadata, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
                            fputs($fileEtdMetadata, '<dublin_core schema="etd">' . "\n");
                        } else {
                            $fileEtdMetadata = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml", "a");
                        }
                        fputs($fileEtdMetadata, "\t" . '<dcvalue element="degree" qualifier="discipline">' . $value . '</dcvalue>' . "\n");
                        fclose($fileEtdMetadata);
                    }
                    break;
                case "Degree Level":
                    if ($value != "") {
                        if (!file_exists("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml")) {
                            $fileEtdMetadata = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml", "w");
                            fputs($fileEtdMetadata, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
                            fputs($fileEtdMetadata, '<dublin_core schema="etd">' . "\n");
                        } else {
                            $fileEtdMetadata = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml", "a");
                        }
                        fputs($fileEtdMetadata, "\t" . '<dcvalue element="degree" qualifier="level">' . $value . '</dcvalue>' . "\n");
                        fclose($fileEtdMetadata);
                    }
                    break;
                case "Files":
                    break;
                default:
                    if ($value != "") {
                        $strExport = "\t" . '<dcvalue element="' . strtolower($key) . '">' . $value . '</dcvalue>' . "\n";
                        fputs($fileDublinCore, $strExport);
                    }
            }
        }
        //If we created a new etd file, we need to close it out.
        if (file_exists("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml")) {
            $fileEtdMetadata = fopen("./ingest/files/SimpleArchiveFormat/item_" . sprintf('%03d', $i) . "/metadata_etd.xml", "a");
            fputs($fileEtdMetadata, "\t" . '<dcvalue element="degree" qualifier="grantor">College of William and Mary</dcvalue>' . "\n");
            fputs($fileEtdMetadata, "</dublin_core>\n");
            fclose($fileEtdMetadata);
        }
        fputs($fileDublinCore, "</dublin_core>\n");
        fclose($fileDublinCore);

        $i++;
    }
}