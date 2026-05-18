<?php
	include_once(APPPATH."libraries/PHPExcel.php");
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator($this->jCfg['user']['fullname'])
								 ->setTitle("member_report_".date("Ymdhis"))
								 ->setSubject("Data Member")
								 ->setDescription("Daftar Member")
								 ->setKeywords("SOS")
								 ->setCategory("Report Excel");
	$styleTitle = array( // set style untuk judul
		'font' => array(
			'bold' => true,
			'size' => '18'
		)
	);
	$styleAll = array( // set style untuk membuat border
		'borders'	=> array(
			'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
	);
	$styleHeader = array( // set style untuk header
		'font' => array(
			'bold' => true,
			'color' => array(
				'argb' => 'FFFFFFFF'
			)
		),
		'borders' => array(
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_DOUBLE
			)
		),
		'alignment' => array(
			'vertical'		=> PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'horizontal'	=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'rotation' => 90,
			'color' => array(
				'argb' => 'FF6EBB37',
			)
		)				
	);
	
	$colums = array('A' => 5, 'B' => 15, 'C' => 30, 'D' => 15, 'E' => 15, 'F' => 20, 'G' => 20, 'H' => 40, 
			'I' => 5, 'J' => 15, 'K' => 20, 'L' => 20, 'M' => 20, 'N' => 20); // set lebar masing-masing kolom
	$header = array('A' => 'No', 'B' => 'Client', 'C' => 'No. Kartu', 'D' => 'Nama Member', 'E' => 'Tipe Relasi', 'F' => 'Jenis Kelamin', 'G' => 'Tgl. Lahir', 
			'H' => 'Provinsi', 'I' => 'Kota/Kabupaten', 'J' => 'Kelas Perawatan', 'K' => 'Status Karyawan', 'L' => 'Status Kartu', 'M' => 'Dental Limit'); // set nama header

	
	$objPHPExcel->getActiveSheet()->setShowGridlines(false); // menghilangkan grid lines	
	$objPHPExcel->getActiveSheet()->mergeCells('A1:M1'); // menggabungkan kolom untuk penulisan judul pertama
	$objPHPExcel->getActiveSheet()->mergeCells('A3:M3'); // menggabungkan kolom untuk penulisan judul kedua
		
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'PT. SOS'); // mengisi judul pertama
    $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Daftar Member'); // mengisi judul kedua
    
	$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleTitle); // set style untuk judul pertama
    $styleTitle['font']['size'] = '14'; // set ukuran hurus untuk judul kedua
	$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitle); // set style untuk judul kedua
	
	$start = 5;
	$num = $start;
    foreach ($header as $k => $v) {
		$objPHPExcel->getActiveSheet()->setCellValue($k.$num, $v);
    	
		$objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($colums[$k]); // set width untuk tiap kolom sesuai dengan data pada array $colums
	}
	
	/*if( isset($data) && count($data) > 0 ){
		$num_doc = 1;
		foreach ($data as $r) {
			++$num;
			// mengisi data pada kolom no, dept/cabang, nomor dokumen, tanggal, tempat, alamat, jenis dan jumlah
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $num_doc)
			                            ->setCellValue('B'.$num, $r->client_name)
			                            ->setCellValue('C'.$num, $r->member_cardno)
			                            ->setCellValue('D'.$num, $r->member_name)
			                            ->setCellValue('E'.$num, $r->member_relationshiptype)
			                            ->setCellValue('F'.$num, $r->member_sex=='M'?'Laki-laki':'Perempuan')
			                            ->setCellValue('G'.$num, myDate($r->member_birthdate,"d M Y",false))
			                            ->setCellValue('H'.$num, $r->member_region)
			                            ->setCellValue('I'.$num, $r->member_location)
			                            ->setCellValue('J'.$num, $r->member_plantype)
			                            ->setCellValue('K'.$num, $r->member_employmentstatus)
			                            ->setCellValue('L'.$num, $r->member_statuscard)
			                            ->setCellValue('M'.$num, $r->member_dentallimit);
                            
			$objPHPExcel->getActiveSheet()->getStyle('M'.$num)->getNumberFormat()->setFormatCode('#,###'); // set format angka pada kolom jumlah
			$num_doc++;
		}
	}*/	
	$objPHPExcel->getActiveSheet()->getStyle('A'.$start.':M'.($num+3))->applyFromArray($styleAll); // set style untuk area A5 sampai M(sebanyak berapa baris datanya)
	$objPHPExcel->getActiveSheet()->getStyle('A'.$start.':M'.$start)->applyFromArray($styleHeader); // set style untuk area header
	
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTop(array(1, 6));
	
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER); // 
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(45); // set skala untuk menampilkan satu halaman penuh saat di print

//	if($option=='download') {
//		Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="member_report_'.date("Ymdhis").'xlsx"');
		header('Cache-Control: max-age=0');
//		If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
//		If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
//	} else {
//		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//		$objWriter->save(str_replace('.php', '.xlsx', __FILE__));		
//		$objWriter->save(str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']).'\\mensa\\assets\\files\\'.$file_name."_".date("Ymdhis").'.xlsx"');
//	}
?>