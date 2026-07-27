<?
// start a session to remember this code
@session_start();

$img_x          = 100;   //Ширина изображения, по умолчанию-100
$img_y          = 30;   //Высота изображения, по умолчанию-30
$font_min_size  = 18;   //Минимальный размер шрифта, по умолчанию-12
$font_max_size  = 24;   //Максимальный размер шрифта, по умолчанию-12
$lines_n_max    = 2;    //Максимальное число шумовых линий, по умолчанию-2
$nois_percent   = 5;    //Зашумленность цветами фона и текста, в процентах, по умолчанию-3
$angle_max      = 3;   //Максимальный угол отклонения от горизонтали по часовой стрелке и против, по умолчанию-20
// В папке /php/fonts/*.ttf лежат несколько шрифтов ttf
$font_arr=glob(dirname(__FILE__)."/fonts/*.ttf");
//echo $font_arr;
$im=imagecreate($img_x, $img_y);
//создаем необходимые цвета
$text_color = imagecolorallocate($im, 0, 0, 0);       //цвет текста
$nois_color = imagecolorallocate($im, 0, 0, 0);       //цвет зашумляющих точек и линий
$img_color  = imagecolorallocate($im, 255, 255, 255); //цвет фона
//заливаем изображение фоновым цветом
imagefill($im, 0, 0, $img_color);
//В переменной $number будет храниться число, показанное на изображении
$number='';
$number=(empty($_SESSION['registr_key']))?'0104':$_SESSION['registr_key'];
$num_n=strlen($number);

// рандомизировать микросекундами
function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
srand(make_seed());
$code="";

#$nums=explode("",$number);
for ($n=0; $n<$num_n; $n++){
	//$num=substr($number,$n,1);
    $num = rand(0,9);
    $code .= $num;
	
    $font_size=rand($font_min_size, $font_max_size);//$img_y/2
    $angle=rand(360-$angle_max,360+$angle_max);

    $font_cur=rand(0,count($font_arr)-1);
    $font_cur=$font_arr[$font_cur];
    //вычисление координат для каждой цифры, формулы обеспечивают нормальное расположние
    //при любых значениях размеров цифры и изображения
    $y=rand(($img_y-$font_size)/4+$font_size, ($img_y-$font_size)/2+$font_size);

    $x=rand(($img_x/$num_n-$font_size)/2, $img_x/$num_n-$font_size)+$n*$img_x/$num_n;

	$text_color = imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100));       //цвет текста

    imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_cur, $num);
};

//Вычисляем число "зашумленных" пикселов
$nois_n_pix=round($img_x*$img_y*$nois_percent/100);
//зашумляем изображение пикселами цвета текста
for ($n=0; $n<$nois_n_pix; $n++){
    $x=rand(0, $img_x);
    $y=rand(0, $img_y);
	$nois_color = imagecolorallocate($im, rand(20, 255), rand(20, 255), rand(20, 255));       //цвет зашумляющих точек и линий
    imagesetpixel($im, $x, $y, $nois_color);
};
//зашумляем изображение пикселами фонового цвета
for ($n=0; $n<$nois_n_pix/2; $n++){
    $x=rand(0, $img_x);
    $y=rand(0, $img_y);
    imagesetpixel($im, $x, $y, $img_color);
};

$lines_n=rand(0,$lines_n_max);
//проводим "зашумляющие" линии цвета текста
for ($n=0; $n<$lines_n; $n++){
    $x1=rand(0, $img_x);
    $y1=rand(0, $img_y);
    $x2=rand(0, $img_x);
    $y2=rand(0, $img_y);
    $nois_color = imagecolorallocate($im, rand(20, 255), rand(20, 255), rand(20, 255));       //цвет зашумляющих точек и линий

    imageline($im, $x1, $y1, $x2, $y2, $nois_color);
};

$_SESSION['random_code'] = md5(strtolower($code));

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?>