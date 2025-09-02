<?php
// display.php - Entry point for highway browser display
// Routes to DisplayController::indexAction()

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use App\Controller\DisplayController;
use Doctrine\ORM\EntityManagerInterface;

// Create a simple request object from GET parameters
$request = Request::createFromGlobals();

// For now, we'll create a minimal response that includes the display template
// This is a temporary solution until we can properly integrate with Symfony routing

$module = $request->query->get('module', '54');
$chr = $request->query->get('chr', '1');
$row = $request->query->get('row', '0');
$distance = $request->query->get('distance', '80');
$anglex = $request->query->get('anglex', '0');
$offsetx = $request->query->get('offsetx', '0');
$eyepos = $request->query->get('eyepos', '[0,40,70]');

// Set up basic data for the template
$data = [
    'module' => $module,
    'chr' => $chr,
    'UrlRow' => $row,
    'distance' => $distance,
    'anglex' => $anglex,
    'offsetx' => $offsetx,
    'eyepos' => $eyepos,
    'CurrentURL' => 'http://localhost:8000/display.php',
    'BuildAndPlatform' => ['build' => '37', 'platform' => 'Test'],
    'Polarize' => false,
    'polarized' => 'false'
];

// Load the Twig template
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

// Render the template
$template = $twig->load('display.html.twig');
echo $template->render($data);
