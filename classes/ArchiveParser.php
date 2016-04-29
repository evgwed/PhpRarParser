<?php

class ArchiveParser
{
    private $archive;
    private $archive_name;

    function __construct($filename)
    {
        $this->archive_name = $filename;
        $this->archive = RarArchive::open($this->archive_name);
        if ($this->archive === FALSE)
            throw new Exception('Erorr open archive');
    }

    private function getElements()
    {
        $rar_entries = $this->archive->getEntries();
        if ($rar_entries === FALSE)
            die("Could retrieve entries.");
        return array_map(function($element){
            return [
                'name' => $element->getName(),
                'size' => $element->getUnpackedSize(),
            ];
        }, $rar_entries);
    }

    private function getMinFileName()
    {
        $elements = $this->getElements();
        usort($elements, function($a, $b){
            if ($a['size'] == $b['size']) return 0;
            return $a['size'] > $b['size'] ? 1 : -1;
        });
        return $elements[0]['name'];
    }

    private function unpackMinFile($pass)
    {
        $minFileName = $this->getMinFileName();
        $archive = RarArchive::open($this->archive_name, $pass);
        $entry = $archive->getEntry($minFileName);
        $entry->extract(".");
        $archive->close();
    }

    public function unpackBrute($minValue, $maxValue, $showLog = false)
    {
        for ($pass = $minValue; $pass <= $maxValue; $pass ++) {
            try {
                $this->unpackMinFile($pass);
                if ($showLog) echo "Pass $pass OK!\n";
                return $pass;
            } catch (Exception $ex) {
                if ($showLog) echo "Pass $pass -\n";
            }
        }
    }

    function __destruct()
    {
        if ($this->archive instanceof RarArchive) {
            $this->archive->close();
        }
    }
}