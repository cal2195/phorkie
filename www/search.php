<?php
namespace phorkie;
/**
 * Search for a search term
 */
require_once 'www-header.php';

if (!isset($_GET['q']) || $_GET['q'] == '') {
    header('Location: ' . Tools::fullUrl('/list'));
    exit();
}
$query = $_GET['q'];

$page = 0;
if (isset($_GET['page'])) {
    if (!is_numeric($_GET['page'])) {
        throw new Exception_Input('List page is not numeric');
    }
    //PEAR Pager begins at 1
    $page = (int)$_GET['page'] - 1;
}
$perPage = 10;

$db     = new Database();
$search = $db->getSearch();

$sres = $search->search($query, $page, $perPage);

//fix non-static factory method error
error_reporting(error_reporting() & ~E_STRICT);
$pager = \Pager::factory(
    array(
        'mode'        => 'Sliding',
        'perPage'     => 10,
        'delta'       => 2,
        'totalItems'  => $sres->getResults(),
        'currentPage' => $page + 1,
        'urlVar'      => 'page',
        'path'        => '/',
        'fileName'    => $sres->getLink($query),
    )
);

render(
    'search',
    array(
        'query' => $query,
        'sres'  => $sres,
        'pager' => $pager
    )
);
?>