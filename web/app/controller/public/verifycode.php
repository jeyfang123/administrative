<?php
/**
 * Created by PhpStorm.
 * User: jey
 * Date: 2017/5/16
 * Time: 9:43
 */
class VerifyCode {
    private $width;
    private $height;
    private $fontFile;
    private $type;
    private $size;
    private $image;
    private $text;
    private $fontSize = 20;
    private $disturbPixel = 50;
    private $disturbLine = 2;
    private $disturbCircle = 2;
    const VERIFY_NUMBER = 1;
    const VERIFY_LETTER = 2;
    const VERIFY_MIX = 3;
    const VERIFY_CHARACTERS = 4;

    function __construct($width = 200, $height = 50, $type = VerifyCode::VERIFY_CHARACTERS, $size = 4){
        $this->width = $width;
        $this->height = $height;
        $this->fontFile = __DIR__.DIRECTORY_SEPARATOR.'codefont.ttc';
        $this->type = $type;
        $this->size = $size;
        $this->image = imagecreatetruecolor($this->width,$this->height);
    }

    //绘制验证码
    function getImage(){
        //获得白色画笔
        $whiteBack = imagecolorallocate($this->image,255,255,255);
        //填充为白色
        imagefilledrectangle($this->image,0,0,$this->width,$this->height,$whiteBack);
        //绘制字符串
        $textArr = $this->getText();
        session_start();
        $_SESSION['verifyCode'] = $this->text;
        //绘制字符
        for($i=0; $i<$this->size; $i++){
            imagettftext($this->image,$this->fontSize,mt_rand(-30,30),
                30+(imagefontwidth($this->fontSize)+20)*$i,
                mt_rand(30,$this->height-10),
                $this->getRandColor(),
                $this->fontFile,
                $textArr[$i]
            );
        }
    }

    //添加干扰元素
    function addDisturb(){
        for($i=0; $i<$this->disturbPixel;$i++){
            imagesetpixel($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),$this->getRandColor());
        }
        for($i=0; $i<$this->disturbLine;$i++){
            imageline($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$this->getRandColor());
        }
        for($i=0; $i<$this->disturbCircle;$i++){
            imagearc($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,360),mt_rand(0,360),$this->getRandColor());
        }
    }

    //输出
    function printImage(){
        $this->getImage();
        $this->addDisturb();
        header('Content-Type:image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    //获取验证码字符串
    function getText(){
        switch ($this->type) {
            case VerifyCode::VERIFY_NUMBER:
                $textArr = array_rand(array_flip(range(0, 9)), $this->size);
                break;
            case VerifyCode::VERIFY_LETTER:
                $textArr = array_rand(array_flip(array_merge(range('a', 'z'), range('A', 'Z'))), $this->size);
                break;
            case VerifyCode::VERIFY_CHARACTERS:
                $characters = '我们感觉不到地球转动的原因同上述事件类似如果地球自转突然加速或减速那么你一定会感觉到地球的恒速旋转旋转使我们的祖先对于宇宙的真相感到很困惑他们注意到星星太阳和月亮似乎都在地球上空移动因为感觉不到地球运动所以逻辑地推理地球是静止的整个天空都在围绕着地球转动早期的希腊科学家阿利斯塔克在公元前几百年前首先提出了日心说宇宙模型那个时候世界上伟大的思想家坚持宇宙地心说已经好几个世纪了直到世哥白尼的日心模型才开始被讨论和理解虽然也有错误之处哥白尼的模型最终证明了在这个世界地球是在恒星下面依照自己的轴线转动当然也在太阳周围的轨道内转动';
                $arr = $this->ch2arr($characters);
                $textArr = array_rand(array_flip($arr), $this->size);
                break;
            case VerifyCode::VERIFY_MIX:
                $textArr = array_rand(array_flip(array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'))), $this->size);
                break;
            default:
                $textArr = array_rand(array_flip(range(0, 9)), $this->size);
                break;
        }
        shuffle($textArr);
        $this->text = join('',$textArr);
        return $textArr;
    }

    //获取随机颜色
    function getRandColor(){
        return imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
    }

    private function ch2arr($str){
        $length = mb_strlen($str, 'utf-8');
        for($i=0; $i<$length; $i++){
            $arr[] = mb_substr($str,$i,1,'utf-8');
        }
        return array_unique($arr);
    }
}