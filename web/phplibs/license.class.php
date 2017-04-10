<?php
class License
{
    public static function check($licensetype) {
        $licenses = Session::getLicense();
        if($licenses[$licensetype] > 0){
            return true;
        }
        return false;
    }

    public static function hasDatrixLicense() {
        return License::check('datrix');
    }

    public static function hasFullTextLicense() {
        return License::check('fulltext');
    }

}
?>
