<?php

/**
 * Clients_model
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Models.Report_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://www.ci2.lcl/
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Класс Report содержит методы работы  с отчетами
 *
 * @category PHP
 * @package  Models.Clients_Model
 * @author   Ермашевский Денис <ermashevsky@gmail.com>
 * @access   public
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 145
 * @link     http://www.reportsender.lcl/
 */
class General_model extends CI_Model {

    /**
     * Унифицированный метод-конструктор __construct()
     *
     * @author Ермашевский Денис
     */
    function __construct() {
        parent::__construct();
        $this->load->library('email');
    }

    function executeEmailSender($A, $B, $C, $D, $E, $F, $G, $H, $I, $J,$K) {

        $data = array(
            'A' => $A,
            'B' => $B,
            'C' => $C,
            'D' => $D,
            'E' => $E,
            'F' => $F,
            'G' => $G,
            'H' => $H,
            'I' => $I,
            'J' => mb_strtolower($J, 'UTF-8'),
            'K' => $K,
        );

        $this->db->insert('excel_dataset', $data);


        if ($J != "#N/A") {

            $username = mb_strtolower($J, 'UTF-8');
            if ($username != '') {

                $this->db->from('managers')->where('username', $username);
                if ($this->db->count_all_results() == 0) {

                    $manager = array(
                        'username' => $username
                    );

                    $this->db->insert('managers', $manager);
                }
            }
        }
        return $this->db->affected_rows();
    }

    function getRecipientList() {
        $this->db->select('managers.id, username, full_name, email, list');
        $this->db->from('managers');
        $this->db->join('subscription_list', 'subscription_list.id_managers = managers.id','left');
        $list = $this->db->get();
        $data = array();
        if (0 < $list->num_rows) {

            foreach ($list->result() as $row) {
                $list_val = new General_model();
                $list_val->id = $row->id;
                $list_val->full_name = $row->full_name;
                $list_val->username = $row->username;
                $list_val->email = $row->email;
                $list_val->list = $row->list;
                $data[$list_val->id] = $list_val;
            }
        }
        return $data;
    }

    function createFiles($username, $list, $email) {
        $objPHPExcel = new PHPExcel();

        if (empty($list)) {
            $array = $username;
        } else {
            $list .="," . $username;
            $array = explode(",", $list);
        }

        $this->db->select('A,B,C,D,E,F,G,H,I,J,K, username, email');
        $this->db->from('excel_dataset');
        $this->db->join('managers', 'managers.username = excel_dataset.J', 'inner');
        $this->db->where_in('username', $array);
        //$this->db->where('username', $username);
        $this->db->group_by('username, A');
        $this->db->order_by("B", "desc");
        $getData = $this->db->get();

        $this->db->select('A,B,C,D,E,F,G,H,I,J,K');
        $this->db->from('excel_dataset');
        $this->db->where('excel_dataset.J', "");
        $getData2 = $this->db->get();

        if (0 < $getData2->num_rows) {

            $single_row = 0;

            foreach ($getData2->result() as $values) {

                // Set document properties
                $single_row++;
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $single_row, $values->A);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $single_row, $values->C);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $single_row, $values->D);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $single_row, $values->E);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $single_row, $values->F);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $single_row, $values->G);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $single_row, $values->H);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $single_row, $values->I);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $single_row, $values->J);
            }
        }

        if (0 < $getData->num_rows) {

            $row = 1;

            foreach ($getData->result() as $values) {

                // Set document properties
                $row++;
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $values->A);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $values->C);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $values->D);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $values->E);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $values->F);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $values->G);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $values->H);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $values->I);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $values->J);

                if ($values->B == "FF0000") {
                    $styleArray = array(
                        'font' => array(
                            'color' => array('rgb' => $values->B),
                            'bold' => true
                    ));
                } else {
                    $styleArray = array(
                        'font' => array(
                            'color' => array('rgb' => $values->B),
                            'bold' => false
                    ));
                }
                
                if ($values->K == "FF0000") {
                    $styleArray2 = array(
                        'font' => array(
                            'color' => array('rgb' => $values->K),
                            'bold' => true
                    ));
                } else {
                    $styleArray2 = array(
                        'font' => array(
                            'color' => array('rgb' => $values->K),
                            'bold' => false
                    ));
                }



                $objPHPExcel->getActiveSheet()->getStyle('I' . $row)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArray2);
                $objPHPExcel->setActiveSheetIndex(0);

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            }
            $objWriter->save($_SERVER['DOCUMENT_ROOT'] . "/" . $this->rus2translit($username) . '.xls');
        }
        
        $call = new General();
        $call->sendMail($_SERVER['DOCUMENT_ROOT'] . "/" . $this->rus2translit($username) . '.xls', $email);
        //$this->sendEMails($_SERVER['DOCUMENT_ROOT'] . "/" . $this->rus2translit($username) . '.xls', $email);
    }

    function rus2translit($string) {

        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );

        return strtr($string, $converter);
    }

//    function sendEmails($filename, $email) {
//
//        $call = new General();
//        $call->sendMail($filename, $email);
//    }

    function getManagers() {
        $this->db->select('username, email, list');
        $this->db->from('managers');
        $this->db->join('subscription_list', 'subscription_list.id_managers = managers.id','left');
        $this->db->where('email IS NOT NULL', null, false);
        $n=0;
        $managers = $this->db->get();
        if (0 < $managers->num_rows) {
            foreach ($managers->result() as $values) {
                
                $this->createFiles($values->username, $values->list, $values->email);
                $n++;
            }
        }
        return $n;
    }

    function getListItem($id) {

        $this->db->select('managers.id, managers.username, managers.email, subscription_list.list');
        $this->db->from('managers');
        $this->db->join('subscription_list', 'subscription_list.id_managers = managers.id', 'left');
        $this->db->where('managers.id', $id);
        $managers = $this->db->get();

        $data = array();

        if (0 < $managers->num_rows) {
            foreach ($managers->result() as $row) {
                $values = new General_model();
                $values->id = $row->id;
                $values->username = $row->username;
                $values->email = $row->email;
                $values->list = $row->list;
                $data[$values->id] = $values;
            }
        }
        return $data;
    }

    function saveListItem($id, $username, $email, $email_list) {
        
        $email  = empty($email) ? NULL : $email;
        
        $data = array(
            'username' => $username,
            'email' => $email
        );

        $this->db->where('id', $id);
        $this->db->update('managers', $data);

        $this->saveList($email_list, $id);
    }

    function saveList($list = NULL, $id_managers = NULL) {
        $this->db->start_cache();
        $this->db->from('subscription_list');
        $this->db->stop_cache();
        // The SKU is our unique ID, if it doesn't exist - we're screwed
        if (!is_null($id_managers)) {
            // In this example, label and price are optional and can be inserted as NULL
            $record = array('list' => $list, 'id_managers' => $id_managers);
            // Check if a record exists for this SKU
            $this->db->where('id_managers', $id_managers);
            if ($this->db->count_all_results() == 0) {
                // the check can be chained for less typing
                // $this->db->where('sku',$sku)->count_all_results()
                // A record does not exist, insert one.
                $query = $this->db->insert('subscription_list', $record);
            } else {
                // A record does exist, update it.
                $query = $this->db->update('subscription_list', $record, array('id_managers' => $id_managers));
            }
            $this->db->flush_cache();
            // Check to see if the query actually performed correctly
            if ($this->db->affected_rows() > 0) {
                return TRUE;
            }
            return FALSE;
        }

        return FALSE;
    }
    
    function truncateTable(){
        $this->db->empty_table('excel_dataset'); 
    }

}

//End of file report_model.php
//Location: ./models/report_model.php