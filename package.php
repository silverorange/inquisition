<?php

require_once 'PEAR/PackageFileManager2.php';

$version = '0.0.7';
$notes = <<<EOT
see ChangeLog
EOT;

$description =<<<EOT
nobody expects the inquisition package
EOT;

$package = new PEAR_PackageFileManager2();
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$result = $package->setOptions(
	array(
		'filelistgenerator' => 'svn',
		'simpleoutput'      => true,
		'baseinstalldir'    => '/',
		'packagedirectory'  => './',
		'dir_roles'         => array(
			'Inquisition'   => 'php',
			'sql' => 'data',
			'/' => 'data'
		),
	)
);

$package->setPackage('Inquisition');
$package->setSummary('A quiz/survey tool');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense('LGPL', 'http://www.gnu.org/copyleft/lesser.html');

$package->setReleaseVersion($version);
$package->setReleaseStability('beta');
$package->setAPIVersion('0.0.1');
$package->setAPIStability('beta');
$package->setNotes($notes);

$package->addIgnore('package.php');

$package->addMaintainer('lead', 'nrf', 'Nathan Fredrickson', 'nathan@silverorange.com');
$package->addMaintainer('developer', 'gauthierm', 'Mike Gauthier', 'mike@silverorange.com');

$package->addReplacement('Inquisition/Inquisition.php', 'pear-config', '@DATA-DIR@', 'data_dir');

$package->setPhpDep('5.1.5');
$package->setPearinstallerDep('1.4.0');
$package->addPackageDepWithChannel('required', 'Swat', 'pear.silverorange.com', '1.4.65');
$package->addPackageDepWithChannel('optional', 'MDB2', 'pear.php.net',          '2.2.2');
$package->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
