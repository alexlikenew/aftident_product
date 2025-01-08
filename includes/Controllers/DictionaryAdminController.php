<?php

namespace Controllers;

use Models\Dictionary;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class DictionaryAdminController extends DictionaryController
{

        public function __construct($table = 'slownik_admin'){

            parent::__construct($table);
        }

        public function loadArticlesAdmin($pages = null, $page = 1) {

            $articles = $this->model->loadArticlesAdmin($page);

            foreach($articles as $key=>$article) {
                $articles[$key]['opis'] = $this->loadDescriptionById($article['id']);
            }
            return $articles;
        }

        public function loadDescriptionById($id) {
            $data = $this->model->loadDescriptionById($id);
            $description = [];
            foreach($data as $value) {
                $description[$value['language_id']] = $value;
            }
            return $description;
        }

        public function countArticlesAdmin() {
            $count = $this->model->countArticlesAdmin();
            if ($count < 1) {
                $count = 1;
            }
            return ceil($count / $this->limit_admin);
        }

        public function saveAll($dane) {

            foreach ($dane['value'] as $parent_id => $description) {
                foreach ($description as $lang_id => $value) {
                    $id = $this->model->getId($parent_id, $lang_id);
                    $value = trim($value);
                    if ($id) {
                        $this->model->update([ 'value' => $value ], $id);
                    } else {
                        $this->model->createDescription([
                            'value'     => $value,
                            'parent_id' => $parent_id,
                        ], $lang_id);
                    }
                }
            }
        }

        public function create(array $data, array $files = null): int {

            $data['label'] = prepareString($data['label'], true);

            $id = $this->model->create($data);

            if ($id) {
                foreach ($data['value'] as $key => $value) {
                    $value = prepareString($value);
                    $this->model->createDescription([
                        'value'     => $value,
                        'parent_id' => $id,
                    ], $key);
                }
            }
            return $id;
        }

        public function delete(int $id = null):bool {
            $this->model->delete($id);
            return true;
        }

        function searchIds($keyword, $page = 1) {
            $data = $this->model->search($keyword, $page);

            foreach ($data AS $value) {
                $item = $this->model->getArticleById($value['id']);
                $item['opis'] = $this->loadDescriptionById($item['id']);
                $articles[] = $item;
            }
            return $articles;
        }

        //kiedys getSzukajPages
        public function searchPages($keyword) {
            $count = $this->model->searchPages($keyword);

            return ceil($count / $this->limit_admin);
        }

        function checkLabel($label) {
        $label =  $this->model->checkLabel($label);

        return $label['value'] ?? 'nie ma etykiety w słowniku';
        }

        public function makeSQL() {
            $file = fopen('slownik.sql', 'a');
            $data = $this->model->getAll();

            foreach($data as $value) {
            $text = $value['label'] . PHP_EOL;
            fwrite($file, $text);

            $descriptions = $this->model->getArticleDescriptions($value['id']);

            foreach($descriptions as $description) {
                $text = $description['language_id'] . ' "' . $description['value'] . '"' . PHP_EOL;
                fwrite($file, $text);
            }
            $text = PHP_EOL;
            fwrite($file, $text);
            }
            fclose($file);
        }

        public function readSQL() {
            $file = fopen(ROOT_PATH . '/slownik.sql', 'r');
            $members = [];
            while (!feof($file)) {
                $members[] = fgets($file);
            }

            fclose($file);
            $words = [];

            $count = 0;
            $index = 0;

            foreach ($members as $member) {
                if ($count == 0) {
                    $words[$index]['label'] = $member;
                    $words[$index]['value'] = array();
                }
                if ($count > 0 && $count < 5) {
                    $pos = strpos($member, ' ');
                    $lang = mb_substr($member, 0, $pos);
                    $val = trim(mb_substr($member, $pos, strlen($member) - 1));
                    $words[$index]['value'][$lang] = htmlspecialchars_decode(mb_substr(trim($val), 1, (strlen($val) - 2)));
                }
                $count++;
                if ($count == 6) {
                    $count = 0;
                    $index++;
                }
            }
            $label = array();
            foreach ($words as $key => $row) {
                $label[$key] = $row['label'];
            }
            array_multisort($label, SORT_ASC, $words);
            foreach ($words as $word) {
                $this->create($word);
            }
        }

        public function exportExcel() {
            $objPHPSpreadsheet = new Spreadsheet();
            $objPHPSpreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
            $objPHPSpreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPSpreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $sheetRowIndex = 1;
            $sheetColIndex = 1;

            $langs = ConfigController::getAllLangs();
            $sheetTypeColIndex = 6;
            $lang_cols = [];

            $objPHPSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($sheetTypeColIndex, $sheetRowIndex, 'lang');
            $objPHPSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($sheetColIndex, $sheetRowIndex, 'Język');
            $sheetColIndex++;

            foreach ($langs as $lang) {
                $lang_cols[$lang['id']] = $sheetColIndex;
                $objPHPSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($sheetColIndex, $sheetRowIndex, $lang['name']);
                $objPHPSpreadsheet->getActiveSheet()->getColumnDimensionByColumn($sheetColIndex)->setWidth(55);
                $sheetColIndex++;
            }


            $sheetRowIndex++;

            $data = $this->model->getAll();
            $articles = [];

            foreach($data as $values) {
                $values['opis'] = $this->loadDescriptionById($values['id']);
                $articles[] = $values;
            }
            $sheetColIndex = 1;
            foreach ($articles as $article) {

                $objPHPSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($sheetTypeColIndex, $sheetRowIndex, 'item');
                $objPHPSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($sheetTypeColIndex, $sheetRowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $objPHPSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($sheetColIndex, $sheetRowIndex, $article['label']);
                $objPHPSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($sheetColIndex, $sheetRowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                foreach ($article['opis'] as $opis) {
                    if (isset($lang_cols[$opis['language_id']])) {
                        $objPHPSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($lang_cols[$opis['language_id']], $sheetRowIndex, $opis['value']);
                        $objPHPSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($lang_cols[$opis['language_id']], $sheetRowIndex)->getAlignment()->setWrapText(true);
                        $objPHPSpreadsheet->getActiveSheet()->getStyleByColumnAndRow($lang_cols[$opis['language_id']], $sheetRowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                    }
                }

                $sheetRowIndex++;
            }

            foreach ($objPHPSpreadsheet->getActiveSheet()->getRowDimensions() as $rd) {
                $rd->setRowHeight(-1);
            }

            $filename = 'slownik-' . date('d-m-Y') . '.xlsx';
            $objWriter = new Xlsx($objPHPSpreadsheet);
            ob_end_flush();

            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $filename);
            $objWriter->save('php://output');
        }

        public function importExcel($files) {

            if (isset($files['file'])) {
                try {
                    $objPHPExcel = IOFactory::load($files['file']['tmp_name']);
                } catch(Exception $e) {
                    $this->setError('Error loading file: ' . $e->getMessage());
                    return false;
                }

                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, false);

                /*
                if (count($sheetData) > 0) {
                    $this->model->truncate();
                }
                */
                $lang_id = [];

                foreach ($sheetData as $sheetRow) {

                    switch ($sheetRow[count($sheetRow) - 1]) {
                        case 'lang':
                            foreach ($sheetRow as $key => $col) {
                                if ($key >0 && !empty($col) && $col!='lang') {
                                    $lang = ConfigController::getLangByName($col);
                                    $lang_id[$key] = $lang['id'];
                                }
                            }
                            break;
                            case 'item':

                                $id = $this->model->getIdByLabel($sheetRow[0]);

                                if (!$id)
                                    $id = $this->model->create(trim($sheetRow[0]));

                                foreach ($sheetRow as $key => $col) {
                                    if ($key > 0 && !empty($col)) {
                                        if(isset($lang_id[$key]))
                                            $this->model->createDescription([
                                                'value' => trim($sheetRow[$key]),
                                                'parent_id' => $id,
                                            ], $lang_id[$key]);
                                    }
                                }
                                break;
                    }
                }
            }
        }

        public function getItemsAdmin($page = 1){
            return $this->getItems(100, $page);
        }
}