<?
require_once(dirname(__FILE__).'/html2fpdf.php'); 
class PdfMaker{
	public function makeByHtml($fields, $data, $title=''){
		@$pdf=new HTML2FPDF();
		@$pdf->AddPage();
		$strContent=$this->createContent($fields, $data, $title);
		@$pdf->WriteHTML($strContent);
		header('Content-type: application/pdf');
		header('Content-disposition: attachment; filename=report.pdf');
		@$pdf->Output();
		exit;
	}
	
	private function createContent($fields, $data, $title){
		$content ='';
		if($title){
			$content.= '<center><h3>' . $title . '</h3></center>';
		}
		
		$content.='<table border="1" width="500" cellspacing="0" cellpadding="2" align="center">';
		$content.= '<tr>';
		foreach($fields as $field){
			$content.= '<td><b>' . $field .'</b></td>';
		}
		$content.= '</tr>';
		
		foreach($data as $row){
			$content.= '<tr>';
			foreach($fields as $key=>$title){
				$content.= '<td>' . @$row->$key .'</td>';
			}
			$content.= '</tr>';
		}
		$content.='</table>';
		
		return $content;
	}
}
?>