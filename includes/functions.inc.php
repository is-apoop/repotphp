<?php

function helloWorld()
{
	echo "<br/>hello world<br/>";
}

function saveXLCfile()
{
	/** Error reporting */
	error_reporting(E_ALL);

	/** Include path **/
	ini_set('include_path', ini_get('include_path').';../classes/');

	/** PHPExcel */
	include 'classes/PHPExcel.php';

	/** PHPExcel_Writer_Excel2007 */
	include 'classes/PHPExcel/Writer/Excel2007.php';

	// Create new PHPExcel object
	echo date('H:i:s') . " Create new PHPExcel object\n";
	$objPHPExcel = new PHPExcel();

	// Set properties
	echo date('H:i:s') . " Set properties\n";
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");


	// Add some data
	echo date('H:i:s') . " Add some data\n";
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
	$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
	$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');

	// Rename sheet
	echo date('H:i:s') . " Rename sheet\n";
	$objPHPExcel->getActiveSheet()->setTitle('Simple');

			
	// Save Excel 2007 file
	echo date('H:i:s') . " Write to Excel2007 format\n";
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('save.xls');

	/*
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="ssss.xlsx"');
	header('Cache-Control: max-age=0');
	$objWriter->save('php://output');
	*/
}

?>