<?php

namespace Punic\Test\Exception;

use Punic\Exception;
use Punic\Test\TestCase;
use stdClass;

class ExceptionTest extends TestCase
{
    public function testExceptions()
    {
        $ex = new Exception('Test');
        $this->assertSame(1, $ex->getCode());
        $this->assertSame('Test', $ex->getMessage());

        $ex = new Exception\BadArgumentType('testValue', 'destination');
        $this->assertSame(Exception::BAD_ARGUMENT_TYPE, $ex->getCode());
        $this->assertSame('testValue', $ex->getArgumentValue());
        $this->assertSame('destination', $ex->getDestinationTypeDescription());
        $this->assertSame("Can't convert 'testValue' to a destination", $ex->getMessage());

        $ex = new Exception\BadArgumentType(new stdClass(), 'destination');
        $this->assertSame("Can't convert stdClass to a destination", $ex->getMessage());

        $ex = new Exception\BadArgumentType(array(), 'destination');
        $this->assertSame("Can't convert array to a destination", $ex->getMessage());

        $ex = new Exception\BadArgumentType(true, 'destination');
        $this->assertSame("Can't convert TRUE to a destination", $ex->getMessage());

        $ex = new Exception\BadArgumentType(1, 'destination');
        $this->assertSame("Can't convert 1 to a destination", $ex->getMessage());

        $ex = new Exception\BadArgumentType(1, 'destination');
        $this->assertSame("Can't convert 1 to a destination", $ex->getMessage());

        $ex = new Exception\BadDataFileContents('filePath', 'fileContents');
        $this->assertSame(Exception::BAD_DATA_FILE_CONTENTS, $ex->getCode());
        $this->assertSame('filePath', $ex->getDataFilePath());
        $this->assertSame('fileContents', $ex->getDataFileContents());
        $this->assertSame("The file 'filePath' contains malformed data", $ex->getMessage());

        $ex = new Exception\DataFileNotFound('fileId', 'it', 'en');
        $this->assertSame(Exception::DATA_FILE_NOT_FOUND, $ex->getCode());
        $this->assertSame('fileId', $ex->getIdentifier());
        $this->assertSame('it', $ex->getLocale());
        $this->assertSame('en', $ex->getFallbackLocale());
        $this->assertSame("Unable to find the data file 'fileId', neither for 'it' nor for 'en'", $ex->getMessage());

        $ex = new Exception\DataFileNotFound('fileId');
        $this->assertSame("Unable to find the data file 'fileId'", $ex->getMessage());

        $ex = new Exception\DataFileNotFound('fileId', 'en', 'en');
        $this->assertSame("Unable to find the data file 'fileId' for 'en'", $ex->getMessage());

        $ex = new Exception\DataFileNotReadable('filePath');
        $this->assertSame(Exception::DATA_FILE_NOT_READABLE, $ex->getCode());
        $this->assertSame('filePath', $ex->getDataFilePath());
        $this->assertSame("Unable to read from the data file 'filePath'", $ex->getMessage());

        $ex = new Exception\DataFolderNotFound('it', 'en');
        $this->assertSame(Exception::DATA_FOLDER_NOT_FOUND, $ex->getCode());
        $this->assertSame('it', $ex->getLocale());
        $this->assertSame('en', $ex->getFallbackLocale());
        $this->assertSame("Unable to find the specified locale folder, neither for 'it' nor for 'en'", $ex->getMessage());

        $ex = new Exception\DataFolderNotFound('en', 'en');
        $this->assertSame("Unable to find the specified locale folder for 'en'", $ex->getMessage());

        $ex = new Exception\InvalidDataFile('fileID');
        $this->assertSame(Exception::INVALID_DATAFILE, $ex->getCode());
        $this->assertSame("'fileID' is not a valid data file identifier", $ex->getMessage());
        $this->assertSame('fileID', $ex->getIdentifier());

        $ex = new Exception\InvalidLocale('xxxxx');
        $this->assertSame(Exception::INVALID_LOCALE, $ex->getCode());
        $this->assertSame("'xxxxx' is not a valid locale identifier", $ex->getMessage());
        $this->assertSame('xxxxx', $ex->getLocale());

        $ex = new Exception\InvalidLocale(array());
        $this->assertSame('A valid locale should be a string, array received', $ex->getMessage());

        $ex = new Exception\NotImplemented('className::methodName');
        $this->assertSame(Exception::NOT_IMPLEMENTED, $ex->getCode());
        $this->assertSame('className::methodName is not implemented', $ex->getMessage());
        $this->assertSame('className::methodName', $ex->getFunction());

        $ex = new Exception\ValueNotInList(1, array(2, 3, 4));
        $this->assertSame(Exception::VALUE_NOT_IN_LIST, $ex->getCode());
        $this->assertSame("'1' is not valid. Acceptable values are: '2', '3', '4'", $ex->getMessage());
        $this->assertSame(1, $ex->getValue());
        $this->assertSame(array(2, 3, 4), $ex->getAllowedValues());
    }
}
