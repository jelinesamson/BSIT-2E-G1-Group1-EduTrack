<?php
/**
 * EduTrack — Sales Report API
 * File: Api/salesReport.php
 *
 * Uses: SELECT * FROM product_journal WHERE status = 'sales'
 * GET params:
 *   ?month=YYYY-MM   (default: current month)
 *   ?type=Uniforms   (optional, filter by product type)
 */

header('Content-Type: application/json');

include('config.php'); // gives $conn

// ── helpers ───────────────────────────────────────────────────────────────────
function ok($data)  { echo json_encode(['success'=>true]  + $data); exit; }
function err($msg)  { echo json_encode(['success'=>false,'error'=>$msg]); exit; }

if (!$conn) err('DB connection failed.');

// ── params ────────────────────────────────────────────────────────────────────
$month = (isset($_GET['month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month']))
       ? $_GET['month'] : date('Y-m');

$typeFilter = isset($_GET['type']) ? mysqli_real_escape_string($conn, trim($_GET['type'])) : '';

$monthStart = $month . '-01';
$monthEnd   = date('Y-m-t', strtotime($monthStart));

// ── available months ──────────────────────────────────────────────────────────
$mRows = mysqli_query($conn,
    "SELECT DISTINCT DATE_FORMAT(sold_at,'%Y-%m') AS m
     FROM product_journal WHERE status='sales' ORDER BY m DESC");
$months = [];
while ($r = mysqli_fetch_assoc($mRows)) $months[] = $r['m'];
if (!$months) $months = [$month];

// ── product types ─────────────────────────────────────────────────────────────
$tRows = mysqli_query($conn, "SELECT name FROM product_types ORDER BY name");
$types = [];
while ($r = mysqli_fetch_assoc($tRows)) $types[] = $r['name'];

// ── type join clause ──────────────────────────────────────────────────────────
$typeJoin = $typeFilter
    ? "JOIN product_types pt2 ON p.type_id=pt2.id AND pt2.name='$typeFilter'"
    : '';

// ── transaction history (SELECT * FROM product_journal WHERE status='sales') ──
$histSql = "
    SELECT pj.id, p.name AS product, pt.name AS category,
           pj.qty, pj.unit_price, pj.total_price,
           pj.paid, pj.`change`, pj.vat_amount, pj.status,
           pj.sold_at
    FROM product_journal pj
    JOIN products p      ON pj.product_id = p.id
    JOIN product_types pt ON p.type_id    = pt.id
    $typeJoin
    WHERE pj.status = 'sales'
      AND pj.sold_at BETWEEN '$monthStart 00:00:00' AND '$monthEnd 23:59:59'
    ORDER BY pj.sold_at DESC
    LIMIT 100";

$hRes    = mysqli_query($conn, $histSql) or err(mysqli_error($conn));
$history = [];
$overallTotal = 0;
$overallVat   = 0;
$overallPaid  = 0;

while ($r = mysqli_fetch_assoc($hRes)) {
    $history[]     = $r;
    $overallTotal += (float)$r['total_price'];
    $overallVat   += (float)$r['vat_amount'];
    $overallPaid  += (float)$r['paid'];
}

// ── product summary (grouped) ─────────────────────────────────────────────────
$sumSql = "
    SELECT p.id, p.name AS product, pt.name AS category,
           p.unit_price, p.stock,
           SUM(pj.qty)         AS total_qty,
           SUM(pj.total_price) AS total_amount,
           SUM(pj.vat_amount)  AS total_vat
    FROM product_journal pj
    JOIN products p       ON pj.product_id = p.id
    JOIN product_types pt ON p.type_id     = pt.id
    $typeJoin
    WHERE pj.status = 'sales'
      AND pj.sold_at BETWEEN '$monthStart 00:00:00' AND '$monthEnd 23:59:59'
    GROUP BY p.id, p.name, pt.name, p.unit_price, p.stock
    ORDER BY total_amount DESC";

$sRes  = mysqli_query($conn, $sumSql) or err(mysqli_error($conn));
$items = [];
$catTotals = [];

while ($r = mysqli_fetch_assoc($sRes)) {
    $cat = $r['category'];
    $items[] = $r;
    if (!isset($catTotals[$cat])) $catTotals[$cat] = ['qty'=>0,'amount'=>0.0];
    $catTotals[$cat]['qty']    += (int)$r['total_qty'];
    $catTotals[$cat]['amount'] += (float)$r['total_amount'];
}
arsort($catTotals);

// ── previous month for % change ───────────────────────────────────────────────
[$y,$m] = explode('-',$month);
$prevKey   = date('Y-m', mktime(0,0,0,(int)$m-1,1,(int)$y));
$prevStart = $prevKey.'-01';
$prevEnd   = date('Y-m-t', strtotime($prevStart));

$pr = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(total_price) AS rev, SUM(qty) AS units, SUM(vat_amount) AS vat
     FROM product_journal
     WHERE status='sales'
       AND sold_at BETWEEN '$prevStart 00:00:00' AND '$prevEnd 23:59:59'"));
$prevRev = (float)($pr['rev'] ?? 0);
$prevQty = (int)($pr['units'] ?? 0);

$totalQty = array_sum(array_column($items,'total_qty'));
$revChg   = $prevRev > 0 ? round(($overallTotal-$prevRev)/$prevRev*100,1) : 0;
$qtyChg   = $prevQty > 0 ? round(($totalQty-$prevQty)/$prevQty*100,1)    : 0;
$topCat   = $catTotals ? array_key_first($catTotals) : '—';

ok([
    'month'            => $month,
    'month_label'      => date('F Y', strtotime($monthStart)),
    'generated'        => date('F j, Y \a\t g:i A'),
    'available_months' => $months,
    'product_types'    => $types,
    'kpi' => [
        'total_revenue'   => $overallTotal,
        'total_qty'       => $totalQty,
        'total_vat'       => $overallVat,
        'total_paid'      => $overallPaid,
        'top_category'    => $topCat,
        'rev_change_pct'  => $revChg,
        'qty_change_pct'  => $qtyChg,
    ],
    'categories' => $catTotals,
    'items'      => $items,
    'history'    => $history,
    'overall_total' => $overallTotal,
    'overall_vat'   => $overallVat,
]);
