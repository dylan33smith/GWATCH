<?php

namespace App\Controller;

use App\Repository\DisplayRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
#use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\DriverManager;
define("USECACHEFILE", false);

class DisplayController extends AbstractController 
{
 
   private function createPDOConnection(EntityManagerInterface $entityManager)
   {
       $connection = $entityManager->getConnection();
       $params = $connection->getParams();
       
       $dsn = sprintf(
           'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
           $params['host'] ?? '127.0.0.1',
           $params['port'] ?? 3306,
           $params['dbname'] ?? 'gwatch_db'
       );
       
       return new \PDO($dsn, $params['user'], $params['password']);
   }
   
   public function indexAction( Request $request, EntityManagerInterface $entityManager)
    {
   		
   		$DisplayRepository = New DisplayRepository($this->createPDOConnection($entityManager));
   
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
 		$data['module'] = $module;
 		$data['chr'] = $chr;	 
 
		$data['polarized'] = 'false'; // Always false for new schema
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
    public function columnsAction( Request $request, EntityManagerInterface $entityManager)
    {
 		$DisplayRepository = New DisplayRepository($this->createPDOConnection($entityManager));
 		
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
		
  
		$colInfo = $DisplayRepository->ReadColumnInfo( $module );

		$rowCount = $DisplayRepository->ReadRowCount( $module, $chr );

		// throw the row count in for good measure
		$colInfo[ "NumRows" ] = $rowCount;

		return new JsonResponse($colInfo);
		

	}
	public function dataAction( Request $request, EntityManagerInterface $entityManager){
	
	
		$DisplayRepository = New DisplayRepository($this->createPDOConnection($entityManager));
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
	


	
	// Removed compareLinkAction - not needed for new schema
	
	
	// Removed polarizationAction - not needed for new schema
	
	
	
	
	public function searchAction( Request $request, EntityManagerInterface $entityManager){
	
	 	$DisplayRepository = New DisplayRepository($entityManager->getConnection());
		$module = $request->query->get('module');
		$chr = $request->query->get('chr');
		if ( ! $module )
		{
			return new JsonResponse(['error' => 'No module specified'], 400);
		}
		if ( ! $chr )
		{
			return new JsonResponse(['error' => 'No chr specified'], 400);
		}
		$type = trim($request->query->get('type'));
		$searchTerm = trim($request->query->get('search'));
		$searchType = $DisplayRepository->GetSearchType($searchTerm);

		if ( !isset( $searchTerm ) || !isset( $searchType ) ) 
		{
			return new JsonResponse(['error' => 'Missing Params'], 400);
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
	
	public function reportAction( Request $request, EntityManagerInterface $entityManager){
	
		$DisplayRepository = New DisplayRepository($this->createPDOConnection($entityManager));
 	
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
			// Removed snapshot-polarized - not needed for new schema
			// Removed TRAX report generation - not needed for new schema
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
				return new Response($svgContent, 200, ['Content-Type' => 'image/svg+xml']);
			}
			else if ( $format == "pdf" )
			{
				// PDF generation logic here - simplified for new schema
				return new Response("PDF generation not implemented", 501);
			}
 return new Response('');
}
	
	// Removed topHitsAction - not needed for new schema
	

	
}