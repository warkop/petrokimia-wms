<?php
function base_url($value='')
{
	$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
	$base_url .= "://".$_SERVER['HTTP_HOST'];
	$base_url .= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';

	return $base_url;
}

function pertagasStorage($path=false)
{
	$base_url = $_SERVER['DOCUMENT_ROOT'].'/pertagas-qrtg-api/application/storage/app/public/assets/';

	if(!empty($path)){
		$base_url .= $path;
	}

	return $base_url;
}

function main_path()
{
	$base_url = $_SERVER['DOCUMENT_ROOT'].'/pertagas-qrtg-api/';

	return $base_url;
}

function directory($url_path='', $root='i49dne8')
{
	$dir = $root.'/'.$url_path;

	return $dir;
}

function aset_tema($url_path='')
{
	$dir = base_url().'assets/main/metronic/'.$url_path;

	return $dir;
}

function aset_extends($url_path='')
{
	$dir = base_url().'assets/extends/'.$url_path;

	return $dir;
}

function zip_directory($path='', $zip_name='', $target_dir='')
{
	$zip_name = empty($zip_name)? date('YmdHis').'.zip' : $zip_name.'.zip';
	// if(file_exists($target_dir.$zip_name) and !is_dir($target_dir.$zip_name)){
	// 	unlink($target_dir.$zip_name);
	// 	echo 'a';
	// }
	// Get real path for our folder
	$rootPath = realpath($path);

	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open($target_dir.$zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ($files as $name => $file)
	{
    // Skip directories (they would be added automatically)
		if (!$file->isDir())
		{
        // Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}

	// Zip archive will be created only after closing object
	$zip->close();
}

function helpCreateDirectory($directory='', $akses=0755){
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
?>
