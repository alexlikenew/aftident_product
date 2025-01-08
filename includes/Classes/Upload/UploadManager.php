<?php


namespace Classes\Upload;


class UploadManager
{
    /**
     * Tablica z plikami
     *
     * @var array
     */
    protected static $files=array();

    /**
     * Zebralismy dane o plikach ?
     *
     * @var bool
     */

    protected static $collected=false;

    /**
     * Konstruktor
     *
     * @throws uploadException Jesli upload nie ma plikow
     */

    private static function collect()
    {
        self::$files=$_FILES;
        unset($_FILES);
        self::$collected=true;
    }

    /**
     * Sprawdza czy zostaly zuploadowane jakiekolwiek pliki
     *
     * @return bool
     */
    public static function hasFiles()
    {
        if(!self::$collected) self::collect();

        return (count(self::$files)>0);
    }

    /**
     * Sprawdza czy plik o podanym kluczu jest zuploadowany
     *
     * @param string $name Klucz pliku
     * @return bool
     */

    public static function has($name)
    {
        if(!self::$collected) self::collect();

        return (isset(self::$files[$name]['name']) && ((int)self::$files[$name]['error']==0));
    }

    /**
     * Zwraca plik o podanym kluczu jako obiekt albo wyrzuca wyjatek
     *
     * @param string $name Klucz pliku
     * @return uploadedFile Jesli plik o podanym kluczu istnieje
     * @throws uploadException Jesli takiego pliku nie ma
     */

    public static function get($name)
    {
        if(!self::$collected) self::collect();

        if(self::has($name))
        {
            $type=(isset(self::$files[$name]['type'])) ? self::$files[$name]['type'] : '' ;

            return new UploadFile(self::$files[$name]);
        }
        else
        {
            throw new UploadException("Nie ma pliku o kluczu <b>$name</b>.");
        }
    }

}