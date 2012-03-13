<?php
class TF_Export {
	/**
	 * Generate a CSV file from headers and a closure for rows.
	 * This allows to have formatted rows.
	 * 
	 * @param string Filename
	 * @param array $headers First lines
	 * @param closure $rowFn A closure that is executed for each line.
	 * @param array $data Raw data.
	 */
	public static function csvFromData($filename, array $headers, closure $rowFn, $data) {
		$finalData = $headers;
		
		foreach($data as $row){
			$finalData[] = $rowFn($row);
		}
		
		self::csvFromArray(
			$filename,
			$finalData
		);
	}
	
	/**
	 * Generate a CSV file from an array.
	 * 
	 * @param string $filename
	 * @param array $data
	 */
	public static function csvFromArray($filename, array $data) {
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$filename);

		$out = fopen('php://output', 'w');
		
		if(!empty($data)) {
			foreach($data as $row) {
				if(is_null($row)) continue;
				
				fputcsv($out, $row);
			}
		}

		fclose($out);
	}
	
	/**
	 * Generate a XLS file from headers and a closure for rows.
	 * This allows to have formatted rows.
	 * 
	 * @param string Filename
	 * @param array $headers First lines
	 * @param closure $rowFn A closure that is executed for each line.
	 * @param array $data Raw data.
	 */
	public static function xlsFromData($filename, array $headers, closure $rowFn, $data) {
		$finalData = $headers;
		
		foreach($data as $row){
			$finalData[] = $rowFn($row);
		}
		
		self::xlsFromArray(
			$filename,
			$finalData
		);
	}
	
	/**
	 * Generate a XLS file from an array.
	 * 
	 * @param string $filename
	 * @param array $data
	 */
	public static function xlsFromArray($filename, array $data) {
		/** PHPExcel */
		require_once("PHPExcel/PHPExcel.php");
		
		$PHPExcel = new PHPExcel();
		
		$PHPExcel
			->getProperties()
			->setCreator("PromoSimple")
			->setTitle("Report");
			
		$Worksheet = $PHPExcel->setActiveSheetIndex(0);
		$Worksheet->setTitle("TripFab Report");
		
		$row = 1;		
		
		foreach($data as $line) {
			if(is_null($line)) continue;
			
			foreach($line as $key=>$value) {
				$Worksheet->setCellValueExplicitByColumnAndRow($key, $row, (string) $value, PHPExcel_Cell_DataType::TYPE_STRING);	
			}
			
			$row++;
		}
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}