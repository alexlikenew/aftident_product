<?php


namespace Classes\Upload;


class UploadFile
{
    /**
     * Oryginalna nazwa pliku
     *
     * @var string
     */
    protected $name='';

    /**
     * Rozszerzenie oryginalne
     *
     * @var string
     */
    protected $extension='';

    /**
     * Rozmiar pliku
     *
     * @var int
     */
    protected $size=0;

    /**
     * Type mime pliku -  nie zawsze podany
     *
     * @var string
     */
    protected $type='';

    /**
     * Nazwa tymczasowa pliku na serwerze
     *
     * @var string
     */
    protected $tmpName='';

    /**
     * Kod bledu przy uploadzie
     *
     * @var int
     */
    protected $error=0;

    /**
     * Zawiera kompletna sciezke do pliku juz przesunietego
     *
     * @var string
     */
    protected $filePath='';

    /**
     * Konstruktor
     *
     * @param array $fileData Tablica z danymi o pliku
     */
    public function __construct($fileData)
    {
        $this->name=basename($fileData['name']);
        $this->extension=strtolower(pathinfo($this->name,PATHINFO_EXTENSION));
        $this->type=(isset($fileData['type'])) ? $fileData['type']  : 0 ;
        $this->size=(int)$fileData['size'];
        $this->tmpName=$fileData['tmp_name'];
        $this->error=(int)$fileData['error'];
    }

    /**
     * Sprawdza czy plik jest OK
     *
     * @return bool true Jesli plik jest ok
     */
    public function isOk()
    {
        return ($this->error==0 && is_uploaded_file($this->tmpName) && $this->size > 0);
    }

    /**
     * Sprawdza czy rozszerzenie jest poprawne
     *
     * Mozliwe wywolania
     *
     * $file->isValidExt("gif");
     * $file->isValidExt(array("gif","jpg"));
     * $file->isValidExt("gif","jpg");
     *
     * @param mixed $ext
     *
     * @return bool true jesli rozszerzenie jest poprawne
     */
    public function isValidExt($ext)
    {
        if(strlen($this->extension)>0)
        {
            if(func_num_args()>1)
            {
                $ext=func_get_args();
            }

            if(is_array($ext))
            {
                array_map('strtolower',$ext);
                return in_array($this->extension,$ext);
            }
            else
            {
                return ($this->extension===strtolower($ext));
            }
        }
        return false;
    }

    /**
     * Sprawdza typ mime jest poprawny
     *
     * Mozliwe wywolania
     *
     * $file->isValidType("image/gif");
     * $file->isValidType(array("image/gif","image/jpg"));
     * $file->isValidType("image/gif","image/jpg");
     *
     * @param mixed $type
     *
     * @return bool true jesli typ mime jest poprawny
     */
    public function isValidType($type)
    {
        if(strlen($this->type))
        {
            if(func_num_args()>1)
            {
                $type=func_get_args();
            }

            if(is_array($type))
            {
                array_map('strtolower',$type);
                return in_array($this->type,$type);
            }
            else
            {
                return ($this->type===strtolower($type));
            }
        }
        return false;
    }

    /**
     * Sprawdz plik ma odpowiedni rozmiar
     *
     * Wywolania
     *
     * $file->isValidSize(300000);
     * $file->isValidSize("300 KB");
     * $file->isValidSize("300 MB");
     *
     * Obsluguje kilobajty i megabajty
     *
     * @param mixed $size
     * @return bool True jesli rozmiar pliku jest mniejszy badz rowny podanemu
     * @throws uploadException Jesli wystapil blad podczas sprawdzania
     */
    public function isValidSize($size)
    {

        if(is_int($size))
        {
            return (($this->size > 0) && ($this->size <= $size));
        }
        elseif(strlen($size)>0)
        {
            preg_match_all("#^([0-9]+)\s*(KB|MB)?$#i",$size,$matches);

            $size=(int)trim($matches[1][0]);

            if($size>0)
            {
                if(strlen($matches[2][0])>0)
                {
                    switch (strtolower($matches[2][0]))
                    {
                        case 'kb':
                            $size*=1024; // kilobjatow
                            break;

                        case 'mb':
                            $size*=1048576;//megabajtow
                            break;
                    }
                }

                return (($this->size > 0) && ($this->size <= $size));
            }
        }

        throw new UploadException("Niepoprawny format danych przy sprawdzaniu wielkosci");
    }

    /**
     * Przesuwa plik do odpowiedniego folderu z nowa nazwa
     *
     * @param string $dest Katalog docelowy
     * @param string $name Nowa nazwa pliku
     * @param string $chmod CHMOD jesli trzeba
     * @return bool True jesli poprawnie przesunieto
     */
    public function moveWithNewName($dest,$name,$chmod=null)
    {
        return $this->move($dest.'/'.$name,$chmod);
    }

    /**
     * Przesuwa plik do odpowiedniego folderu ze stary nazwa pliku
     *
     * @param string $dest Katalog docelowy
     * @param string $chmod CHMOD jesli trzeba
     * @return bool True jesli poprawnie przesunieto
     */
    public function moveWithOriginalName($dest,$chmod=null)
    {
        return $this->move($dest.'/'.$this->name,$chmod);
    }

    /**
     * Przesuwa plik do odpowiedniego folderu nadajac plikowi nazwe
     *
     * @param string $dest Katalog docelowy wraz z nazwa pliku
     * @param string $chmod CHMOD jesli trzeba
     * @return bool True Jesli plik zostal poprawnie przesuniety
     */
    protected function move($dest,$chmod=null)
    {
        $ret=move_uploaded_file($this->tmpName,$dest);

        if($ret && strlen($chmod)==3)
        {
            $ret=chmod($dest,octdec("0".$chmod));
        }

        $ret=(bool)$ret;

        if($ret) $this->filePath=$dest;

        return $ret;
    }

    /**
     * Zwraca nazwe pliku z komputera klienta
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->name;
    }

    /**
     * Zwraca rozszerznie oryginalnego pliku
     *
     * @return string
     */
    public function getOriginalExtension()
    {
        return $this->extension;
    }

    /**
     * Zwraca sciezke do pliku na serwerze - przydatne pozniej
     *
     * @return string
     */
    public function getUploadedFilePath()
    {
        return $this->filePath;
    }


    /**
     * Zwraca type mime pliku
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->type;
    }

    /**
     * Zwraca oryginalny rozmiar pliku
     *
     * @return int;
     */
    public function getFilesize()
    {
        return $this->size;
    }

    /**
     * Zwraca kod bledu
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->error;
    }

    /**
     * Zwraca blad w postaci komunikatu
     *
     * @return string
     */
    public function getErrorAsString()
    {
        $msg='';

        switch ($this->getErrorCode())
        {
            case UPLOAD_ERR_CANT_WRITE :  $msg='Nie mozna bylo zapisac pliku'; break;
            case UPLOAD_ERR_FORM_SIZE : $msg="Wielkosc pliku przekroczyla rozmiar maksymalny podany w formularzu"; break;
            case UPLOAD_ERR_INI_SIZE : $msg="Wielkosc pliku przekroczyla rozmiar maksymalny podany w pliku php.ini"; break;
            case UPLOAD_ERR_NO_FILE : $msg="Brak pliku"; break;
            case UPLOAD_ERR_NO_TMP_DIR: $msg="Katalog tymczasowy nie istnieje"; break;
            case UPLOAD_ERR_PARTIAL : $msg="Plik zostal zuploadowany czesciowo"; break;
            case UPLOAD_ERR_OK: $msg="Brak bledow"; break;
        }

        return $msg;
    }
}