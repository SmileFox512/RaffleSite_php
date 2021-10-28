<?php



    session_start();
 

      
$REG['BACKGROUND_COLOR_RED'] = 255;
$REG['BACKGROUND_COLOR_GREEN'] = 255;
$REG['BACKGROUND_COLOR_BLUE'] = 255;
$REG['TEXT_COLOR_RED'] = 0;
$REG['TEXT_COLOR_GREEN'] = 0;
$REG['TEXT_COLOR_BLUE'] = 0;

//
// These are the dimensions for the registration key(verification image)
//

$REG['KEY_WIDTH'] = 56;
$REG['KEY_HEIGHT'] = 17;


$key = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);

 $_SESSION['jpeg_code'] = md5(md5($key));


header("Content-type: image/png");
$im = @imagecreate($REG['KEY_WIDTH'], $REG['KEY_HEIGHT'])
   or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, $REG['BACKGROUND_COLOR_RED'], $REG['BACKGROUND_COLOR_GREEN'], $REG['BACKGROUND_COLOR_BLUE']);
$text_color = imagecolorallocate($im, $REG['TEXT_COLOR_RED'], $REG['TEXT_COLOR_GREEN'], $REG['TEXT_COLOR_BLUE']);
imagestring($im, 5, 1, 1,  $key, $text_color);
imagepng($im);
imagedestroy($im);


?>