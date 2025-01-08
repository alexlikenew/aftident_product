<?php


namespace Classes;
use Classes\PhpThumb\PhpThumbFactory;
use Classes\Upload\UploadManager;


class Image
{
    public $dir;
    public $name;
    public $max_size;

    public function __construct($directory, $max_size = '8048 KB') {
        $this->dir = $directory;
        $this->max_size = $max_size;
        $this->options = array
        (
            'resizeUp'              => false,
            'jpegQuality'           => 90,
            'pngCompression'		=> 9,
            'correctPermissions'    => false,
            'preserveAlpha'         => true,
            'alphaMaskColor'        => array (255, 255, 255),
            'preserveTransparency'  => true,
            'transparencyMaskColor' => array (0, 0, 0)
        );
    }

    public function clearVars() {
        unset($this->name);
        return true;
    }

   public  function setFile($filename) {
        $this->clearVars();

        $this->name = $filename;
    }

    /* dodajemy plik, na ktorym bedziemy operowac */

    public function UploadFile($index, $filename) {
        $this->clearVars();

        $this->name = $filename;

        $oFile = UploadManager::get($index);

        if (!$oFile->isOk()) {
            //$this->setError($oFile->getErrorAsString());
            return false;
        }

        if (!$oFile->isValidExt('png', 'gif', 'jpg', 'jpeg', 'webp')) {
            //$this->setError($GLOBALS['_FILE_WRONG_EXTENSION']);
            return false;
        }

        if (!$oFile->isValidSize($this->max_size)) {
            //$this->setError($GLOBALS['_FILE_TO_BIG']);
            return false;
        }

        return $oFile->moveWithNewName($this->dir, $this->name, '666');
    }

    public function createBlurImage($filename){


        $img = PhpThumbFactory::create($this->dir . DIRECTORY_SEPARATOR . $this->name, $this->options);
        $img->blurImage();

        $filename = nameThumb($filename, '_blur');
        $img->save($this->dir . DIRECTORY_SEPARATOR . $filename);
    }

    public static function getOtherAppends(){
        return[
            //['type' =>  'list',         'append'    => '_l',    'ext' => false],
            //['type' =>  'detail',       'append'    => '_d',    'ext' => false],
            //['type' =>  'main',         'append'    => '_m',    'ext' => false],
            ['type' =>  'blur',         'append'    => '_blur', 'ext' => false],
            ['type' =>  'small',        'append'    => '_s',    'ext' => 'webp'],
            ['type' =>  'list',         'append'    => '_l',    'ext' => 'webp'],
            ['type' =>  'detail',       'append'    => '_d',    'ext' => 'webp'],
            ['type' =>  'main',         'append'    => '_m',    'ext' => 'webp'],
            ['type' =>  'photo_webp',   'append'    => '',      'ext' => 'webp'],
            ['type' =>  'blur_webp',    'append'    => '_blur', 'ext' => 'webp'],
            ['type' =>  'avif',    'append'    => '', 'ext' => 'avif'],
        ];
    }

    public function ScaleImage($width, $height) {

        $thumb = PhpThumbFactory::create($this->dir . DIRECTORY_SEPARATOR . $this->name, $this->options);
        $dimensions = $thumb->getCurrentDimensions();

        //zmniejszenie zdjecia oryginalnego jesli jest za duze
        if ($dimensions['width'] > $width || $dimensions['height'] > $height) {
            $thumb->resize($width, $height);
            $thumb->save($this->dir . DIRECTORY_SEPARATOR . $this->name);
        }
    }

    public function saveWebP($fileName){
        $nameArray = explode('.', $fileName);
        array_pop($nameArray);
        $name = implode('.', $nameArray);
        $webpName = implode('.', $nameArray).'.webp';
        $avifName = implode('.', $nameArray).'.avif';
        $img = PhpThumbFactory::create($this->dir . DIRECTORY_SEPARATOR . $this->name, $this->options);



        imagewebp($img->getOldImage(), $this->dir . DIRECTORY_SEPARATOR . $webpName);
        if(function_exists('imageavif'))
            imageavif($img->getOldImage(), $this->dir . DIRECTORY_SEPARATOR . $avifName);
        else
            try{
                if (function_exists("exec")) {
                    $command = "convert ".$this->dir . DIRECTORY_SEPARATOR .$fileName." ".$this->dir . DIRECTORY_SEPARATOR . $name.".avif";
                    exec($command);
                }

            }
            catch(\Exception $e){

            }

    }

    public function ThumbFromCenter($width, $height, $koncowka) {
        $thumbname = nameThumb($this->name, $koncowka);

        $thumb = PhpThumbFactory::create($this->dir . DIRECTORY_SEPARATOR . $this->name, $this->options);
        $thumb->adaptiveResize($width, $height);
        $thumb->save($this->dir . DIRECTORY_SEPARATOR . $thumbname, false);
    }

    public function Thumb($x, $y, $x2, $y2, $width, $height, $koncowka) {
        $thumbname = nameThumb($this->name, $koncowka, 'webp');

        $cropWidth = $x2 - $x;
        $cropHeight = $y2 - $y;

        $thumb = PhpThumbFactory::create($this->dir . DIRECTORY_SEPARATOR . $this->name, $this->options);
        $thumb->crop($x, $y, $cropWidth, $cropHeight);
        $thumb->resize($width, $height);
        $thumb->save($this->dir . DIRECTORY_SEPARATOR . $thumbname, false);
    }

}