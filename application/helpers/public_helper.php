<?php
function app_info($key=''){
	$app_info = [
		'title' => 'Petrokimia Gresik WMS',
		'description' => 'Warehouse Management System',
		'name' => 'Petrokimia Gresik WMS',
		'shortname' => 'Petrokimia Gresik WMS',
		'client' => [
			'shortname' => 'PG',
			'fullname' => 'Petrokimia Gresik',
			'city' => 'Kabupaten Gresik',
			'category' => 'BUMN'
		],
		'year' => '2019',
		'copyright' => 'Petrokimia Gresik',
		'vendor' => [
			'company' => 'Energeek The E-Government Solution',
			'office' => 'Jl Baratajaya 3/16, Surabaya, Jawa Timur',
			'contact' => [
				'phone' => '+62 856-3306-260',
				'email' => 'aditya.tanjung@energeek.co.id',
				'instagram' => 'https://www.instagram.com/energeek.co.id/'
			],
			'site' => 'http://energeek.co.id/'
		]
	];

	$error=0;
	if(empty($key)){
		$result = $app_info;
	}else{
		$result = false;
		if(is_array($key)){
			$temp = $app_info;
			for ($i=0; $i < count($key); $i++) {
				$error++;
				if(is_array($temp) and count($temp) > 0){
					if(array_key_exists($key[$i], $temp)){
						$error--;
						$result = $temp[$key[$i]];
						$temp = $temp[$key[$i]];
					}
				}
			}
		}else{
			if(array_key_exists($key, $app_info)){
				$result = $app_info[$key];
			}
		}
	}

	if($error > 0){
		$result = false;
	}

	return $result;
}

// function directory($url_path='')
// {
// 	$dir = 'TyhhZ2j4Eq/'.$url_path;
//
// 	return $dir;
// }
/**
 * Function helpNumeric
 * Fungsi ini digunakan untuk mengecek apakah sebuah variabel berisi nilai
   yang bersifat numeric (int, float, double).
 * @access public
 * @param (any) $var
 * @param (int) $res
 * @return (int) {0}
 */
function helpNumeric($var, $res = 0)
{
	return is_numeric($var) ? $var : $res;
}

function helpCreateFolder($directory='', $akses=0755){
	if(!empty($directory)){
		$explode_dir = explode('/', $directory);

		if(count($explode_dir) > 0){
			$temp_directory = '';
			for ($i=0; $i < count($explode_dir); $i++) {
				if(!empty($explode_dir[$i]) && $explode_dir[$i] != '.'){
					$temp_directory .= $explode_dir[$i];

					if(!is_dir($temp_directory)){
						mkdir($temp_directory, $akses);
					}
				}

				$temp_directory .= '/';
			}
		}else{
			if(!is_dir($directory)){
				mkdir($directory, $akses);
			}
		}
	}

	return true;
}

/**
 * Function helpRoman
 * Fungsi ini digunakan untuk merubah angka menjadi bilangan romawi
 * @access public
 * @param (int) $var
 * @return (string) {''}
 */
function helpRoman($var)
{
	$n = intval($var);
	$result = '';
	$lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
		'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
		'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
	foreach ($lookup as $roman => $value) {
		$matches = intval($n / $value);
		$result .= str_repeat($roman, $matches);
		$n = $n % $value;
	}
	return $result;
}

/**
 * Function helpIndoDay
 * Fungsi ini digunakan untuk mencari nama hari dalam bahasa Indonesia
 * @access public
 * @param (int) $var Nomor urut hari yang dimulai dari angka 0 untuk hari senin
 * @return (string) {'Undefined'}
 */
function helpIndoDay($var)
{
	$dayArray = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
	if(array_key_exists($var, $dayArray )){
		return $dayArray[$var];
	}else{
		return 'Undefined';
	}
}

/**
 * Function helpIndoMonth
 * Fungsi ini digunakan untuk mencari nama bulan dalam bahasa Indonesia
 * @access public
 * @param (int) $var Nomor urut bulan yang dimulai dari angka 0 untuk bulan januari
 * @return (string) {'Undefined'}
 */
function helpIndoMonth($num)
{
	$monthArray = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	if(array_key_exists($num, $monthArray)){
		return $monthArray[$num];
	}else{
		return 'Undefined';
	}
}

/**
 * Function helpDate
 * Fungsi ini digunakan untuk melakukan konversi format tanggal
 * @access public
 * @param (date) $var Tanggal yang akan dikonversi
 * @param (string) $mode Kode untuk model format yang baru
   - se (short English)		: (Y-m-d) 2015-31-01
   - si (short Indonesia)	: (d-m-Y) 31-01-2015
   - me (medium English)	: (F d, Y) January 31, 2015
   - mi (medium Indonesia)	: (d F Y) 31 Januari 2015
   - le (long English)		: (l F d, Y) Sunday January 31, 2015
   - li (long Indonesia)	: (l, d F Y) Senin, 31 Januari 2015
 * @return (string) {'Undefined'}
 */

function columnLetter($c){

    $c = intval($c);
    if ($c <= 0) return '';

    $letter = '';
             
    while($c != 0){
       $p = ($c - 1) % 26;
       $c = intval(($c - $p) / 26);
       $letter = chr(65 + $p) . $letter;
    }
    
    return $letter;
        
}

function helpDate($var, $mode = 'se')
{
	switch($mode){
		case 'se':
			return date('Y-m-d', strtotime($var));
		break;
		case 'si':
			return date('d-m-Y', strtotime($var));
		break;
		case 'ri':
			return date('d-m-Y h:i', strtotime($var));
		break;
		case 'me':
			return date('F d, Y', strtotime($var));
		break;
		case 'mi':
			$day = date('d', strtotime($var));
			$month = date('n', strtotime($var));
			$year = date('Y', strtotime($var));

			$month = helpIndoMonth($month - 1);
			return $day.' '.$month.' '.$year;
		break;
		case 'le':
			return date('l F d, Y', strtotime($var));
		break;
		case 'li':
			$dow = date('w', strtotime($var));
			$day = date('d', strtotime($var));
			$month = date('n', strtotime($var));
			$year = date('Y', strtotime($var));

			$hari = helpIndoDay($dow);
			$month = helpIndoMonth($month - 1);
			return $hari .', '. $day.' '.$month.' '.$year;
		break;
		case 'bi':
			$month = date('n', strtotime($var));
			$year = date('Y', strtotime($var));

			$month = helpIndoMonth($month - 1);
			return $month.' '.$year;
		break;
		default:
			return 'Undefined';
		break;
	}
}

/**
 * Function helpSecSql
 * Fungsi ini digunakan untuk merubah variabel menjadi aman sebelum dimasukkan ke dalam database
 * @access public
 * @param (string) $var
 * @return (string)
 */
function helpSecSql($var)
{
	return addslashes(strtolower($var));
}

function helpCurrency($nominal, $start = '', $pemisah = '.', $end = '')
{
	$nominal = empty($nominal) ? 0 : $nominal;
	$angka_belakang = ',00';
	$temp_rp = explode('.', $nominal);

	if ($end == '') {
		$angka_belakang = '';
	}

	if (count($temp_rp) > 1) {
		$nominal = $temp_rp[0];
		$angka_belakang = ',' . $temp_rp[1];
	}

	$hasil = $start . number_format($nominal, 0, "", $pemisah) . $angka_belakang . $end;
	return $hasil;
}

/**
 * Function helpTerbilang
 * Fungsi ini digunakan untuk merubah angka yang dimasukkan menjadi ejaan
 * @access public
 * @param (int) $var
 * @return (string)
 */
function helpTerbilang($num)
{
	$digits = array(
		0 => "nol",
		1 => "satu",
		2 => "dua",
		3 => "tiga",
		4 => "empat",
		5 => "lima",
		6 => "enam",
		7 => "tujuh",
		8 => "delapan",
		9 => "sembilan");
	$orders = array(
		0 => "",
		1 => "puluh",
		2 => "ratus",
		3 => "ribu",
		6 => "juta",
		9 => "miliar",
		12 => "triliun",
		15 => "kuadriliun");

	$is_neg = $num < 0; $num = "$num";

	$int = ""; if (preg_match("/^[+-]?(\d+)/", $num, $m)) $int = $m[1];
	$mult = 0; $wint = "";

	while (preg_match('/(\d{1,3})$/', $int, $m)) {

		$s = $m[1] % 10;
		$p = ($m[1] % 100 - $s)/10;
		$r = ($m[1] - $p*10 - $s)/100;

		if ($r==0) $g = "";
		elseif ($r==1) $g = "se$orders[2]";
		else $g = $digits[$r]." $orders[2]";

		if ($p==0) {
			if ($s==0);
			elseif ($s==1) $g = ($g ? "$g ".$digits[$s] :
			($mult==0 ? $digits[1] : "se"));
			else $g = ($g ? "$g ":"") . $digits[$s];
		} elseif ($p==1) {
			if ($s==0) $g = ($g ? "$g ":"") . "se$orders[1]";
			elseif ($s==1) $g = ($g ? "$g ":"") . "sebelas";
			else $g = ($g ? "$g ":"") . $digits[$s] . " belas";
		} else {
			$g = ($g ? "$g ":"").$digits[$p]." puluh".
			($s > 0 ? " ".$digits[$s] : "");
		}

		$wint = ($g ? $g.($g=="se" ? "":" ").$orders[$mult]:"").
		($wint ? " $wint":"");

		$int = preg_replace('/\d{1,3}$/', '', $int);
		$mult+=3;
	}
	if (!$wint) $wint = $digits[0];
	$frac = ""; if (preg_match("/\.(\d+)/", $num, $m)) $frac = $m[1];
	$wfrac = "";
	for ($i=0; $i<strlen($frac); $i++) {
		$wfrac .= ($wfrac ? " ":"").$digits[substr($frac,$i,1)];
	}
	$hasil= ($is_neg ? "minus ":"").$wint.($wfrac ? " koma $wfrac":"");
	$hasil=str_replace("sejuta","satu juta",$hasil);
	return $hasil;
}

/**
 * Function helpResponse
 * Fungsi ini digunakan untuk mengambil response restful
 * @access public
 * @param (string) $code
 * @param (array) $data
 * @param (string) $msg
 * @param (string) $status
 * @return (array)
 */
function helpResponse($code, $data = NULL, $msg = '', $status = '')
{
	switch($code){
		case '200':
			$s = 'OK';
			$m = 'Sukses';
		break;
		case '201':
		case '202':
			$s = 'Saved';
			$m = 'Data berhasil disimpan';
		break;
		case '204':
			$s = 'No Content';
			$m = 'Data tidak ditemukan';
		break;
		case '304':
			$s = 'Not Modified';
			$m = 'Data gagal disimpan';
		break;
		case '400':
			$s = 'Bad Request';
			$m = 'Fungsi tidak ditemukan';
		break;
		case '401':
			$s = 'Unauthorized';
			$m = 'Silahkan login terlebih dahulu';
		break;
		case '403':
			$s = 'Forbidden';
			$m = 'Sesi anda telah berakhir';
		break;
		case '404':
			$s = 'Not Found';
			$m = 'Halaman tidak ditemukan';
		break;
		case '414':
			$s = 'Request URI Too Long';
			$m = 'Data yang dikirim terlalu panjang';
		break;
		case '500':
			$s = 'Internal Server Error';
			$m = 'Maaf, terjadi kesalahan dalam mengolah data';
		break;
		case '502':
			$s = 'Bad Gateway';
			$m = 'Tidak dapat terhubung ke server';
		break;
		case '503':
			$s = 'Service Unavailable';
			$m = 'Server tidak dapat diakses';
		break;
		default:
			$s = 'Undefined';
			$m = 'Undefined';
		break;
	}

	$status = ($status != '') ? $status : $s;
	$msg = ($msg != '') ? $msg : $m;
	$result=array(
		"status"=>$status,
		"code"=>$code,
		"message"=>$msg,
		"data"=>$data
	);

	setHeader($code,$status);
	return $result;
}

// function dump($var="")
// {
// 	if($var == ""){
// 		echo "No value to return.";
// 	} else {
// 		echo "<pre>";
// 		print_r($var);
// 		echo "</pre>";
// 	}
// }

function rand_str($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function clean($var)
{
	$var = htmlspecialchars($var, ENT_QUOTES, "ISO-8859-1");
	return $var;
}

function setHeader($code='200', $status='')
{
	header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$status);
}

function helpToNum($data) {
	$alphabet = array( 'a', 'b', 'c', 'd', 'e',
		'f', 'g', 'h', 'i', 'j',
		'k', 'l', 'm', 'n', 'o',
		'p', 'q', 'r', 's', 't',
		'u', 'v', 'w', 'x', 'y',
		'z'
	);
	$alpha_flip = array_flip($alphabet);
	$return_value = -1;
	$length = strlen($data);
	for ($i = 0; $i < $length; $i++) {
		$return_value +=
		($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
	}
	return $return_value;
}

function toNum($data) {
	$alphabet = array( 'a', 'b', 'c', 'd', 'e',
		'f', 'g', 'h', 'i', 'j',
		'k', 'l', 'm', 'n', 'o',
		'p', 'q', 'r', 's', 't',
		'u', 'v', 'w', 'x', 'y',
		'z'
	);
	$alpha_flip = array_flip($alphabet);
	$return_value = -1;
	$length = strlen($data);
	for ($i = 0; $i < $length; $i++) {
		$return_value +=
		($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
	}
	return $return_value;
}

function toAlpha($data){
	$alphabet =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	$alpha_flip = array_flip($alphabet);
	if($data <= 25){
		return $alphabet[$data];
	}
	elseif($data > 25){
		$dividend = ($data + 1);
		$alpha = '';
		$modulo=0;
		while ($dividend > 0){
			$modulo = ($dividend - 1) % 26;
			$alpha = $alphabet[$modulo] . $alpha;
			$dividend = floor((($dividend - $modulo) / 26));
		}
		return $alpha;
	}
}

function helpEmpty($value = '', $replace_with = '-', $null = false)
{
	if ($null == false) {
		$result = (empty($value) && $value != '0') ? $replace_with : $value;
	} else {
		$result = (empty($value) && $value != '0') ? '' : $value;
	}

	return $result;
}

function myBasePath($replace = '', $to = '')
{
	$root = $_SERVER['DOCUMENT_ROOT'];
	$root .= preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])) . '/';

	if (!empty($replace)) {
		$root = str_replace($replace, $to, $root);
	}

	return $root;
}

function protectPath($value = '')
{
	$arr_forbidden = ['../', '..'];
	return str_replace($arr_forbidden, '', $value);
}
