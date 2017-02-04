<?
$prefix     = 'wp_';
$dbId       = 'wp';
$configFile = array( ConfigDir.'/Db/wordpress.master.config.php' );

$tbl['options'] = array(
    'name'      => $prefix.'options',
    'dbId'      => $dbId, 
    'configFile'=> $configFile,
);
$tbl['postmeta'] = array(
    'name'      => $prefix.'postmeta',
    'dbId'      => $dbId,
    'configFile'=> $configFile,
);
$tbl['posts'] = array(
    'name'      => $prefix.'posts',
    'dbId'      => $dbId,
    'configFile'=> $configFile,
);
$tbl['termRelationships'] = array(
    'name'      => $prefix.'term_relationships',
    'dbId'      => $dbId,
    'configFile'=> $configFile,
);
$tbl['termTaxonomy'] = array(
    'name'      => $prefix.'term_taxonomy',
    'dbId'      => $dbId,
    'configFile'=> $configFile,
);
$tbl['terms'] = array(
    'name'      => $prefix.'terms',
    'dbId'      => $dbId,
    'configFile'=> $configFile,
);
?>