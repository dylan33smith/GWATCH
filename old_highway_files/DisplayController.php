<?php

namespace App\Controller;

use App\Repository\DisplayRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
#use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Doctrine\DBAL\Driver\Connection;
define("USECACHEFILE", false);

class DisplayController extends AbstractController 
{
 
   
   public function indexAction( Request $request, Connection $connection)
    {
   		
   		$DisplayRepository = New DisplayRepository($connection);
   
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
 		$data['module'] = $module;
 		$data['chr'] = $chr;	 
 
		$data['polarized'] = ( $request->query->get('polarized') ? 'true' : 'false' );
		$data['UrlRow'] = $DisplayRepository->getCurrentRow($request->query->get('row'));
		$data['distance'] =  $DisplayRepository->getDistance($request->query->get('distance'));
		$data['anglex'] = $DisplayRepository->getAngleX($request->query->get('anglex'));
    	$data['offsetx'] = $DisplayRepository->getOffsetX($request->query->get('offsetx'));
		$data['eyepos'] = $DisplayRepository->getEyePos($request->query->get('eyepos'));
 		$data['CurrentURL'] = $DisplayRepository->getCurrentURL();
 		$data['BuildAndPlatform'] = $DisplayRepository->GetBuildAndPlatform($module);
 		$data['Polarize'] = $DisplayRepository->isPolarization($module);
 		 
 		
 	
    
		 return $this->render('display.html.twig', $data);

	}
    public function columnsAction( Request $request, Connection $connection)
    {
 		$DisplayRepository = New DisplayRepository($connection);
 		
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
		
  
		$colInfo = $DisplayRepository->ReadColumnInfo( $module );

		$rowCount = $DisplayRepository->ReadRowCount( $module, $chr );

		// throw the row count in for good measure
		$colInfo[ "NumRows" ] = $rowCount;

		return new JsonResponse($colInfo);
		

	}
	public function dataAction( Request $request, Connection $connection){
	
	
		$DisplayRepository = New DisplayRepository($connection);
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
 		$data['module'] = $module;
 		$data['chr'] = $chr;

		$rowDataStart = $request->query->get('rowStart');
		$rowDataCount = $request->query->get('rowCount');
		$threshold = $request->query->get('threshold');



		if (!isset($rowDataStart) || !isset($rowDataCount))
		{
	
			$DisplayRepository->ReturnError("Missing Params");
	
		}

	 

		if (USECACHEFILE)
		{

			// if there is a cache file that matches the data we require,
			// just return that.

			$cacheFileName = "/home/newgwatch/cache/$module.$chr.$rowDataStart.$rowDataCount.cache";
			if (file_exists($cacheFileName))
			{
				echo file_get_contents($cacheFileName);
				exit(0);
			}
		
		}

 
		$colInfo = $DisplayRepository->ReadColumnInfo($module);

		// echo json_encode( $colInfo );

		$numColumns = $colInfo["NumColumns"];
		$numDataColumns = $colInfo["NumDataColumns"];
 
		$rowCount =$DisplayRepository->ReadRowCount($module, $chr);
 
		// true means return the values as heights

		$dataBlock = $DisplayRepository->ReadRowData($module, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, 1, 6.0);
 
	//print_r($dataBlock);	 
	
		return new JsonResponse($dataBlock);

		if (USECACHEFILE)
		{

			// We write to a temp file and then rename it to
			// the right file, so that if two clients are trying
			// to write the same cache file, only one will
			// succeeed and we can ensure it is valid.

			$uniqer = substr(md5(uniqid(rand() , 1)) , 0, 5);
			$tmpFile = "$cacheFileName.$uniqer";

			// dump the cache to the temp file

			$cacheHandle = fopen($tmpFile, "w");
			fputs($cacheHandle, $json);
			fclose($cacheHandle);

			// rename it to the right name

			if (!rename($tmpFile, $cacheFileName))
			{

				// somebody else beat us to it?
				// just delete the temp file

				unlink($tmpFile);
			}
				
		}
		
		
	
	}
	


	
	public function compareLinkAction( Request $request, Connection $connection ){
	
		$DisplayRepository = New DisplayRepository($connection);
		$module = $request->query->get('module');
		$chr = $request->query->get('chr');
		$pos = $request->query->get('pos');
	
		if ( !$module  || !$chr || !$pos){

			echo "Wrong params";

		}
		else{

			$str = $DisplayRepository->createCompareLinks($module, $chr, $pos);
			
			 
			
			if($str) {
				foreach($str as $v)
					$data['links'][] =  '<a href="/display.php?chr='. $v["chr"] .'&module='. $v["module"] .'&row='. $v["nrow"] .'">Module_'. $v["module"] .'</a>';
			}
			else $data['links'][] = 'Not linked';
		
		}
 		
 		//print_r($data['links']);
 		
 		return $this->render('compare.html.twig', $data);
	
	
	}
	
	
	public function polarizationAction( Request $request, Connection $connection){
	
		$module = $request->query->get('module');
		$chr = $request->query->get('chr');
		$row = $request->query->get('row');
		$DisplayRepository = New DisplayRepository($connection);
	
		$polData = $DisplayRepository->getPolarizationData($module, $chr,  $row);
	
		 
		return new JsonResponse($polData);
	
	}
	
	
	
	
	public function searchAction( Request $request, Connection $connection){
	
	 	$DisplayRepository = New DisplayRepository($connection);
		$module = $request->query->get('module');
		$chr = $request->query->get('chr');
		if ( ! $module )
		{
			ReturnError("No module specified ");
		}
		if ( ! $chr )
		{
			ReturnError("No chr specified ");
		}
		$type = trim($request->query->get('type'));
		$searchTerm = trim($request->query->get('search'));
		$searchType = $DisplayRepository->GetSearchType($searchTerm);

		if ( !isset( $searchTerm ) || !isset( $searchType ) ) 
		{
		
			ReturnError("Missing Params");
		}



		$colInfo = $DisplayRepository->ReadColumnInfo( $module );

		$numColumns = $colInfo[ "NumColumns" ];

		$numDataColumns = $colInfo[ "NumDataColumns" ];

		$rowCount = $DisplayRepository->ReadRowCount( $module, $chr );

	

 		//$chrdescr =	$app['repository.display']->getChrDescription($module);
		
		return new JsonResponse( $DisplayRepository->search($module, $chr, $searchTerm, $searchType, $type) );
		
 

	
	}
	
	
	
	public function imgcolorAction( Request $request){
	
		header('Content-Type: image/png');

		$w = $request->query->get('width');
		$h = $request->query->get('height');

		if ($w<825) {
			$width = 400;
			$fs = 50;
			$fs_small=10;
			$temp_x = 30;
			$y = 90;
		} else {
			$width = 800;
			$fs = 80;
			$fs_small=20;
			$temp_x = 140;
			$y = 110;

		}

		$im = imagecreatetruecolor($width, 120);
		imagesavealpha($im, true);
		$black = imagecolorallocate($im, 0,0,0);
		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 128, 128, 128);

		$t_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagefill($im, 0, 0, $t_colour);

		$colors = array(imagecolorallocate($im,245,185,15), imagecolorallocate($im,65,107,236), imagecolorallocate($im,48,149,84),imagecolorallocate($im,208,79,53));
		shuffle($colors);

		$text = 'GWATCH';
		$text2 = 'Genome-Wide Association Tracks Chromosome Highway';
		$arr1 = str_split($text); 
		$font = "/home/gwatch/htdocs/font/Artifika-Regular.ttf";
 
		for($i = 0; $i < count($arr1); ++$i) {
			if ($i==1 or $i==5) {
				$box = imagettftext($im, $fs, 0, $temp_x, 75, $grey, $font, $arr1[$i]);
				$temp_x += $box[2] - $box[0];
			} elseif ($i==4) {
				$box = imagettftext($im, $fs, 0, $temp_x, 75, $colors[1], $font, $arr1[$i]);
				$temp_x += $box[2] - $box[0];
			} else {
				$box = imagettftext($im, $fs, 0, $temp_x, 75, $colors[$i], $font, $arr1[$i]);
				$temp_x += $box[2] - $box[0];
			}
		}

		imagettftext($im, $fs_small, 0, 0, $y, $black, $font, $text2);
		imagepng($im);
		imagedestroy($im);

	
	}

	public function smallImgcolorAction( Request $request)
	{
		header('Content-Type: image/png');

		$h = 30;
		$w = 100;

		$im = imagecreatetruecolor(180, 25);
		imagesavealpha($im, true);

		$t_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagefill($im, 0, 0, $t_colour);

 
		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 128, 128, 128);
		$black = imagecolorallocate($im, 0, 0, 0);
		$colors = array(imagecolorallocate($im,245,185,15), imagecolorallocate($im,65,107,236), imagecolorallocate($im,48,149,84),imagecolorallocate($im,208,79,53));
		shuffle($colors);


		$text = 'GWATCH';
		$arr1 = str_split($text);
		 
		
		$font = system("pwd")."/font/Artifika-Regular.ttf";

		$temp_x = 0;
		for($i = 0; $i < count($arr1); ++$i) {
			if ($i==1 or $i==5) {
				$box = imagettftext($im, 25, 0, $temp_x, 25, $grey, $font, $arr1[$i]);
				$temp_x += $box[2] - $box[0];
			} elseif ($i==4) {
				$box = imagettftext($im, 25, 0, $temp_x, 25, $colors[1], $font, $arr1[$i]);
				$temp_x += $box[2] - $box[0];
			} else {
				$box = imagettftext($im, 25, 0, $temp_x, 25, $colors[$i], $font, $arr1[$i]);
				$temp_x += $box[2] - $box[0];
			}
		}



		imagepng($im);
		imagedestroy($im);
		 

	}
	
	public function reportAction( Request $request, Connection $connection){
	
		$DisplayRepository = New DisplayRepository($connection);
 	
		$reportType = trim($request->query->get('reportType'));
			if ( $reportType == "array" )
			{
			//echo 1;
 				$svgContent = $DisplayRepository->generateGWASArray($request);
				$report = '2D';
			}
			else if ( $reportType == "snapshot" )
			{
				$svgContent = $DisplayRepository->generateGWASSnapshot( $request, false );
				$report = '3D';
			}
			else if ( $reportType == "snapshot-polarized" )
			{
				$svgContent = $DisplayRepository->generateGWASSnapshot( $request, true );
				$report = '3D_pol';
			}
			else if ( $reportType == "trax" )
			{
				$data = $DisplayRepository->generateTRAXreport( $request );
				
				//print_r($data);
				
				return $this->render('traxreport.html.twig', $data);
				exit;
 
 			}
			else if ( $reportType == "lde" )
			{
				$svgContent = $DisplayRepository->generateLDSnapshot( $request );
 
 			}
			else
			{
				echo "<h3>Unknown report type!</h3>";
				exit(0);
			}





			$format = trim($request->query->get('format'));
			$module = trim($request->query->get('module'));
			$chr = trim($request->query->get('chr'));
			$row = trim($request->query->get('row'));
 			if ( $format == "svg")
			{
				// just dump the SVG file straight into the browser
				header('Content-type: image/svg+xml');
				echo $svgContent;
			}
			else if ( $format == "pdf" )
			{

				// write it to a temp file
				$tempFileName = "temp/" . substr(md5(uniqid(rand(),1)),0,5) . ".svg";
				$svgFile = fopen( $tempFileName, "w");
				fputs( $svgFile, $svgContent );
				fclose( $svgFile );
	
				// kick batik to convert it do a PDF
				$command = "java -Djava.awt.headless=true -Xms128m -Xmx512m -jar ./batik/batik-rasterizer.jar -m application/pdf $tempFileName 2>&1";
				$result = exec($command);
				if ( strpos( $result, "success" ) )
				{

					header('Content-type: application/pdf');
					header("Content-Disposition: attachment; filename=$report"."_". $module ."_". $chr ."_". $row .".pdf");
											 
					$pdfFile = str_replace( ".svg", ".pdf", $tempFileName );
		
					echo file_get_contents ( $pdfFile );
		
					unlink( $pdfFile );
		
				}
				else
				{
					echo $result;
				}

			unlink( $tempFileName );
 		}
 return new Response('');
}
	
    public function topHitsAction( Request $request, Connection $connection)
    {
    	$DisplayRepository = New DisplayRepository($connection);
  
  		$data['tophits'] = $DisplayRepository->generateTopHitsReport( $request );
  		return $this->render('tophits.html.twig',$data);
	}
	

	
}