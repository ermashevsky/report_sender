<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(E_ALL);
require_once APPPATH . "/third_party/PHPExcel.php";

class General extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct() {
        parent::__construct();

        $this->load->library('ion_auth');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->helper('url', 'form', 'text','file');
    }

    public function index() {
        $this->load->view('header');
        $this->load->view('general');
    }

    public function uploadify() {
        $targetFolder = '/uploads'; // Relative to the root
        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
            $targetFile = rtrim($targetPath, '/') . '/' . $_FILES['Filedata']['name'];
            // Validate the file type
            $fileTypes = array('xls'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array($fileParts['extension'], $fileTypes)) {
                move_uploaded_file($tempFile, $targetFile);
                echo $targetPath;
            } else {
                echo '<div class="alert alert-error">
						<a class="close" data-dismiss="alert" href="#">×</a>Можно загружать только Excel файлы</div>';
            }
        }
    }

    public function find_all_files() {
        $dir = $this->input->post('dir');
        echo "<table class='table table-striped table-bordered table-condensed table-hover'><tr><td><b>Имя</b></td><td><b>Тип</b></td><td><b>Размер</b></td>";
        echo "<td><b>Действие</b></td></tr>";
        $itemHandler = opendir($dir);
        $i = 0;
        while (($item = readdir($itemHandler)) !== false) {
            if (substr($item, 0, 1) != ".") {
                if (!is_dir($item)) {
                    $fullpath = $dir . '/' . $item;
                    echo "<tr><td>" . $item . "</td><td>файл</td><td>" . filesize($fullpath) . " Bytes</td>
						<td><a class='btn btn-mini' href=javascript:viewfile('" . $fullpath . "')><i class='icon-eye-open'></i> Просмотр</a> <a class='btn btn-mini' href=javascript:delfile('" . $fullpath . "') id='delete_btn' ><i class='icon-trash'></i> Удалить</a></td></tr>";
                }
                $i ++;
            }
        }
        echo "</table>";
    }
    
    function getRecipientList(){
        $this->load->model('general_model');
        $data = $this->general_model->getRecipientList();
        return $data;
    }
    
    public function readXLS() {
        $file = $this->input->post('pathfile');

        $objReader = new PHPExcel_Reader_Excel5();
        //$objReader->setInputEncoding('CP1251');
        //$objReader->setDelimiter(';');
        //$objReader->setEnclosure('');

        $objPHPExcel = $objReader->load($file);
        $objReader->setReadDataOnly(true);
        
//		$objPHPExcel -> setActiveSheetIndex(0)
//				->setCellValue('A1', 'resource')
//				->setCellValue('B1', 'amount')
//				->setCellValue('C1', 'date')
//				->setCellValue('D1', 'assortment');

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true, true, true, true, true, true,true,true); //!!!

                foreach ($sheetData as $key=>$rows):
                    //echo $rows['A']. ' ' .$rows['C']. ' ' .$rows['D']. ' '.$rows['E']. ' ' .$rows['F']. ' ' .$rows['G']. ' ' .$rows['H']. ' ' .$rows['I']. ' ' .$rows['J'].'<br/>';
                    //echo $objPHPExcel->getActiveSheet(0)->getStyle("I".$rowcount++)->getFill()->getStartColor()->getRGB();
                    $sheetData[$key]["B"] = $objPHPExcel->getActiveSheet(0)->getStyle("I".$key)->getFill()->getStartColor()->getRGB(); //Тута берем цвет I а значение сохраняем в поле B таблицы
                    $sheetData[$key]["K"] = $objPHPExcel->getActiveSheet(0)->getStyle("H".$key)->getFill()->getStartColor()->getRGB();
                    //array_push($sheetData, "B".$key, $objPHPExcel->getActiveSheet(0)->getStyle("I".$key)->getFill()->getStartColor()->getRGB());
		endforeach;
                
                
                
echo json_encode($sheetData);
                        
//		$loadedSheetNames = $objPHPExcel -> getSheetNames();
//		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
//
//
//
//		foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
//			$objWriter -> setSheetIndex($sheetIndex);
//			$objWriter -> save('application/csv/mts/file_' . date('Y-m-d', now()) . '.csv');
//
//
//		unlink($inputFileName);
//	}
    }

    public function send_email() {

        $A = $this->input->post('A');
        $B = $this->input->post('B');
        $C = $this->input->post('C');
        $D = $this->input->post('D');
        $E = $this->input->post('E');
        $F = $this->input->post('F');
        $G = $this->input->post('G');
        $H = $this->input->post('H');
        $I = $this->input->post('I');
        $J = $this->input->post('J');
        $K = $this->input->post('K');

        $this->load->model('general_model');
        $data = $this->general_model->executeEmailSender($A, $B, $C, $D, $E, $F, $G, $H, $I, $J,$K);

        echo json_encode($data);
    }

    public function createXlsFiles() {
        $this->load->model('general_model');
        $data = $this->general_model->getManagers();
        echo json_encode($data);
    }

    public function sendMail($filename, $email) {
        $this->load->library('email');

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.dialog64.ru';
        $config['smtp_user'] = 'ermashevsky@dialog64.ru';
        $config['smtp_pass'] = 'kk6k29';
        $config['smtp_port'] = 25;
        $config['smtp_timeout'] = 5;

        $config['charset'] = 'utf-8';
        $config['crlf'] = "\n";
        $config['newline'] = "\r\n";
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);
        
        $this->email->from('zaharov@dialog64.ru', 'Захаров Владимир');
        $this->email->to($email);
        $this->email->attach($filename);
        $this->email->subject('Еженедельная статистика');
        $this->email->message('Доброго дня! В прикрепленном файле еженедельная статистика.');
        
        $this->email->send();

          unlink($filename);
        
//        $path = $_SERVER['DOCUMENT_ROOT'] . "/*.xls";
//        
//        if (!file_exists($path)) 
//        {
//            $this->load->model('general_model');
//            $this->general_model->truncateTable();
//        } 
        //echo $this->email->print_debugger();
    }

    /**
     * Метод удаления файла с сервера
     *
     * @author Ермашевский Денис
     * @return null;
     */
    function deleteFromServer() {
        $path = trim($this->input->post('pathfile'));
        unlink($path);
    }
    
    public function getListItem(){
        $id = trim($this->input->post('id'));
        
        $this->load->model('general_model');
        $data = $this->general_model->getListItem($id);
        
        echo json_encode($data);
    }
    
    public function saveListItem(){
        
        $id = trim($this->input->post('id'));
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $email_list = $this->input->post('email_list');
        
        $this->load->model('general_model');
        $data = $this->general_model->saveListItem($id, $username, $email, $email_list);
        
        
        
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/general.php */